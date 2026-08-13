<?php

session_start();


if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$feedback_list = isset($_SESSION["feedback"]) ? $_SESSION["feedback"] : [];
$request_count = isset($_SESSION["requests"]) ? count($_SESSION["requests"]) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Done! - Student Work</title>
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
            background: linear-gradient(135deg, #00b894 0%, #00a844 100%);
            padding: 20px;
        }

        .complete-container {
            background: white;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        .complete-container .emoji {
            font-size: 60px;
            display: block;
            margin-bottom: 10px;
        }

        .complete-container h1 {
            font-size: 28px;
            color: #1a1a2e;
            margin-bottom: 5px;
        }

        .complete-container .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .complete-container .user-badge {
            display: inline-block;
            background: #e8f0fe;
            color: #4b6aff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .stat-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
        }

        .stat-box .number {
            font-size: 24px;
            font-weight: 700;
            color: #4b6aff;
        }

        .stat-box .label {
            font-size: 12px;
            color: #888;
        }

        .feedback-section {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: left;
        }

        .feedback-section h3 {
            color: #1a1a2e;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .feedback-item {
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            border-left: 3px solid #4b6aff;
        }

        .feedback-item .text {
            color: #333;
            font-size: 14px;
        }

        .feedback-item .meta {
            color: #999;
            font-size: 12px;
            margin-top: 3px;
        }

        .no-feedback {
            color: #999;
            font-size: 14px;
            font-style: italic;
            padding: 10px 0;
        }

        .btn-dashboard {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, #00b894, #00a844);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        .btn-dashboard:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 184, 148, 0.3);
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

    <div class="complete-container">
        <span class="emoji">🎉</span>
        <h1>Deal Done!</h1>
        <p class="subtitle">Your request has been posted successfully</p>
        
        <div class="user-badge">
            👤 <?php echo htmlspecialchars($user_email); ?>
        </div>

       
        <div class="stats-grid">
            <div class="stat-box">
                <div class="number"><?php echo $request_count; ?></div>
                <div class="label">Total Requests</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo count($feedback_list); ?></div>
                <div class="label">Feedback Given</div>
            </div>
        </div>

        
        <div class="feedback-section">
            <h3>💬 Feedback Received</h3>
            <?php if (count($feedback_list) > 0) { ?>
                <?php foreach (array_reverse($feedback_list) as $fb) { ?>
                    <div class="feedback-item">
                        <div class="text">"<?php echo htmlspecialchars($fb["feedback"]); ?>"</div>
                        <div class="meta">By: <?php echo htmlspecialchars($fb["email"]); ?> • <?php echo htmlspecialchars($fb["timestamp"]); ?></div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="no-feedback">No feedback submitted yet.</div>
            <?php } ?>
        </div>

        <a href="dashboardS.php" class="btn-dashboard">
            🏠 Return to Dashboard
        </a>

        <p class="note">
            🔒 Thank you for using Student Work Marketplace!
        </p>
    </div>

</body>
</html>