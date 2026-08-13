<?php
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION["user_email"])) {
    header("Location: dashboardS.php");
    exit();
}

$error = "";
$success = "";

// Handle registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $fullname = trim($_POST["fullname"]);
    
    // Validation
    if (empty($email) || empty($password) || empty($confirm_password) || empty($fullname)) {
        $error = "❌ Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "❌ Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "❌ Passwords do not match.";
    } else {
        // Check if user already exists
        $user_exists = false;
        if (isset($_SESSION["registered_users"])) {
            foreach ($_SESSION["registered_users"] as $user) {
                if ($user["email"] == $email) {
                    $user_exists = true;
                    break;
                }
            }
        }
        
        if ($user_exists) {
            $error = "❌ This email is already registered. Please login.";
        } else {
            // Add user to session (in a real app, save to database)
            if (!isset($_SESSION["registered_users"])) {
                $_SESSION["registered_users"] = [];
            }
            
            $_SESSION["registered_users"][] = [
                "email" => $email,
                "password" => $password, // In real app, use password_hash()
                "fullname" => $fullname,
                "registered_at" => date("Y-m-d H:i:s")
            ];
            
            $success = "✅ Account created successfully! You can now login.";
            
            // Auto-login (optional)
            // $_SESSION["user_email"] = $email;
            // header("Location: dashboardS.php");
            // exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - DeskDeal</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #004d1a, #00a844);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Wave background */
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

        .register-container {
            position: relative;
            z-index: 10;
            background: white;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
        }

        .register-container .logo-area {
            text-align: center;
            margin-bottom: 20px;
        }

        .register-container .logo-area .logo-icon {
            font-size: 40px;
            display: block;
            margin-bottom: 3px;
        }

        .register-container .logo-area h1 {
            font-size: 28px;
            color: #1a1a2e;
        }

        .register-container .logo-area h1 span {
            color: #00a844;
        }

        .register-container .logo-area .subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 3px;
        }

        .register-container .divider-line {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #00e676, #00a844);
            border-radius: 2px;
            margin: 15px auto 25px auto;
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

        .form-group label .required {
            color: #e63946;
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

        .form-group .hint {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }

        .btn-register {
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

        .btn-register:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 168, 68, 0.3);
        }

        .btn-back {
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
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-back:hover {
            background: #00a844;
            color: white;
        }

        .error-message {
            background: #ffe0e0;
            color: #d63031;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 600;
            border-left: 4px solid #dc3545;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 600;
            border-left: 4px solid #28a745;
        }

        .success-message small {
            display: block;
            margin-top: 8px;
            font-weight: 400;
        }

        .success-message a {
            color: #155724;
            font-weight: 600;
        }

        .success-message a:hover {
            text-decoration: underline;
        }

        .back-to-login {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #666;
        }

        .back-to-login a {
            color: #00a844;
            font-weight: 600;
            text-decoration: none;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }

        .terms {
            margin-top: 15px;
            font-size: 12px;
            color: #999;
            text-align: center;
        }

        .terms a {
            color: #00a844;
            text-decoration: none;
        }

        .terms a:hover {
            text-decoration: underline;
        }

        .password-strength {
            margin-top: 5px;
            display: flex;
            gap: 5px;
        }

        .password-strength .bar {
            flex: 1;
            height: 4px;
            background: #eee;
            border-radius: 2px;
            transition: background 0.3s;
        }

        .password-strength .bar.weak { background: #ff6b6b; }
        .password-strength .bar.medium { background: #ffd93d; }
        .password-strength .bar.strong { background: #6bcb77; }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

    <div class="register-container">
        <div class="logo-area">
            <span class="logo-icon">📚</span>
            <h1>Desk<span>Deal</span></h1>
            <p class="subtitle">Create your account to get started</p>
            <div class="divider-line"></div>
        </div>

        <?php if ($error != "") { ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <?php if ($success != "") { ?>
            <div class="success-message">
                <?php echo $success; ?>
                <small>🔑 <a href="loginS.php">Click here to login</a></small>
            </div>
        <?php } ?>

        <?php if ($success == "") { ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="fullname">Full Name <span class="required">*</span></label>
                    <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" placeholder="Create a password" required>
                    <div class="hint">Must be at least 6 characters</div>
                    <div class="password-strength">
                        <div class="bar" id="bar1"></div>
                        <div class="bar" id="bar2"></div>
                        <div class="bar" id="bar3"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                </div>

                <button type="submit" name="register" class="btn-register">📝 Create Account</button>
            </form>

            <div class="back-to-login">
                Already have an account? <a href="loginS.php">Login</a>
            </div>

            <div class="terms">
                By signing up, you agree to our <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
            </div>
        <?php } ?>

        <a href="loginS.php" class="btn-back">← Back to Login</a>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const bar1 = document.getElementById('bar1');
            const bar2 = document.getElementById('bar2');
            const bar3 = document.getElementById('bar3');

            // Reset bars
            bar1.className = 'bar';
            bar2.className = 'bar';
            bar3.className = 'bar';

            if (password.length === 0) return;

            const hasLetters = /[a-zA-Z]/.test(password);
            const hasNumbers = /[0-9]/.test(password);
            const hasSpecials = /[^a-zA-Z0-9]/.test(password);
            const length = password.length;

            let strength = 0;
            if (length >= 6) strength++;
            if (hasLetters && hasNumbers) strength++;
            if (hasSpecials && length >= 8) strength++;

            if (strength >= 2) {
                bar1.className = 'bar strong';
                bar2.className = 'bar strong';
                if (strength >= 3) {
                    bar3.className = 'bar strong';
                } else {
                    bar3.className = 'bar medium';
                }
            } else if (strength >= 1) {
                bar1.className = 'bar medium';
                bar2.className = 'bar medium';
                bar3.className = 'bar';
            } else {
                bar1.className = 'bar weak';
                bar2.className = 'bar';
                bar3.className = 'bar';
            }
        });
    </script>

</body>
</html>