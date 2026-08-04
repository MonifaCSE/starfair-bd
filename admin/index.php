<?php
require_once __DIR__ . '/../backend/config.php';

// Redirect to dashboard if session is already active
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STAR FAIR | Admin Login</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/logo/favicon.ico?v=2">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/logo/favicon.ico?v=2">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="css/admin.css">
</head>

<body class="admin-login-body">

    <div class="login-container">
        <div class="login-card">
            <div class="text-center mb-4">
                <img src="../assets/images/logo/logo.jpg" alt="Star Fair Logo" class="login-logo mb-3">
                <h3 class="text-gold">Admin Portal</h3>
                <p class="text-muted small">Sign in to manage registrations & magazines</p>
            </div>

            <form id="loginForm">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" required placeholder="admin@starfairbd.com">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" required placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" id="loginBtn" class="btn btn-gold w-100 py-2.5">
                    Sign In
                </button>
            </form>
        </div>
    </div>

    <!-- Admin JS Handler -->
    <script src="js/admin.js"></script>
</body>

</html>
