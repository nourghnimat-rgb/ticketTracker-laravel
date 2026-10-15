<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
        <form method="POST" action="/logout">
    @csrf
    <button type="submit">Logout</button>
    @can('create', App\Models\User::class)
    <a href="{{ route('users.create') }}">Create User</a>
@endcan
</form>
    </body>
</html>
