<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><h4>Register</h4></div>
                    <div class="card-body">
                        <form id="register-form">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" required>
                            </div>
                             <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>
                        <div id="error-message" class="mt-3 text-danger"></div>
                         <div class="mt-3">
                            <p>Already have an account? <a href="/login">Login here</a></p>
                        </div>
                    </div>
                </div>
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
                    // Handle validation errors
                    if (response.status === 422) {
                        let errors = Object.values(data.errors).join(' ');
                        errorMessage.textContent = errors;
                    } else {
                        errorMessage.textContent = data.message || 'Registration failed.';
                    }
                    return;
                }

                // On success
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
