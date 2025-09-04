<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $sites = Site::withCount(['getBackups'])
            ->when($request->filled('domain'), function ($query) use ($request) {
                $query->where('domain', 'like', '%' . $request->domain . '%');
            })
            ->when($request->filled('enabled'), function ($query) use ($request) {
                $query->where('enabled', $request->enabled);
            })
            ->when($request->filled('backup_status'), function ($query) use ($request) {
                $query->where('backup_status', $request->backup_status);
            })
            ->when($request->filled('ssl_status'), function ($query) use ($request) {
                $query->where('ssl_status', $request->ssl_status);
            })
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 10);
        return view('sites.index', compact('sites'));
    }

    public function create()
    {
        return view('sites.create');
    }

    public function edit($id)
    {
        $site = Site::findOrFail($id);
        return view('sites.edit', compact('site'));
    }

    public function show($id)
    {
        $site = Site::withCount(['getBackups'])->findOrFail($id);
        return view('sites.show', compact('site'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255',
            'enabled' => 'required|boolean',
            'working_directory' => 'required|string|max:255',
            'backup_status' => 'required|boolean',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer',
            'ssl_status' => 'required|boolean',
        ]);

        Site::create($request->all());

        return redirect()->route('sites.index')->with('success', 'Site created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'domain' => 'required|string|max:255',
            'enabled' => 'required|boolean',
            'working_directory' => 'required|string|max:255',
            'backup_status' => 'required|boolean',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer',
            'ssl_status' => 'required|boolean',
        ]);

        $site = Site::findOrFail($id);
        $site->update($request->all());

        return redirect()->route('sites.index')->with('success', 'Site updated successfully.');
    }

    public function destroy($id)
    {
        $site = Site::findOrFail($id);
        $site->delete();

        return redirect()->route('sites.index')->with('success', 'Site deleted successfully.');
    }

}
