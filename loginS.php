<?php 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$hardcoded_users = [
    "student@123" => "password123",
    "test@test.com" => "123456"
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

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            background: #0a0a1a;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1600&q=80') center/cover no-repeat;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, 
                rgba(10, 10, 26, 0.2) 0%, 
                rgba(10, 10, 26, 0.5) 50%,
                rgba(10, 10, 26, 0.85) 100%
            );
            z-index: 1;
        }

        .left-section {
            flex: 1.2;
            padding: 60px 70px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 2;
            color: white;
            min-height: 100vh;
        }

        .left-section .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 50px;
        }

        .left-section .brand .logo {
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 800;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .left-section .brand h2 {
            font-size: 20px;
            font-weight: 700;
            color: white;
            letter-spacing: -0.5px;
        }

        .left-section .brand h2 span {
            color: #a8a8ff;
        }

        .left-section .hero h1 {
            font-size: 44px;
            font-weight: 800;
            line-height: 1.15;
            color: white;
            letter-spacing: -2px;
            max-width: 520px;
        }

        .left-section .hero h1 span {
            background: linear-gradient(135deg, #a8a8ff, #6c5ce7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .left-section .hero .cta-tag {
            display: inline-block;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 1px;
            text-transform: uppercase;
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 6px 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.03);
        }

        .left-section .hero p {
            margin-top: 20px;
            font-size: 16px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.8;
            max-width: 420px;
            font-weight: 300;
        }

        .left-section .impact-badge {
            margin-top: 30px;
            display: inline-block;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 8px 22px;
            border-radius: 30px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 400;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: fit-content;
        }

        .left-section .impact-badge strong {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .left-section .vps-link {
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        .left-section .vps-link:hover {
            color: rgba(255, 255, 255, 0.85);
            gap: 12px;
        }

        .left-section .vps-link .arrow {
            font-size: 18px;
            transition: all 0.3s;
            color: rgba(255, 255, 255, 0.3);
        }

        .left-section .vps-link:hover .arrow {
            transform: translateX(4px);
        }

        .left-section .footer-text {
            margin-top: 60px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.3);
        }

        .right-section {
            width: 440px;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-left: 1px solid rgba(255, 255, 255, 0.06);
            position: relative;
            z-index: 2;
        }

        .right-section .welcome-text {
            margin-bottom: 30px;
        }

        .right-section .welcome-text h2 {
            font-size: 22px;
            font-weight: 700;
            color: white;
            letter-spacing: -0.5px;
        }

        .right-section .welcome-text p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.35);
            margin-top: 4px;
            font-weight: 300;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.04);
            color: white;
            outline: none;
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.2);
            font-weight: 300;
        }

        .form-group input:focus {
            border-color: rgba(108, 92, 231, 0.3);
            background: rgba(255, 255, 255, 0.06);
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.04);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 6px 0 20px 0;
        }

        .form-options .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
        }

        .form-options .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #6c5ce7;
            cursor: pointer;
        }

        .form-options .forgot-link {
            font-size: 13px;
            color: #a8a8ff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .form-options .forgot-link:hover {
            color: #c8c8ff;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6c5ce7, #5a4bda);
            color: white;
            border: none;
            border-radius: 10px;
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

        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            gap: 16px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.06);
        }

        .divider span {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.25);
            font-weight: 300;
        }

        .btn-register {
            width: 100%;
            padding: 13px;
            background: rgba(255, 255, 255, 0.03);
            color: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-register:hover {
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.7);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .error-message {
            background: rgba(220, 38, 38, 0.1);
            color: #ef4444;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-weight: 500;
            font-size: 13px;
            border: 1px solid rgba(220, 38, 38, 0.1);
        }

        .trust-badges {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 24px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.15);
        }

        .trust-badges span {
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s;
        }

        .trust-badges span:hover {
            color: rgba(255, 255, 255, 0.35);
        }

        @media (max-width: 1024px) {
            .left-section {
                padding: 40px 50px;
            }

            .left-section .hero h1 {
                font-size: 38px;
            }

            .right-section {
                width: 380px;
                padding: 40px 35px;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            body::before {
                background: url('https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&q=80') center/cover no-repeat;
            }

            .left-section {
                padding: 30px 25px;
                min-height: 50vh;
                justify-content: center;
            }

            .left-section .hero h1 {
                font-size: 28px;
                max-width: 100%;
            }

            .left-section .hero p {
                max-width: 100%;
            }

            .left-section .footer-text {
                margin-top: 30px;
            }

            .right-section {
                width: 100%;
                padding: 30px 25px;
                border-left: none;
                border-top: 1px solid rgba(255, 255, 255, 0.06);
                backdrop-filter: blur(20px);
            }
        }

        @media (max-width: 480px) {
            .left-section .hero h1 {
                font-size: 22px;
            }

            .left-section .brand h2 {
                font-size: 18px;
            }

            .right-section {
                padding: 25px 20px;
            }

            .form-options {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }

            .trust-badges {
                gap: 12px;
                flex-wrap: wrap;
            }

            .left-section .impact-badge {
                font-size: 12px;
                padding: 6px 16px;
            }
        }
    </style>
</head>
<body>

    <!-- ===== LEFT SIDE ===== -->
    <div class="left-section">
        <div class="brand">
            <div class="logo">D</div>
            <h2>Desk<span>Deal</span></h2>
        </div>

        <div class="hero">
            <h1><span>Student Learning</span> Made Simple</h1>

            <span class="cta-tag">GET HELP OR EARN</span>

            <p>
                Need help with homework? Or want to earn money by helping others? 
                DeskDeal connects students to collaborate, learn, and earn together.
            </p>
        </div>

        <div class="impact-badge">
            <strong>Post Tasks</strong> • <strong> Find Help</strong> • <strong> Earn Money</strong>
        </div>

        <a href="#" class="vps-link">
            Post a Request • Apply for Work • Earn Money
            <span class="arrow">→</span>
        </a>

        <div class="footer-text">
           © 2026 DeskDeal • Built for Students
        </div>
    </div>

    <!-- ===== RIGHT SIDE ===== -->
    <div class="right-section">
        <div class="welcome-text">
            <h2>Welcome Back</h2>
            <p>Login to continue your learning journey</p>
        </div>

        <?php if ($error != "") { ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="student@deskdeal.com" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <div class="form-options">
                <label class="remember">
                    <input type="checkbox" checked> Remember me
                </label>
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>

            <button type="submit" name="login" class="btn-login">Sign In</button>
        </form>

        <div class="divider">
            <span>or continue with</span>
        </div>

        <a href="registerS.php" class="btn-register">Create New Account</a>

        <div class="trust-badges">
            <span>Secure</span>
            <span>Student Friendly</span>
            <span>Trusted Community</span>
        </div>
    </div>

</body>
</html>