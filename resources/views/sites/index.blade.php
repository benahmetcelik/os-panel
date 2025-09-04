@extends('layouts.app')

@section('content')
    <div class="card-area">
        <div class="card-header">
            <h4 class="card-title">Sites</h4>
            <a href="{{ route('sites.create') }}" class="btn btn-success">Add New Site</a>
        </div>
        <div class="table-filters">
            <form method="GET" action="{{ route('sites.index') }}" class="form form-inline">
                <label for="domain_filter">Domain : </label>
                <input type="text" name="domain" id="domain_filter" class="form-control" placeholder="Search by domain"
                       value="{{ request('domain') }}">



                <label for="ssl_status_switch">SSL</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="ssl_status_switch"
                        data-for="ssl_status"
                           value="1" {{ request('ssl_status') === '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="ssl_status_switch"></label>
                    <input type="hidden"  name="ssl_status" value="{{ request('ssl_status') }}" id="ssl_status_hidden">
                </div>

                <label for="backup_status_switch">Backup</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="backup_status_switch"
                           data-for="backup_status"
                           value="1" {{ request('backup_status') === '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="backup_status_switch"></label>

                    <input type="hidden" name="backup_status" value="{{ request('backup_status') }}" id="backup_status_hidden">
                </div>

                <label for="enabled_status_switch">Status</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="enabled_status_switch"
                           data-for="enabled_status"
                           value="1" {{ request('enabled_status') === '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="enabled_status_switch"></label>

                    <input type="hidden" name="enabled_status" value="{{ request('enabled_status') }}" id="enabled_status_hidden">
                </div>



                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>URL</th>
                        <th>Files</th>
                        <th>Status</th>
                        <th>SSL</th>
                        <th>Backup</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($sites as $site)
                        <tr>
                            <td><a href="http{{ $site->ssl_status ? 's' : '' }}://{{ $site->domain }} "
                                   target="_blank">{{ $site->domain }} ↗️</a></td>
                            <td><a href="{{ $site->working_directory }}" class="btn btn-info btn-small" target="_blank"><i
                                        class="fa fa-folder-open"></i></a></td>
                            <td><span class="btn btn-{{ $site->enabled ? 'success' : 'warning' }} text-white btn-small"><i
                                        class="fa fa-{{ $site->enabled ? 'play' : 'pause' }}"></i></span></td>
                            <td><span
                                    class="btn btn-{{ $site->ssl_status ? 'success' : 'warning' }} text-white btn-small"><i
                                        class="fa fa-{{ $site->ssl_status ? 'lock' : 'unlock' }}"></i></span></td>
                            <td><span
                                    class="btn btn-{{ $site->backup_status ? 'success' : 'warning' }} text-white btn-small"><i
                                        class="fa fa-floppy-disk"></i> ({{ $site->get_backups_count }})</span></td>
                            <td>
                                <div class="grid">
                                    <a href="{{ route('sites.edit', $site->id) }}" class="btn btn-primary btn-small"><i
                                            class="fa fa-pen"></i></a>
                                    <form action="{{ route('sites.destroy', $site->id) }}" method="POST"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-small"
                                                onclick="return confirm('Are you sure you want to delete this site?')">
                                            <i class="fa fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $sites->links() }}
            </div>
        </div>
    </div>
@endsection


@push('scripts')

@endpush
