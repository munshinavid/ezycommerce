<?php
$pageTitle = 'Contact Us — EzyCommerce';
$pageDescription = 'Get in touch with EzyCommerce. We love to hear from our customers.';
require_once __DIR__ . "/components/header.php";
?>

<style>
    .contact-hero {
        background: linear-gradient(135deg, #1A1A2E 0%, #16213E 50%, #0F3460 100%);
        padding: 80px 0; text-align: center; color: #fff; position: relative; overflow: hidden;
    }
    .contact-hero::before {
        content: ''; position: absolute; top: -40%; right: -15%; width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(108,92,231,0.25) 0%, transparent 70%); border-radius: 50%;
    }
    .contact-hero h1 { font-size: 2.8rem; font-weight: 900; margin-bottom: 12px; position: relative; z-index: 1; letter-spacing: -1px; }
    .contact-hero p { font-size: 1.1rem; color: rgba(255,255,255,0.7); position: relative; z-index: 1; max-width: 500px; margin: 0 auto; }
    
    .contact-section { max-width: 1100px; margin: -60px auto 80px; padding: 0 24px; position: relative; z-index: 2; }
    .contact-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 28px; }
    
    .contact-info-cards { display: flex; flex-direction: column; gap: 16px; }
    .contact-info-card {
        background: #fff; border-radius: 16px; padding: 28px; border: 1px solid #E8ECF1;
        display: flex; align-items: flex-start; gap: 18px; box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
    }
    .contact-info-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .contact-icon {
        width: 52px; height: 52px; border-radius: 14px;
        background: linear-gradient(135deg, rgba(108,92,231,0.1), rgba(162,155,254,0.1));
        color: #6C5CE7; display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .contact-info-card h3 { font-weight: 700; font-size: 16px; margin-bottom: 6px; color: #2D3436; }
    .contact-info-card p { color: #636E72; font-size: 14px; line-height: 1.6; }
    .contact-info-card a { color: #6C5CE7; text-decoration: none; font-weight: 600; }
    .contact-info-card a:hover { text-decoration: underline; }
    
    .contact-form-card {
        background: #fff; border-radius: 16px; padding: 36px; border: 1px solid #E8ECF1;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    .contact-form-card h2 { font-size: 22px; font-weight: 700; margin-bottom: 8px; color: #2D3436; }
    .contact-form-card .subtitle { color: #636E72; font-size: 15px; margin-bottom: 28px; }
    
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 600; font-size: 14px; color: #2D3436; }
    .form-group input, .form-group textarea {
        width: 100%; padding: 13px 16px; border-radius: 10px; border: 1.5px solid #E8ECF1;
        background: #F7F8FC; font-size: 15px; font-family: 'Inter', sans-serif;
        color: #2D3436; outline: none; transition: all 0.2s;
    }
    .form-group input:focus, .form-group textarea:focus {
        border-color: #6C5CE7; background: #fff; box-shadow: 0 0 0 4px rgba(108,92,231,0.1);
    }
    .form-group textarea { min-height: 140px; resize: vertical; }
    .form-row { display: flex; gap: 16px; }
    .form-row .form-group { flex: 1; }
    
    .submit-btn {
        width: 100%; padding: 15px; background: linear-gradient(135deg, #6C5CE7, #5A4BD1);
        color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 700;
        cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.25s;
        box-shadow: 0 4px 15px rgba(108,92,231,0.3); display: flex; align-items: center;
        justify-content: center; gap: 8px;
    }
    .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(108,92,231,0.4); }
    
    @media(max-width: 768px) {
        .contact-grid { grid-template-columns: 1fr; }
        .contact-hero h1 { font-size: 2rem; }
        .contact-hero { padding: 56px 24px; }
        .form-row { flex-direction: column; gap: 0; }
        .contact-section { margin-top: -40px; }
    }
</style>

<section class="contact-hero">
    <h1>Get In Touch</h1>
    <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
</section>

<section class="contact-section">
    <div class="contact-grid">
        <div class="contact-info-cards">
            <div class="contact-info-card">
                <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                <div>
                    <h3>Our Location</h3>
                    <p>123 Commerce Street<br>City, State 12345</p>
                </div>
            </div>
            <div class="contact-info-card">
                <div class="contact-icon"><i class="fas fa-phone"></i></div>
                <div>
                    <h3>Phone Number</h3>
                    <p><a href="tel:+15551234567">+1 (555) 123-4567</a><br>Mon - Fri, 9am - 6pm</p>
                </div>
            </div>
            <div class="contact-info-card">
                <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                <div>
                    <h3>Email Address</h3>
                    <p><a href="mailto:support@ezycommerce.com">support@ezycommerce.com</a><br>We reply within 24 hours</p>
                </div>
            </div>
            <div class="contact-info-card">
                <div class="contact-icon"><i class="fas fa-clock"></i></div>
                <div>
                    <h3>Business Hours</h3>
                    <p>Monday — Friday: 9:00 AM — 6:00 PM<br>Saturday: 10:00 AM — 4:00 PM</p>
                </div>
            </div>
        </div>
        
        <div class="contact-form-card">
            <h2>Send Us a Message</h2>
            <p class="subtitle">Fill out the form and our team will get back to you shortly.</p>
            <form action="<?php echo url('/contact'); ?>" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="John Doe" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="How can we help?">
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Tell us more about your inquiry..." required></textarea>
                </div>
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . "/components/footer.php"; ?>
