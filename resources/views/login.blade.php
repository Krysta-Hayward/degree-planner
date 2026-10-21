<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, inital-scale=1.0">
    <title>Admin Login - Degree Planner</title>
</head>

<body>
    <h1>Login</h1>

    @if ($errors->any())
        <p>{{  $errors->first() }}</p>
    @endif

    <form method="POST" action="{{  route('login') }}">
        @csrf

        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Log In</button>
    </form>

    <p><a href="/">Back to Home</a></p>
</body>

</html>