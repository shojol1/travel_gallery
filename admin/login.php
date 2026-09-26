<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    header("Location: /travel-memories/admin/index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email']= $user['email'];
            $_SESSION['user_role'] = $user['role'];

            set_flash('success', 'Welcome back, ' . $user['name'] . '!');
            header("Location: /travel-memories/admin/index.php");
            exit;
        } else {
            $error = "Invalid email address or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Travel Memories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e2229 0%, #121416 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .login-header {
            background: #2d5a4c;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <h3 class="fw-bold mb-1"><i class="fas fa-compass me-2"></i> Travel Admin</h3>
        <p class="small text-white-50 mb-0">Sign in to manage your travel memories</p>
    </div>
    
    <div class="p-4 p-md-5">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small mb-3"><?= e($error); ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control bg-light border-start-0" placeholder="admin@example.com" value="admin@example.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-key text-muted"></i></span>
                    <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="••••••••" value="admin123" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100 py-2 fw-semibold shadow-sm" style="background-color: #2d5a4c; border: none;">
                <i class="fas fa-sign-in-alt me-2"></i> Sign In to Dashboard
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="/travel-memories/index.php" class="small text-muted text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Return to Public Website
            </a>
        </div>
    </div>
</div>

</body>
</html>
