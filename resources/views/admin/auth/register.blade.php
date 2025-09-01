<!DOCTYPE html>
<html>
<head>
    <title>Admin Register</title>
</head>
<body>
    <h2>Register Admin</h2>
    <form method="POST" action="{{ url('/admin/register') }}">
        @csrf
        <input type="text" name="name" placeholder="Nama" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" required><br>
        <button type="submit">Register</button>
    </form>
    <a href="{{ route('admin.login') }}">Sudah punya akun? Login</a>
</body>
</html>
