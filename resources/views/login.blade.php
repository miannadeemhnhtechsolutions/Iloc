<!-- resources/views/auth/login.blade.php -->

@if(session('error'))
    <div>{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <input type="text" name="email" placeholder="Username">
    <input type="password" name="password" placeholder="Password">
    <button type="submit">Login</button>
</form>
