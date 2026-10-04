<h1>Create User</h1>

@if (session('success'))
    <p>{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('users.store') }}">
    @csrf

    <label for="name">Name</label>
    <input id="name" name="name" value="{{ old('name') }}" required>
    @error('name') <p>{{ $message }}</p> @enderror

    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
    @error('email') <p>{{ $message }}</p> @enderror

    <label for="password">Password</label>
    <input id="password" type="password" name="password" required>
    @error('password') <p>{{ $message }}</p> @enderror

    <label for="password_confirmation">Confirm Password</label>
    <input id="password_confirmation" type="password" name="password_confirmation" required>

    <label for="role">Role</label>
    <select id="role" name="role" required>
        <option value="user" @selected(old('role') === 'user')>User</option>
        <option value="manager" @selected(old('role') === 'manager')>Manager</option>
        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
    </select>
    @error('role') <p>{{ $message }}</p> @enderror

    <button type="submit">Create User</button>
</form>