<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sri Sri Tattva || HMS') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,500;0,600&display=swap"
    rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('svg/connection.svg') }}" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script type="text/javascript">
        const FURL = 'https://fb.sstapp.me';
    </script>
</head>
<body>
    <div>
        @include('elements.menu')
        <main id="app">
            <vue-progress-bar></vue-progress-bar>
            @yield('content')
            @if((in_array(Auth::user()->admin_type_id, [1,4,5,6,9])) && (!in_array(Route::current()->parameter('slug'), ['arequest'])))
                <appointment-creater-component></appointment-creater-component>
            @endif
        </main>
        @include('elements.logoutbox')
        @include('elements.sidebar')
    </div>
    @if(Auth::user()->admin_type_id == 1)
        <div class="modal fade" id="switchModal" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="/switch-admin" class="m-0" method="post">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="switchModalModalTitle">Switch Location</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="location" class="control-label">Location</label>
                                        <select name="location" id="location" class="form-control">
                                            @foreach ($locations as $location)
                                                @if($location->id != Auth::user()->location_id)
                                                    <option value="{{ $location->id }}">{{$location->clinic_name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-wide btn-danger" data-dismiss="modal"> Cancel </button>
                            <button type="submit" class="btn btn-wide btn-success"> Switch </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    <script src="{{ asset('js/manifest.js') }}" defer></script>
    <script src="{{ asset('js/vendor.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
