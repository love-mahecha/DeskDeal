<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$hardcoded_users = [
    "student@123" => "password123",
    "test@test.com" => "123456",
    "student@deskdeal.com" => "password123",
    "admin@deskdeal.com" => "admin123"
];

$registered_users = [];
if (isset($_SESSION["registered_users"])) {
    foreach ($_SESSION["registered_users"] as $user) {
        $registered_users[$user["email"]] = $user["password"];
    }
}

$users = array_merge($hardcoded_users, $registered_users);
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    if (array_key_exists($email, $users) && $users[$email] == $password) {
        $_SESSION["user_email"] = $email;
        header("Location: dashboardS.php");
        exit();
    } else {
        $error = "Invalid email or password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeskDeal - Student Work Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0a0a1a;
            color: white;
            overflow-x: hidden;
        }

        /* ===== VIDEO HERO SECTION ===== */
        .hero-section {
            position: relative;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .hero-section video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        .hero-section .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, 
                rgba(10, 10, 26, 0.3) 0%, 
                rgba(10, 10, 26, 0.6) 50%,
                rgba(10, 10, 26, 0.85) 100%
            );
            z-index: 1;
        }

        .hero-section .content {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 20px;
            animation: fadeInUp 1.5s ease-out;
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(50px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .hero-section .content .logo {
            font-size: 48px;
            font-weight: 900;
            letter-spacing: -2px;
            background: linear-gradient(135deg, #ffffff, #a8a8ff, #6c5ce7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 60px rgba(108, 92, 231, 0.3);
        }

        .hero-section .content h1 {
            font-size: 56px;
            font-weight: 800;
            line-height: 1.15;
            color: white;
            letter-spacing: -2px;
            margin-top: 10px;
        }

        .hero-section .content h1 span {
            background: linear-gradient(135deg, #a8a8ff, #6c5ce7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-section .content p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 15px;
            font-weight: 300;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-section .content .cta-tag {
            display: inline-block;
            margin-top: 15px;
            padding: 6px 22px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 30px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            letter-spacing: 2px;
        }

        .hero-section .scroll-indicator {
            position: absolute;
            bottom: 40px;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.25);
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-decoration: none;
            transition: all 0.3s;
        }

        .hero-section .scroll-indicator:hover {
            color: rgba(255, 255, 255, 0.6);
        }

        .hero-section .scroll-indicator .arrow-down {
            width: 24px;
            height: 24px;
            border-right: 2px solid rgba(255, 255, 255, 0.3);
            border-bottom: 2px solid rgba(255, 255, 255, 0.3);
            transform: rotate(45deg);
            animation: bounceDown 2s infinite;
        }

        @keyframes bounceDown {
            0%, 100% { transform: rotate(45deg) translateY(0); }
            50% { transform: rotate(45deg) translateY(8px); }
        }

        /* ===== LOGIN SECTION ===== */
        .login-section-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            background: #0a0a1a;
            position: relative;
        }

        .login-section-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1600&q=80') center/cover no-repeat;
            opacity: 0.08;
            z-index: 0;
        }

        /* ===== SCROLL TRANSITIONS ===== */
        .scroll-transition {
            opacity: 0;
            transform: translateY(60px);
            transition: all 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .scroll-transition.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .scroll-transition-left {
            opacity: 0;
            transform: translateX(-60px);
            transition: all 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .scroll-transition-left.visible {
            opacity: 1;
            transform: translateX(0);
        }

        .scroll-transition-right {
            opacity: 0;
            transform: translateX(60px);
            transition: all 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .scroll-transition-right.visible {
            opacity: 1;
            transform: translateX(0);
        }

        .scroll-transition-scale {
            opacity: 0;
            transform: scale(0.9);
            transition: all 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .scroll-transition-scale.visible {
            opacity: 1;
            transform: scale(1);
        }

        /* ===== DELAYS ===== */
        .delay-1 { transition-delay: 0.1s; }
        .delay-2 { transition-delay: 0.2s; }
        .delay-3 { transition-delay: 0.3s; }
        .delay-4 { transition-delay: 0.4s; }
        .delay-5 { transition-delay: 0.5s; }

        /* ===== LOGIN CONTAINER ===== */
        .login-container {
            position: relative;
            z-index: 1;
            display: flex;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 960px;
            overflow: hidden;
            min-height: 520px;
        }

        .about-section {
            width: 55%;
            padding: 45px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .about-section .logo-area {
            margin-bottom: 25px;
        }

        .about-section .logo-area .logo-icon {
            font-size: 44px;
            display: block;
            margin-bottom: 4px;
        }

        .about-section .logo-area h1 {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #a8a8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .about-section .logo-area .tagline {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 400;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        .about-section .divider-line {
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #6c5ce7, #00a844);
            border-radius: 2px;
            margin: 18px 0 20px 0;
        }

        .about-section .about-text {
            font-size: 14px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
            font-weight: 300;
        }

        .about-section .about-text strong {
            color: white;
            font-weight: 600;
        }

        .about-section .features {
            list-style: none;
            padding: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px 15px;
        }

        .about-section .features li {
            padding: 5px 0;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.65);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 300;
        }

        .about-section .features li .icon {
            font-size: 14px;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            flex-shrink: 0;
        }

        .about-section .footer-text {
            margin-top: 25px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 15px;
            letter-spacing: 0.5px;
        }

        .login-section {
            width: 45%;
            padding: 45px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.02);
            border-left: 1px solid rgba(255, 255, 255, 0.05);
        }

        .login-section h2 {
            font-size: 26px;
            color: white;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .login-section .subtitle {
            color: rgba(255, 255, 255, 0.35);
            font-size: 13px;
            margin-bottom: 25px;
            font-weight: 300;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
            position: relative;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px 13px 44px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            outline: none;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.2);
            font-weight: 300;
        }

        .form-group input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(108, 92, 231, 0.3);
            box-shadow: 0 0 25px rgba(108, 92, 231, 0.05);
        }

        .form-group .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            opacity: 0.25;
            pointer-events: none;
        }

        .error-message {
            background: rgba(255, 71, 87, 0.1);
            color: #ff6b6b;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-weight: 500;
            font-size: 13px;
            border: 1px solid rgba(255, 71, 87, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6c5ce7, #00a844);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(108, 92, 231, 0.35);
        }

        .btn-login:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-register {
            width: 100%;
            padding: 13px;
            background: rgba(255, 255, 255, 0.04);
            color: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            margin-top: 10px;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-register:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            border-color: rgba(255, 255, 255, 0.15);
        }

        .forgot-link {
            margin-top: 16px;
            text-align: center;
        }

        .forgot-link a {
            color: rgba(255, 255, 255, 0.3);
            text-decoration: none;
            font-size: 12px;
            font-weight: 400;
            transition: all 0.3s;
        }

        .forgot-link a:hover {
            color: rgba(255, 255, 255, 0.6);
        }

        .signup-prompt {
            margin-top: 4px;
            text-align: center;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.25);
        }

        .signup-prompt a {
            color: #6c5ce7;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .signup-prompt a:hover {
            color: #a8a8ff;
        }

        .trust-badges {
            margin-top: 16px;
            display: flex;
            justify-content: center;
            gap: 20px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.15);
        }

        .trust-badges span {
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }

        .trust-badges span:hover {
            color: rgba(255, 255, 255, 0.4);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .hero-section .content h1 {
                font-size: 42px;
            }
        }

        @media (max-width: 820px) {
            .login-container {
                flex-direction: column;
                max-width: 440px;
                min-height: auto;
                border-radius: 20px;
                margin: 10px;
            }

            .about-section {
                width: 100%;
                padding: 30px 25px;
                border-radius: 20px 20px 0 0;
            }

            .about-section .features {
                grid-template-columns: 1fr 1fr;
            }

            .login-section {
                width: 100%;
                padding: 30px 25px;
                border-left: none;
                border-top: 1px solid rgba(255, 255, 255, 0.05);
            }

            .about-section .logo-area h1 {
                font-size: 28px;
            }

            .hero-section .content h1 {
                font-size: 32px;
            }

            .hero-section .content .logo {
                font-size: 36px;
            }
        }

        @media (max-width: 480px) {
            .hero-section .content h1 {
                font-size: 24px;
            }

            .hero-section .content p {
                font-size: 14px;
            }

            .about-section {
                padding: 20px 18px;
            }
            .login-section {
                padding: 20px 18px;
            }
            .form-group input {
                padding: 11px 14px 11px 38px;
                font-size: 13px;
            }
            .about-section .features {
                grid-template-columns: 1fr;
            }
            .about-section .logo-area h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

    <!-- ===== VIDEO HERO SECTION ===== -->
    <section class="hero-section" id="hero">
        <video autoplay muted loop playsinline>
            <source src="https://assets.mixkit.co/videos/preview/mixkit-typing-on-a-laptop-in-a-modern-office-38618-large.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="overlay"></div>

        <div class="content">
            <div class="logo">DeskDeal</div>
            <h1><span>Student Learning</span> Made Simple</h1>
            <p>Post tasks, find help, or earn money — all in one place.</p>
            <div class="cta-tag">🚀 GET STARTED</div>
        </div>

        <a href="#login" class="scroll-indicator">
            <span>Scroll to Login</span>
            <div class="arrow-down"></div>
        </a>
    </section>

    <!-- ===== LOGIN SECTION WITH SCROLL TRANSITIONS ===== -->
    <section class="login-section-wrapper" id="login">
        <div class="login-container">

            <!-- LEFT SIDE - About Section -->
            <div class="about-section">
                <div class="scroll-transition-left delay-1">
                    <div class="logo-area">
                        <span class="logo-icon">📚</span>
                        <h1>DeskDeal</h1>
                        <p class="tagline">Student Work Marketplace</p>
                    </div>
                </div>

                <div class="scroll-transition delay-2">
                    <div class="divider-line"></div>
                </div>

                <div class="scroll-transition delay-3">
                    <p class="about-text">
                        Welcome to <strong>DeskDeal</strong> — your go-to platform for students to 
                        <strong>get help</strong> with assignments or <strong>earn money</strong> 
                        by helping others.
                    </p>
                </div>

                <div class="scroll-transition delay-4">
                    <ul class="features">
                        <li><span class="icon">📝</span> Post homework requests</li>
                        <li><span class="icon">💰</span> Set your price per page</li>
                        <li><span class="icon">💼</span> Apply to work on assignments</li>
                        <li><span class="icon">🎉</span> Earn while you learn</li>
                    </ul>
                </div>

                <div class="scroll-transition delay-5">
                    <div class="footer-text">
                        © 2026 DeskDeal • Made for Students
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE - Login Form -->
            <div class="login-section">
                <div class="scroll-transition-right delay-1">
                    <h2>Welcome Back</h2>
                    <p class="subtitle">Login to continue your learning journey</p>
                </div>
                
                <?php if ($error != "") { ?>
                    <div class="scroll-transition delay-2">
                        <div class="error-message">
                            <?php echo $error; ?>
                        </div>
                    </div>
                <?php } ?>
                
                <form action="" method="POST">
                    <div class="scroll-transition-right delay-2">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <span class="input-icon">📧</span>
                            <input type="email" id="email" name="email" placeholder="student@deskdeal.com" required>
                        </div>
                    </div>

                    <div class="scroll-transition-right delay-3">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <span class="input-icon">🔒</span>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>

                    <div class="scroll-transition-right delay-3">
                        <button type="submit" name="login" class="btn-login">Sign In</button>
                    </div>
                </form>

                <div class="scroll-transition-right delay-4">
                    <a href="registerS.php" class="btn-register">
                        Create New Account
                    </a>
                </div>

                <div class="scroll-transition delay-5">
                    <div class="forgot-link">
                        <a href="#">Forgot Password?</a>
                    </div>
                </div>

                <div class="scroll-transition delay-5">
                    <div class="signup-prompt">
                        Don't have an account? <a href="registerS.php">Sign Up</a>
                    </div>
                </div>

                <div class="scroll-transition-scale delay-5">
                    <div class="trust-badges">
                        <span>Secure</span>
                        <span>Student Friendly</span>
                        <span>Trusted Community</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        // ===== SCROLL TRANSITION OBSERVER =====
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.scroll-transition, .scroll-transition-left, .scroll-transition-right, .scroll-transition-scale');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            elements.forEach(el => observer.observe(el));
        });

        // ===== VIDEO AUTOPLAY FIX =====
        document.querySelector('video')?.play();
    </script>

</body>
</html>