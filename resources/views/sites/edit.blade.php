@extends('layouts.app')

@section('content')
    <div class="card-area">
        <div class="card-header">
            <h4 class="card-title">Site Oluştur</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('sites.update',$site->id) }}" class="form">
                @method('PUT')
                @csrf
                @if($errors->any())

                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                @endif
                <label for="domain_input">Domain : </label>
                <input type="text" name="domain" id="domain_input" class="form-control" placeholder="example.com"
                       value="{{ $site->domain ?? old('domain') }}">

                {{--          TODO: Filemanager popup eklenecek      --}}
                <label for="working_directory">Volume : </label>
                <input type="text" name="working_directory" id="working_directory" class="form-control" placeholder="/var/www/html"
                       value="{{ $site->working_directory ?? old('working_directory') }}">

                <label for="ip_input">IP : </label>
                <input type="text" name="ip_address" id="ip_input" class="form-control" placeholder="127.0.0.1"
                       value="{{ $site->ip_address ?? old('ip_address') }}">


                <label for="port_input">Port : </label>
                <input type="number" name="port" id="port_input" class="form-control" placeholder="80"
                       value="{{ $site->port ?? old('port',80) }}">

                <div class="grid">
                    <label for="ssl_status_switch">SSL</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ssl_status_switch"
                               data-for="ssl_status"
                               value="{{ $site->ssl_status ?? old('ssl_status',0) }}" {{ ($site->ssl_status ?? old('ssl_status',0)) == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="ssl_status_switch"></label>
                        <input type="hidden" name="ssl_status" value="{{ $site->ssl_status ?? old('ssl_status',0) }}"
                               id="ssl_status_hidden">
                    </div>

                    <label for="backup_status_switch">Backup</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="backup_status_switch"
                               data-for="backup_status"
                               value="{{ $site->backup_status ?? old('backup_status',0) }}" {{ ($site->backup_status ?? old('backup_status',0)) == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="backup_status_switch"></label>
                        <input type="hidden" name="backup_status" value="{{ $site->backup_status ?? old('backup_status',0) }}"
                               id="backup_status_hidden">
                    </div>

                    <label for="enabled_status_switch">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="enabled_status_switch"
                               data-for="enabled"
                               value="{{ $site->enabled ?? old('enabled',0) }}" {{ ($site->enabled ?? old('enabled')) == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="enabled_status_switch"></label>
                        <input type="hidden" name="enabled" value="{{ $site->enabled ?? old('enabled',0) }}"
                               id="enabled_status_hidden">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Kaydet</button>
            </form>
        </div>
    </div>
@endsection


@push('styles')

@endpush

@push('scripts')

@endpush
