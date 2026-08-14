<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$success_message = "";
$error_message = "";

if (!isset($_SESSION["feedback"])) {
    $_SESSION["feedback"] = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_feedback"])) {
    $feedback = trim(htmlspecialchars($_POST["feedback"]));
    
    if (empty($feedback)) {
        $error_message = "❌ Please write your feedback before submitting!";
    } else {
        $_SESSION["feedback"][] = [
            "email" => $user_email,
            "feedback" => $feedback,
            "timestamp" => date("Y-m-d H:i:s")
        ];
        
        $success_message = "✅ Thank you for your feedback!";
        
        header("refresh:2; url=completeS.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - DeskDeal</title>
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
            background: radial-gradient(circle, #6c5ce7, #4b6aff);
        }

        .bg-blob-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, #a8a8ff, #6c5ce7);
        }

        .bg-blob-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, #4b6aff, #6c5ce7);
            opacity: 0.1;
        }

        .feedback-container {
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
            max-width: 550px;
            text-align: center;
            animation: popIn 0.6s ease-out;
        }

        @keyframes popIn {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .feedback-container .emoji {
            font-size: 50px;
            display: block;
            margin-bottom: 10px;
            animation: pulseGlow 3s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 20px rgba(108, 92, 231, 0.3)); }
            50% { transform: scale(1.05); filter: drop-shadow(0 0 40px rgba(108, 92, 231, 0.6)); }
        }

        .feedback-container h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #a8a8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .feedback-container .subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            font-weight: 300;
            margin-bottom: 20px;
        }

        .feedback-container .user-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.7);
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .form-group label .required {
            color: #ff6b6b;
        }

        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            outline: none;
            border: 1px solid rgba(255, 255, 255, 0.06);
            resize: vertical;
            min-height: 130px;
        }

        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.25);
            font-weight: 300;
        }

        .form-group textarea:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(108, 92, 231, 0.3);
            box-shadow: 0 0 25px rgba(108, 92, 231, 0.05);
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #6c5ce7, #5a4bda);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.25);
            margin-top: 5px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(108, 92, 231, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-skip {
            display: inline-block;
            margin-top: 14px;
            color: rgba(255, 255, 255, 0.25);
            text-decoration: none;
            font-size: 13px;
            font-weight: 400;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .btn-skip:hover {
            color: rgba(255, 255, 255, 0.6);
        }

        .success-message {
            background: rgba(40, 167, 69, 0.1);
            color: #6bcb77;
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid rgba(40, 167, 69, 0.15);
        }

        .success-message small {
            display: block;
            margin-top: 4px;
            font-weight: 300;
            font-size: 13px;
            color: rgba(107, 203, 119, 0.7);
        }

        .error-message {
            background: rgba(255, 71, 87, 0.1);
            color: #ff6b6b;
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid rgba(255, 71, 87, 0.1);
        }

        .note {
            margin-top: 20px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.15);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 15px;
            font-weight: 300;
        }

        @media (max-width: 600px) {
            .feedback-container {
                padding: 30px 25px;
                border-radius: 20px;
                max-width: 100%;
            }

            .feedback-container h1 {
                font-size: 24px;
            }

            .form-group textarea {
                min-height: 100px;
            }
        }

        @media (max-width: 400px) {
            .feedback-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <div class="feedback-container">
        <span class="emoji">💬</span>
        <h1>We Value Your Feedback!</h1>
        <p class="subtitle">Help us improve your experience</p>
        
        <div class="user-badge">
            👤 <?php echo htmlspecialchars($user_email); ?>
        </div>

        <?php if ($success_message != "") { ?>
            <div class="success-message">
                <?php echo $success_message; ?>
                <small>Redirecting to completion page...</small>
            </div>
        <?php } ?>

        <?php if ($error_message != "") { ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="feedback">What can we improve on this site? <span class="required"></span></label>
                <textarea id="feedback" name="feedback" placeholder="Tell us your thoughts... (e.g., Add more subjects, Better UI, Faster loading, etc.)" required></textarea>
            </div>

            <button type="submit" name="submit_feedback" class="btn-submit">
                 Submit Feedback
            </button>
        </form>

        <a href="completeS.php" class="btn-skip">⏭Skip & Continue →</a>

        <p class="note">
             Your feedback helps us make DeskDeal better for everyone!
        </p>
    </div>

</body>
</html>