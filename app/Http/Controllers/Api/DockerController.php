<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DockerController extends Controller
{
    public function actionToContainer($action,Request $request):JsonResponse
    {
        $output = shell_exec('docker ps -a --format "{{json .}}"');
        $containers = array_map(function ($line) {
            return json_decode($line, true);
        }, explode("\n", trim($output)));
        $containerExists = collect($containers)->contains('ID', $request->containerId);
        if ($containerExists) {
            $stopOutput = shell_exec("docker {$action} {$request->containerId} 2>&1");
            if (strpos($stopOutput, 'Error') !== false) {
                return response()->json(['error' => "Failed to {$action} container", 'details' => $stopOutput], 500);
            }
            return response()->json(['message' => "Container {$action} successfully", 'details' => $stopOutput,'success' => true]);
        } else {
            return response()->json(['error' => 'Container not found','success' => false], 404);
        }
    }


    public function logs($id)
    {
        return view('containers.logs', compact( 'id'));
    }
    public function getDockerLogs($containerId):JsonResponse
    {
        $logs = shell_exec("docker logs --tail 400 {$containerId} 2>&1");
        if (strpos($logs, 'Error') !== false) {
            return response()->json(['error' => 'Failed to retrieve logs', 'details' => $logs], 500);
        }
        return response()->json(['logs' => $logs,'success' => true]);
    }



    public function stats($id)
    {
        return view('containers.stats', compact('id'));
    }

    public function statsAsync($id)
    {
        $output = shell_exec("docker stats --no-stream --format '{{json .}}' {$id}");
        $data = json_decode(trim($output), true);

        // Eğer json decode hatalıysa boş bir array döndür
        if (!$data) {
            $data = [
                'Container' => $id,
                'CPUPerc' => '0%',
                'MemUsage' => '0 / 0',
                'MemPerc' => '0%',
                'NetIO' => '0 / 0',
                'BlockIO' => '0 / 0',
                'PIDs' => '0'
            ];
        }

        return response()->json($data);
    }


    // Kullanıcının gönderdiği input'u container'e yaz
    public function terminalInput(Request $request, $id)
    {
        $input = $request->input('input');

        // Her container için bir geçici fifo dosyası kullanabiliriz
        $fifo = storage_path("docker_fifo_$id");
        if(!file_exists($fifo)) {
            posix_mkfifo($fifo, 0600);
        }

        file_put_contents($fifo, $input);
        return response()->json(['status'=>'ok']);
    }

    public function terminalStream($id)
    {
        $response = response()->stream(function() use ($id) {
            $fifo = storage_path("docker_fifo_$id");
            if(!file_exists($fifo)) {
                posix_mkfifo($fifo, 0600);
            }

            $process = proc_open(
                "docker exec -i $id sh",
                [
                    0 => ["pipe","r"],
                    1 => ["pipe","w"],
                    2 => ["pipe","w"]
                ],
                $pipes
            );

            stream_set_blocking($pipes[1], false);
            stream_set_blocking($pipes[2], false);

            while(true) {
                // Container stdout
                $out = stream_get_contents($pipes[1]);
                $err = stream_get_contents($pipes[2]);

                if($out) echo "data: $out\n\n";
                if($err) echo "data: $err\n\n";
                ob_flush(); flush();

                // Kullanıcı input
                if(file_exists($fifo)) {
                    $input = file_get_contents($fifo);
                    if($input) {
                        fwrite($pipes[0], $input);
                        fflush($pipes[0]);
                        file_put_contents($fifo, '');
                    }
                }

                usleep(100000); // 0.1s
            }

            fclose($pipes[0]); fclose($pipes[1]); fclose($pipes[2]);
            proc_close($process);
        }, 200, [
            "Content-Type" => "text/event-stream",
            "Cache-Control" => "no-cache",
            "Connection" => "keep-alive",
        ]);

        return $response;
    }

    public function terminal($containerId)
    {
        return view('containers.ssh', compact('containerId'));
    }
}
