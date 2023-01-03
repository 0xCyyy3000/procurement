@extends('layouts.app')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <form method="POST" class="form-floating" action="{{ route('login') }}">
                    @csrf
                    <div class="card rounded-4 m-auto">
                        <div class="card-body p-5">
                            <div class="row mb-3 align-items-center">
                                <img class="w-25 m-auto" src="{{ asset('images/aclc tacloban.png') }}" alt="">
                            </div>
                            <div class="row mb-3">
                                <label for="email" class=" form-label text-md-left p-0">{{ __('Email Address') }}</label>
                                <input id="email" type="email"
                                    class="m-auto form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" placeholder="example@email.com" required
                                    autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="row">
                                <label for="password" class=" form-label text-md-left p-0">{{ __('Password') }}</label>
                                <input id="password" type="password"
                                    class="m-auto form-control @error('password') is-invalid @enderror" name="password"
                                    value="{{ old('password') }}" placeholder="password" required autofocus>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row m-auto w-75 p-3 pt-0 pb-5">
                            <button type="submit" class="btn text-white w-100 m-auto"
                                style="background-color: #7380ec !important;">
                                Login
                            </button>
                        </div>
                        {{-- <div class="row">
                        <div class="col-md-8 offset-md-4">
                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                        </div>
                    </div> --}}
                </form>
            </div>
        </div>
    </div>
    </div>
    </div>
@endsection
