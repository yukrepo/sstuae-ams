@extends('layouts.app')

@section('content')
<div class="container">
    <div class="text-center">
        <a href="/" class="servicelogo"><img src="{{ asset('images/logo.png') }}" alt="Sri Sri Tattva"></a>
        <div class="loginbox">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group border-bottom text-left pb-2 mb-2">
                    <h5>Please Login to Continue</h5>
                </div>
                <div class="form-group  text-left">
                    <label for="username" class="control-label">
                        <i class="bi bi-person-fill"></i>
                        <small>Username</small>
                    </label>
                    <input id="username" type="text" class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('email') }}" required autofocus  placeholder="enter ID" autocomplete="your employee ID or username">
                    @if ($errors->has('username'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('username') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group  text-left">
                    <label for="username" class="control-label">
                        <i class="bi bi-lock-fill"></i> 
                        <small>Password</small>
                    </label>
                    <input id="password" type="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required autocomplete="your current password"  placeholder="enter password">
                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-success btn-block">
                            {{ __('Login') }}
                        </button>
                    </div>
                    <div class="col-md-6">
                        <div class="text-left mt-2">
                            <input class="checkposition" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="form-check-label" for="remember">
                                {{ __('Remember Me') }}
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
