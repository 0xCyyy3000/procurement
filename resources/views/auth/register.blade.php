@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="card rounded-4 m-auto">
                        <div class="card-body p-5">
                            <div class="row mb-3">
                                <h2 class="text-center fw-bold">Regsitration Form</h2>
                            </div>
                            <div class="row mb-3">
                                <label for="name" class=" form-label text-md-left p-0">{{ __('Name') }}</label>
                                <input id="name" type="name"
                                    class="m-auto form-control @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name') }}" placeholder="Your name" required autocomplete="name"
                                    autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
                            <div class="row mb-3">
                                <label for="email" class=" form-label text-md-left p-0">{{ __('Department') }}</label>
                                <select name="department" class="form-select @error('department') is-invalid @enderror">
                                    <option value="">Choose one...</option>
                                    @foreach ($departments as $department)
                                        @if ($department->id > 3)
                                            <option value="{{ $department->id }}">{{ $department->department }}</option>
                                        @endif
                                    @endforeach
                                </select>

                                @error('department')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="row mb-3">
                                <label for="password" class=" form-label text-md-left p-0">{{ __('Password') }}</label>
                                <input id="password" type="password"
                                    class="m-auto form-control @error('password') is-invalid @enderror" name="password"
                                    value="{{ old('password') }}" placeholder="Your password" required autofocus>

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="row">
                                <label for="password_confirmation"
                                    class=" form-label text-md-left p-0">{{ __('Confirm Password') }}</label>
                                <input id="password_confirmation" type="password"
                                    class="m-auto form-control @error('password_confirmation') is-invalid @enderror"
                                    name="password_confirmation" placeholder="Re-type password" required autofocus>

                                @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row m-auto w-75 p-3 pt-0 pb-5">
                            <button type="submit" class="btn text-white w-100 m-auto"
                                style="background-color: #7380ec !important;">
                                Register
                            </button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection
