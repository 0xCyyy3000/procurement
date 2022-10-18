<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="landing_page" content="landing_page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <title>Procurement System</title>
</head>

<body>
    <div class="main_container">
        <div class="header">
            Procurement System
        </div>
        <div class="container">
            <!-- Left Container -->
            <div class="system_title">
                Centralized Procurement System
            </div>
            <div class="system_description">
                Make your procurement automated and centralized.
            </div>
            <form action="/users/authenticate" method="POST">
                @csrf
                <div class="container">
                    <div class="field">
                        <input type="email" name="email" autocomplete="off" required value="{{ old('email') }}" />
                        <label>Email Address</label>
                        @error('email')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <input type="password" name="password" autocomplete="off" required />
                        <label>Password</label>
                        @error('password')
                            <p>{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <input type="submit" value="Login" name="submit">
                    </div>
                    <div class="contact_admin">
                        No account? <a href="#">Create one</a>
                    </div>
                </div>
            </form>
            <div class="container">
                <!-- Image here -->
                <!-- Right Container -->
                <img src="{{ asset('images/clean.png') }}" alt="centralized system">
            </div>
        </div>
    </div>
</body>

</html>
