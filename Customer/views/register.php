<?php
$pageTitle = 'Create Account — EzyCommerce';
$pageDescription = 'Create your EzyCommerce account and start shopping.';
require_once __DIR__ . "/components/header.php";
?>

<style>
    .register-section {
        max-width: 560px; margin: 48px auto 80px; padding: 0 24px;
    }
    .register-card {
        background: #fff; border-radius: 20px; padding: 40px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04); border: 1px solid #E8ECF1;
    }
    .register-card h1 {
        font-size: 26px; font-weight: 800; color: #2D3436;
        margin-bottom: 8px; letter-spacing: -0.3px;
    }
    .register-card .subtitle { color: #636E72; font-size: 15px; margin-bottom: 32px; }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px; color: #2D3436; }
    .form-group input, .form-group textarea {
        width: 100%; padding: 13px 16px; border-radius: 10px; border: 1.5px solid #E8ECF1;
        background: #F7F8FC; font-size: 15px; font-family: 'Inter', sans-serif;
        color: #2D3436; outline: none; transition: all 0.2s;
    }
    .form-group input:focus, .form-group textarea:focus {
        border-color: #6C5CE7; background: #fff; box-shadow: 0 0 0 4px rgba(108,92,231,0.1);
    }
    .form-group textarea { min-height: 100px; resize: vertical; }
    
    .submit-btn {
        width: 100%; padding: 15px; background: linear-gradient(135deg, #6C5CE7, #5A4BD1);
        color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 700;
        cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.25s;
        box-shadow: 0 4px 15px rgba(108,92,231,0.3); display: flex; align-items: center;
        justify-content: center; gap: 8px; margin-top: 8px;
    }
    .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(108,92,231,0.4); }
    
    .form-footer { text-align: center; margin-top: 24px; color: #636E72; font-size: 14px; }
    .form-footer a { color: #6C5CE7; font-weight: 600; text-decoration: none; }
    .form-footer a:hover { text-decoration: underline; }
    
    @media(max-width: 480px) {
        .register-card { padding: 24px; }
        .register-card h1 { font-size: 22px; }
    }
</style>

<section class="register-section">
    <div class="register-card">
        <h1>Create Your Account</h1>
        <p class="subtitle">Join EzyCommerce and unlock exclusive member benefits.</p>
        
        <form action="<?php echo url('/register'); ?>" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a strong password" required>
            </div>
            <div class="form-group">
                <label for="about">About You <span style="font-weight:400;color:#B2BEC3;">(Optional)</span></label>
                <textarea id="about" name="about" placeholder="Tell us a bit about yourself..."></textarea>
            </div>
            <button type="submit" class="submit-btn">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
            <div class="form-footer">
                Already have an account? <a href="<?php echo url('/login'); ?>">Sign in</a>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . "/components/footer.php"; ?>
