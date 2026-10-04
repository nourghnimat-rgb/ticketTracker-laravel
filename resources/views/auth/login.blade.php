<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div>
        <form method="POST" action="/login">
            @csrf

            <label for="email">Email</label>
            <input id="email" type="email" name="email" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
            @error('email')
            <p>{{ $message }}</p>
            @enderror

            <button type="submit">Login</button>
        </form>




    </div>
</body>

</html>