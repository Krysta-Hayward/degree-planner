<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, inital-scale=1.0">
    <title>Account Login - Degree Planner</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/theme.js') }}"></script>
</head>
<header class="site-header">
    @include('partials.app-logo')
    <button class="button-links theme-toggle" type="button" aria-pressed="false">Dark Mode</button>
</header>
<body>
    <h1>Account Login</h1>

    @if ($errors->any())
        <p>{{  $errors->first() }}</p>
    @endif

    <form method="POST" action="{{  route('login') }}" class="form-group">
        @csrf

        <div class="login-page">
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="button-links">Log In</button>
        </div>
    </form>

    <a href="/" class="button-links">Back to Home</a>
</body>

</html>