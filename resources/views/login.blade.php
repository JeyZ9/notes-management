<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyNotes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,.1);
        }
        .login-card .card-header {
            background-color: #fff;
            border-bottom: none;
            text-align: center;
            padding-top: 2rem;
            padding-bottom: 1rem;
        }
        .login-card .card-header h3 {
            font-weight: bold;
        }
        .login-card .card-body {
            padding: 2rem;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #86b7fe;
        }
    </style>
</head>
<body>
    <div class="card login-card">
        <div class="card-header">
            <h3><i class="fas fa-book-open"></i> MyNotes Login</h3>
            <p class="text-muted">Welcome back! Please login to your account.</p>
        </div>
        <div class="card-body">
            <form id="login-form">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                </div>
            </form>
            <div id="error-message" class="mt-3 text-danger text-center"></div>
            <div class="mt-4 text-center">
                <p>Don't have an account? <a href="/register">Register here</a></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorMessage = document.getElementById('error-message');
            errorMessage.textContent = '';

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (!response.ok) {
                    errorMessage.textContent = data.message || 'Login failed. Please check your credentials.';
                    return;
                }

                if (data.access_token) {
                    localStorage.setItem('api_token', data.access_token);
                    window.location.href = '/notes';
                } else {
                     errorMessage.textContent = 'Login failed: No token received.';
                }

            } catch (error) {
                console.error('Error during login:', error);
                errorMessage.textContent = 'An error occurred. Please try again.';
            }
        });
    </script>
</body>
</html>