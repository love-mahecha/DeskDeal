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
        
        $success_message = " Thank you for your feedback!";
        
      
        header("refresh:2; url=completeS.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Student Work</title>
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
            background: linear-gradient(135deg, #6c5ce7 0%, #4b6aff 100%);
            padding: 20px;
        }

        .feedback-container {
            background: white;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 550px;
            text-align: center;
        }

        .feedback-container .emoji {
            font-size: 50px;
            display: block;
            margin-bottom: 10px;
        }

        .feedback-container h1 {
            font-size: 28px;
            color: #1a1a2e;
            margin-bottom: 5px;
        }

        .feedback-container .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .feedback-container .user-badge {
            display: inline-block;
            background: #e8f0fe;
            color: #4b6aff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
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

        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            transition: border-color 0.3s;
            resize: vertical;
            min-height: 120px;
            background: white;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: #4b6aff;
        }

        .form-group textarea::placeholder {
            color: #aaa;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4b6aff, #3a56d4);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 5px;
        }

        .btn-submit:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(75, 106, 255, 0.3);
        }

        .btn-skip {
            display: inline-block;
            margin-top: 15px;
            color: #999;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s;
        }

        .btn-skip:hover {
            color: #4b6aff;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            border-left: 4px solid #28a745;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            border-left: 4px solid #dc3545;
        }

        .note {
            margin-top: 20px;
            font-size: 13px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>

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
                <br><small>Redirecting to completion page...</small>
            </div>
        <?php } ?>

        <?php if ($error_message != "") { ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

       
        <form action="" method="POST">
            <div class="form-group">
                <label for="feedback">What can we improve on this site? <span class="required">*</span></label>
                <textarea id="feedback" name="feedback" placeholder="Tell us your thoughts... (e.g., Add more subjects, Better UI, Faster loading, etc.)" required></textarea>
            </div>

            <button type="submit" name="submit_feedback" class="btn-submit">
                📤 Submit Feedback
            </button>
        </form>

        <a href="completeS.php" class="btn-skip">⏭️ Skip & Continue →</a>

        <p class="note">
             Your feedback helps us make this site better for everyone!
        </p>
    </div>

</body>
</html>