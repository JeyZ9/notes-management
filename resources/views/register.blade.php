<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MyNotes</title>
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
        .register-card {
            width: 100%;
            max-width: 400px;
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,.1);
        }
        .register-card .card-header {
            background-color: #fff;
            border-bottom: none;
            text-align: center;
            padding-top: 2rem;
            padding-bottom: 1rem;
        }
        .register-card .card-header h3 {
            font-weight: bold;
        }
        .register-card .card-body {
            padding: 2rem;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #86b7fe;
        }
    </style>
</head>
<body>
    <div class="card register-card">
        <div class="card-header">
            <h3><i class="fas fa-user-plus"></i> Create Account</h3>
            <p class="text-muted">Join MyNotes to start creating your notes.</p>
        </div>
        <div class="card-body">
            <form id="register-form">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Your Name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" placeholder="you@example.com" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Create a password" required>
                </div>
                 <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password_confirmation" placeholder="Confirm your password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>
            <div id="error-message" class="mt-3 text-danger text-center"></div>
            <div class="mt-4 text-center">
                <p>Already have an account? <a href="/login">Login here</a></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('register-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            const errorMessage = document.getElementById('error-message');
            errorMessage.textContent = '';

            if (password !== password_confirmation) {
                errorMessage.textContent = 'Passwords do not match.';
                return;
            }

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ name, email, password, password_confirmation })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422) {
                        let errors = Object.values(data.errors).map(err => err.join(' ')).join(' ');
                        errorMessage.textContent = errors;
                    } else {
                        errorMessage.textContent = data.message || 'Registration failed.';
                    }
                    return;
                }

                alert('Registration successful! Please log in.');
                window.location.href = '/login';

            } catch (error) {
                console.error('Error during registration:', error);
                errorMessage.textContent = 'An error occurred. Please try again.';
            }
        });
    </script>
</body>
</html>