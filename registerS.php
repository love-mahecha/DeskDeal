<?php
session_start();

if (isset($_SESSION["user_email"])) {
    header("Location: dashboardS.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $fullname = trim($_POST["fullname"]);
    
    if (empty($email) || empty($password) || empty($confirm_password) || empty($fullname)) {
        $error = "❌ Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "❌ Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "❌ Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "❌ Passwords do not match.";
    } else {
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
            if (!isset($_SESSION["registered_users"])) {
                $_SESSION["registered_users"] = [];
            }
            
            $_SESSION["registered_users"][] = [
                "email" => $email,
                "password" => $password,
                "fullname" => $fullname,
                "registered_at" => date("Y-m-d H:i:s")
            ];
            
            $success = "✅ Account created successfully! You can now login.";
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
            justify-content: center;
            align-items: center;
            background: #0a0a1a;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .bg-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.3;
            pointer-events: none;
        }

        .bg-blob-1 {
            width: 500px;
            height: 500px;
            top: -200px;
            left: -200px;
            background: radial-gradient(circle, #00a844, #007e33);
        }

        .bg-blob-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, #6c5ce7, #00a844);
        }

        .bg-blob-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, #6bcb77, #00a844);
            opacity: 0.1;
        }

        .register-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            padding: 45px 45px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
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
            margin-bottom: 4px;
        }

        .register-container .logo-area h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #6bcb77);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .register-container .logo-area .subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            font-weight: 300;
            margin-top: 2px;
        }

        .register-container .divider-line {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #6bcb77, #00a844);
            border-radius: 2px;
            margin: 15px auto 25px auto;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 5px;
            letter-spacing: 0.3px;
        }

        .form-group label .required {
            color: #ff6b6b;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px;
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
            color: rgba(255, 255, 255, 0.25);
            font-weight: 300;
        }

        .form-group input:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(0, 168, 68, 0.3);
            box-shadow: 0 0 25px rgba(0, 168, 68, 0.05);
        }

        .form-group .hint {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.2);
            margin-top: 4px;
            font-weight: 300;
        }

        .btn-register {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #00a844, #007e33);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 20px rgba(0, 168, 68, 0.25);
            margin-top: 5px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 168, 68, 0.35);
        }

        .btn-register:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-back {
            width: 100%;
            padding: 13px;
            background: rgba(255, 255, 255, 0.04);
            color: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            margin-top: 10px;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.8);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .error-message {
            background: rgba(255, 71, 87, 0.1);
            color: #ff6b6b;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 15px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid rgba(255, 71, 87, 0.1);
        }

        .success-message {
            background: rgba(40, 167, 69, 0.1);
            color: #6bcb77;
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 15px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid rgba(40, 167, 69, 0.15);
        }

        .success-message small {
            display: block;
            margin-top: 6px;
            font-weight: 300;
            font-size: 13px;
        }

        .success-message a {
            color: #6bcb77;
            font-weight: 500;
        }

        .success-message a:hover {
            text-decoration: underline;
        }

        .back-to-login {
            text-align: center;
            margin-top: 12px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.3);
            font-weight: 300;
        }

        .back-to-login a {
            color: #6bcb77;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        .back-to-login a:hover {
            color: #a8e6a8;
        }

        .terms {
            margin-top: 12px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.15);
            text-align: center;
            font-weight: 300;
        }

        .terms a {
            color: rgba(255, 255, 255, 0.3);
            text-decoration: none;
            transition: all 0.3s;
        }

        .terms a:hover {
            color: rgba(255, 255, 255, 0.5);
        }

        .password-strength {
            margin-top: 6px;
            display: flex;
            gap: 6px;
        }

        .password-strength .bar {
            flex: 1;
            height: 4px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 2px;
            transition: background 0.3s;
        }

        .password-strength .bar.weak { background: #ff6b6b; }
        .password-strength .bar.medium { background: #ffd93d; }
        .password-strength .bar.strong { background: #6bcb77; }

        @media (max-width: 480px) {
            .register-container {
                padding: 30px 25px;
                border-radius: 20px;
            }
        }

        @media (max-width: 400px) {
            .register-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <div class="register-container">
        <div class="logo-area">
            <span class="logo-icon">📚</span>
            <h1>DeskDeal</h1>
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
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const bar1 = document.getElementById('bar1');
            const bar2 = document.getElementById('bar2');
            const bar3 = document.getElementById('bar3');

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