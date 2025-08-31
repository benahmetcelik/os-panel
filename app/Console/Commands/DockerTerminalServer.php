<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class DockerTerminalServer extends Command implements MessageComponentInterface
{
    protected $signature = 'docker:terminal-server';
    protected $description = 'Start Docker WebSocket Terminal Server';

    protected $clients;
    protected $connectionContainerMap = [];
    protected $workingDirectories = [];

    public function __construct()
    {
        parent::__construct();
        $this->clients = new \SplObjectStorage;
    }

    public function handle()
    {
        $this->info("Starting Docker Terminal WebSocket Server...");
        $this->info("Server will listen on ws://0.0.0.0:8080");

        $server = IoServer::factory(
            new HttpServer(new WsServer($this)),
            8080,
            '0.0.0.0'
        );

        $this->info("Docker Terminal WebSocket Server started successfully!");
        $this->info("Waiting for connections...");

        $server->run();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        $this->info("New connection: " . $conn->resourceId);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);
        if (!isset($data['containerId'], $data['input'])) {
            $this->error("Invalid message format: " . $msg);
            return;
        }

        $containerId = $data['containerId'];
        $input = trim($data['input']);
        $this->info("Received input for container $containerId: " . json_encode($input));

        if (!isset($this->connectionContainerMap[$from->resourceId])) {
            $this->connectionContainerMap[$from->resourceId] = $containerId;
            $this->info("Mapped connection {$from->resourceId} to container $containerId");

            // İlk bağlantıda working directory'yi al
            if (!isset($this->workingDirectories[$containerId])) {
                $this->workingDirectories[$containerId] = '/';
            }
        }


        $containerStatus = shell_exec("docker ps --filter id=$containerId --format '{{.Status}}' 2>/dev/null");
        if (empty(trim($containerStatus))) {
            $this->error("Container $containerId is not running or does not exist");
            $from->send(json_encode(['error' => "Container $containerId is not running or does not exist"]));
            return;
        }


        if (preg_match('/^cd\s+(.+)$/', $input, $matches)) {
            $newPath = trim($matches[1]);


            if (!str_starts_with($newPath, '/')) {
                $newPath = rtrim($this->workingDirectories[$containerId], '/') . '/' . $newPath;
            }


            $newPath = realpath($newPath) ?: $newPath;


            $testCommand = "docker exec $containerId sh -c 'cd " . escapeshellarg($newPath) . " && pwd' 2>/dev/null";
            $testOutput = shell_exec($testCommand);

            if ($testOutput && trim($testOutput)) {
                $this->workingDirectories[$containerId] = trim($testOutput);
                $this->info("Changed working directory to: " . $this->workingDirectories[$containerId]);
                $from->send(json_encode(['type' => 'directory', 'data' => $this->workingDirectories[$containerId]]));
            } else {
                $this->error("Invalid directory: $newPath");
                $from->send(json_encode(['error' => "Invalid directory: $newPath"]));
            }
            return;
        }


        $this->info("Executing command in container $containerId: " . $input);


        $shellCommand = $this->getAvailableShell($containerId);


        $workingDir = $this->workingDirectories[$containerId] ?? '/';
        $dockerCommand = "docker exec -w " . escapeshellarg($workingDir) . " $containerId $shellCommand -c " . escapeshellarg($input);

        $this->info("Full command: " . $dockerCommand);


        $output = shell_exec($dockerCommand . " 2>&1");

        if ($output === null) {
            $this->error("Failed to execute command in container $containerId");
            $from->send(json_encode(['error' => 'Failed to execute command in container']));
            return;
        }


        $output = rtrim($output, "\n\r");

        $this->info("Command output: " . json_encode($output));


        $response = json_encode(['type' => 'output', 'data' => $output]);
        $this->info("Sending to frontend: " . $response);
        $from->send($response);
    }

    private function getAvailableShell($containerId)
    {
        // Burada bash mi yoksa sh mi olması gerektiğini tespit eden bir yapı kurmayı planlıyorum
        //TODO: bash mi yoksa sh mi olması gerektiğini bulan bir yapı kur
        $bashTest = shell_exec("docker exec $containerId which bash 2>/dev/null");
        if (!empty(trim($bashTest))) {
            return 'bash';
        }

        // Bash yoksa sh'i dene
        $shTest = shell_exec("docker exec $containerId which sh 2>/dev/null");
        if (!empty(trim($shTest))) {
            return 'sh';
        }

        // Hiçbiri yoksa /bin/sh'i zorla (sh hepsine yer diye :D )
        return '/bin/sh';
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        $this->info("Connection closed: " . $conn->resourceId);

        if (isset($this->connectionContainerMap[$conn->resourceId])) {
            $containerId = $this->connectionContainerMap[$conn->resourceId];
            unset($this->connectionContainerMap[$conn->resourceId]);
            $this->info("Unmapped connection from container: " . $containerId);
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->error("An error occurred: " . $e->getMessage());
        $conn->close();
    }
}
