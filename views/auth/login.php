<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LeadFlow CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/style.css" rel="stylesheet">
</head>
<body>
<div class="login-page">
    <div class="login-container">
        <div class="login-left">
            <div class="login-brand">
                <i class="fas fa-bolt"></i>
                <h1>LeadFlow <span>CRM</span></h1>
            </div>
            <div class="login-hero">
                <img src="https://illustrations.popsy.co/amber/man-riding-a-rocket.svg" alt="Hero" class="login-illustration">
                <h2>Manage Your Leads Efficiently</h2>
                <p>Import, track, assign, and analyze leads with our powerful CRM dashboard.</p>
            </div>
            <div class="login-features">
                <div class="login-feature"><i class="fas fa-check-circle"></i> Excel Import</div>
                <div class="login-feature"><i class="fas fa-check-circle"></i> Team Management</div>
                <div class="login-feature"><i class="fas fa-check-circle"></i> Analytics</div>
                <div class="login-feature"><i class="fas fa-check-circle"></i> Lead Assignment</div>
            </div>
        </div>
        <div class="login-right">
            <div class="login-form-wrapper">
                <h3>Welcome Back</h3>
                <p class="text-muted mb-4">Sign in to your account</p>
                <?php if ($error): ?>
                    <div class="alert alert-danger py-2"><i class="fas fa-exclamation-circle me-1"></i><?= e($error) ?></div>
                <?php endif; ?>
                <form method="POST" action="<?= BASE_URL ?>/login">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" name="email" id="loginEmail" placeholder="admin@leadflow.com" value="admin@leadflow.com" required>
                        <label><i class="fas fa-envelope me-2"></i>Email Address</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="password" class="form-control" name="password" id="loginPassword" placeholder="Password" required>
                        <label><i class="fas fa-lock me-2"></i>Password</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg login-btn">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>
                <div class="login-credentials mt-4">
                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Default: admin@leadflow.com / admin123</small>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
