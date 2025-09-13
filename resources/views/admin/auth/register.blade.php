<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --bg-light: #f8f9fa;
            --bg-gradient: linear-gradient(135deg, #e0f7fa, #c2e9fb);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        .container {
            display: flex;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 90%;
            max-width: 1000px;
        }

        .left-panel {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: rgba(255, 255, 255, 0.7);
            position: relative;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-gradient);
            z-index: -1;
            clip-path: polygon(0 0, 100% 0, 75% 100%, 0% 100%);
            opacity: 0.8;
        }

        .logo img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        .right-panel {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
        }

        .right-panel h2 {
            font-size: 2em;
            margin-bottom: 40px;
            font-weight: 600;
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-group .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
        }

        .register-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background-color: var(--primary-color);
            color: #fff;
            font-size: 1.1em;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
        }

        .register-btn:hover {
            background-color: #0056b3;
        }

        .register-btn:active {
            transform: scale(0.99);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9em;
            color: #555;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                width: 95%;
            }

            .left-panel, .right-panel {
                padding: 40px 20px;
            }

            .left-panel::before {
                clip-path: polygon(0 0, 100% 0, 100% 75%, 0% 100%);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <div class="logo">
                <img src="{{ asset('images/logo (2).png') }}" alt="Andri Hotel Logo">
            </div>
        </div>
        <div class="right-panel">
            <h2>Register</h2>
            <form method="POST" action="{{ url('/admin/register') }}">
                @csrf
                <div class="form-group">
                    <input type="text" name="name" placeholder="Nama" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <span class="password-toggle" onclick="togglePasswordVisibility('password')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                </div>
                <div class="form-group">
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Konfirmasi Password" required>
                    <span class="password-toggle" onclick="togglePasswordVisibility('password_confirmation')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                </div>
                <button type="submit" class="register-btn">Register</button>
            </form>
            <div class="login-link">
                <p>Sudah punya akun? <a href="{{ route('admin.login') }}">Login</a></p>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(id) {
            const passwordInput = document.getElementById(id);
            const passwordToggle = passwordInput.nextElementSibling.querySelector('svg');
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                passwordToggle.innerHTML = '<path d="M17.94 17.94A10.74 10.74 0 0 1 12 20c-7 0-11-8-11-8a18.23 18.23 0 0 1 5-5m2-2l2-2m5-5l2 2m-1-1l-2-2m-2-2l-2-2m-2-2l-2-2z"></path><line x1="1" y1="1" x2="23" y2="23"></line><circle cx="12" cy="12" r="3"></circle>';
            } else {
                passwordInput.type = "password";
                passwordToggle.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }
    </script>
</body>
</html>