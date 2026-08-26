<?php
/**
 * STAR FAIR - Initial Setup Script (First Admin Creation)
 * This script will delete itself after successfully creating the first admin.
 */

require_once __DIR__ . '/../database.php';

// 1. Check if any admin already exists
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM `admins`");
    $adminCount = $stmt->fetchColumn();
    
    if ($adminCount > 0) {
        http_response_code(403);
        die("<h3>Access Denied:</h3> An admin account already exists. For security reasons, this setup script has been disabled.");
    }
} catch (PDOException $e) {
    die("Database error during lookup: " . htmlspecialchars($e->getMessage()));
}

$message = '';
$success = false;

// 2. Handle admin creation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (!$email) {
        $message = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        // Hash password securely
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO `admins` (email, password_hash) VALUES (?, ?)");
            $stmt->execute([$email, $hash]);
            
            $success = true;
            $message = "Initial admin account created successfully! The script has attempted to self-delete. **IMPORTANT**: Please verify your server files and manually delete `backend/admin/create-admin.php` if it remains to protect your database.";
            
            // Try to self-destruct
            @unlink(__FILE__);
        } catch (PDOException $e) {
            $message = "Failed to create account: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STAR FAIR | Admin Initial Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0e0e0e; color: #fff; font-family: sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .setup-card { background-color: #161616; border: 1px solid #D4AF37; border-radius: 12px; padding: 30px; width: 100%; max-width: 450px; box-shadow: 0 4px 15px rgba(212,175,55,0.15); }
        .text-gold { color: #D4AF37; }
        .btn-gold { background-color: #D4AF37; color: #000; font-weight: bold; border: none; }
        .btn-gold:hover { background-color: #bfa030; color: #000; }
    </style>
</head>
<body>

<div class="setup-card">
    <h3 class="text-center text-gold mb-3">Star Fair Admin Setup</h3>
    <p class="text-muted text-center small mb-4">Create the initial administrator account. This script self-destructs after success.</p>
    
    <?php if ($message): ?>
        <div class="alert alert-<?= $success ? 'success' : 'danger' ?> small" role="alert">
            <?= $message // Keep raw HTML styling for markups ?>
        </div>
    <?php endif; ?>

    <?php if (!$success): ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label text-muted small">Admin Email Address</label>
                <input type="email" name="email" class="form-control bg-dark text-white border-secondary" id="email" required placeholder="admin@starfairbd.com">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label text-muted small">Secure Password</label>
                <input type="password" name="password" class="form-control bg-dark text-white border-secondary" id="password" required placeholder="Min 8 characters">
            </div>
            <div class="mb-4">
                <label for="confirm_password" class="form-label text-muted small">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control bg-dark text-white border-secondary" id="confirm_password" required placeholder="Re-type password">
            </div>
            <button type="submit" class="btn btn-gold w-100 py-2">Create Admin Account</button>
        </form>
    <?php else: ?>
        <div class="text-center mt-4">
            <a href="../../admin/index.php" class="btn btn-gold px-4">Go to Admin Login</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
