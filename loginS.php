<?php 
// Starting the session so I can remember the logged-in user
session_start();

// Enable error reporting (remove after testing)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// HARDCODED USERS (for demo)
$hardcoded_users = [
    "student@123" => "password123",
    "test@test.com" => "123456"
];

// Get registered users from session
$registered_users = [];
if (isset($_SESSION["registered_users"])) {
    foreach ($_SESSION["registered_users"] as $user) {
        $registered_users[$user["email"]] = $user["password"];
    }
}

// MERGE both arrays (hardcoded + registered)
$users = array_merge($hardcoded_users, $registered_users);

// Variable to store error
$error = "";

// Checking if login is submitted 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    
    // Checking if the email exists in our "database" AND the password matches
    if (array_key_exists($email, $users) && $users[$email] == $password) {
        // SUCCESS: Save email in session and redirect to Dashboard
        $_SESSION["user_email"] = $email;
        
        // Redirect to dashboard
        header("Location: dashboardS.php");
        exit();
    } else {
        // FAILURE: Set an error message
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* ---------- WAVE BACKGROUND ---------- */
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #004d1a;
            position: relative;
            overflow: hidden;
        }

        /* Wave 1 */
        body::before {
            content: '';
            position: absolute;
            width: 1200px;
            height: 1200px;
            top: -400px;
            left: -300px;
            border-radius: 45%;
            background: linear-gradient(135deg, #00e676 0%, #00a844 70%);
            z-index: 1;
            animation: rotateWave 25s infinite linear;
        }

        /* Wave 2 */
        body::after {
            content: '';
            position: absolute;
            width: 1000px;
            height: 1000px;
            bottom: -300px;
            right: -200px;
            border-radius: 40%;
            background: linear-gradient(135deg, #00c853 0%, #007e33 80%);
            z-index: 1;
            animation: rotateWave 20s infinite linear reverse;
        }

        @keyframes rotateWave {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ---------- MAIN CONTAINER ---------- */
        .main-container {
            position: relative;
            z-index: 10;
            display: flex;
            background: white;
            border-radius: 25px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 950px;
            overflow: hidden;
            min-height: 550px;
        }

        /* ---------- LEFT SIDE: ABOUT SECTION ---------- */
        .about-section {
            flex: 1;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding: 45px 35px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .about-section .logo-area {
            margin-bottom: 25px;
        }

        .about-section .logo-area .logo-icon {
            font-size: 45px;
            display: block;
            margin-bottom: 5px;
        }

        .about-section .logo-area h1 {
            font-size: 36px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .about-section .logo-area h1 span {
            color: #00e676;
        }

        .about-section .logo-area .tagline {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 3px;
        }

        .about-section .divider-line {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #00e676, #4b6aff);
            border-radius: 2px;
            margin: 20px 0;
        }

        .about-section .about-text {
            font-size: 15px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 20px;
        }

        .about-section .about-text strong {
            color: #00e676;
        }

        .about-section .features {
            list-style: none;
            padding: 0;
        }

        .about-section .features li {
            padding: 6px 0;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .about-section .features li .icon {
            font-size: 18px;
        }

        .about-section .footer-text {
            margin-top: 25px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 15px;
        }

        /* ---------- RIGHT SIDE: LOGIN FORM ---------- */
        .login-section {
            flex: 1;
            padding: 45px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .login-section h2 {
            font-size: 26px;
            color: #1a1a2e;
            margin-bottom: 5px;
        }

        .login-section .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #00a844;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #00a844, #007e33);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 5px;
        }

        .btn-login:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 168, 68, 0.3);
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: #00a844;
            border: 2px solid #00a844;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-register:hover {
            background: #00a844;
            color: white;
        }

        .error-message {
            background: #ffe0e0;
            color: #d63031;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        /* ---------- NEW FEATURES STYLES ---------- */
        .forgot-link {
            margin-top: 15px;
            text-align: center;
        }

        .forgot-link a {
            color: #00a844;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s;
        }

        .forgot-link a:hover {
            color: #007e33;
            text-decoration: underline;
        }

        .signup-prompt {
            margin-top: 10px;
            text-align: center;
            font-size: 13px;
            color: #999;
        }

        .signup-prompt a {
            color: #00a844;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s;
        }

        .signup-prompt a:hover {
            color: #007e33;
            text-decoration: underline;
        }

        .trust-badges {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 20px;
            font-size: 12px;
            color: #bbb;
        }

        .trust-badges span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
                max-width: 400px;
                border-radius: 15px;
            }

            .about-section {
                padding: 30px 25px;
                border-radius: 15px 15px 0 0;
            }

            .login-section {
                padding: 30px 25px;
            }

            .about-section .logo-area h1 {
                font-size: 28px;
            }

            .trust-badges {
                gap: 12px;
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>

    <div class="main-container">
        <!-- LEFT SIDE: ABOUT -->
        <div class="about-section">
            <div class="logo-area">
                <span class="logo-icon">📚</span>
                <h1>Desk<span>Deal</span></h1>
                <p class="tagline">Student Work Marketplace</p>
            </div>

            <div class="divider-line"></div>

            <p class="about-text">
                Welcome to <strong>DeskDeal</strong> — your go-to platform for students to 
                <strong>get help</strong> with assignments or <strong>earn money</strong> 
                by helping others. Whether you need a hand with homework or want to put 
                your skills to work, DeskDeal connects students just like you.
            </p>

            <ul class="features">
                <li><span class="icon">📝</span> Post your homework requests</li>
                <li><span class="icon">💰</span> Set your price per page</li>
                <li><span class="icon">💼</span> Apply to work on assignments</li>
                <li><span class="icon">🎉</span> Earn while you learn</li>
            </ul>

            <div class="footer-text">
                © 2026 DeskDeal • Built with ❤️ for students
            </div>
        </div>

        <!-- RIGHT SIDE: LOGIN -->
        <div class="login-section">
            <h2>Welcome Back!</h2>
            <p class="subtitle">Login to start earning or getting help</p>
            
            <?php if ($error != "") { ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php } ?>
            
            <!-- Login form -->
            <form action="" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <button type="submit" name="login" class="btn-login">🔑 Login</button>
            </form>

            <!-- REGISTER BUTTON -->
            <a href="registerS.php" class="btn-register" style="text-decoration: none; display: block; text-align: center;">
                📝 Create New Account
            </a>

            <!-- NEW: Forgot Password Link -->
            <div class="forgot-link">
                <a href="#">🔑 Forgot Password?</a>
            </div>

            <!-- NEW: Sign Up Prompt -->
            <div class="signup-prompt">
                Don't have an account? <a href="registerS.php" style="color: #00a844; font-weight: 600; text-decoration: none;">Sign Up</a>
            </div>

            <!-- NEW: Trust Badges -->
            <div class="trust-badges">
                <span>🔒 Secure</span>
                <span>🛡️ Private</span>
                <span>✅ Safe</span>
            </div>

        </div>
    </div>

</body>
</html>