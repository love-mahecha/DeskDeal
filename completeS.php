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
    <title>Done! - DeskDeal</title>
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
            background: radial-gradient(circle, #00b894, #00a844);
        }

        .bg-blob-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, #6c5ce7, #00b894);
        }

        .bg-blob-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, #6bcb77, #00b894);
            opacity: 0.1;
        }

        .complete-container {
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
            max-width: 600px;
            text-align: center;
        }

        .complete-container .emoji {
            font-size: 60px;
            display: block;
            margin-bottom: 10px;
            animation: pulseGlow 3s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 20px rgba(0, 184, 148, 0.3)); }
            50% { transform: scale(1.05); filter: drop-shadow(0 0 40px rgba(0, 184, 148, 0.6)); }
        }

        .complete-container h1 {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #6bcb77);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .complete-container .subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            font-weight: 300;
            margin-bottom: 20px;
        }

        .complete-container .user-badge {
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.04);
            padding: 18px 15px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.3s;
        }

        .stat-box:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .stat-box .number {
            font-size: 24px;
            font-weight: 700;
            color: #6bcb77;
        }

        .stat-box .label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.3);
            font-weight: 300;
            margin-top: 2px;
        }

        .feedback-section {
            background: rgba(255, 255, 255, 0.04);
            padding: 18px 20px;
            border-radius: 16px;
            margin: 20px 0 25px 0;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .feedback-section h3 {
            color: rgba(255, 255, 255, 0.6);
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .feedback-item {
            background: rgba(255, 255, 255, 0.03);
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 8px;
            border-left: 3px solid #6bcb77;
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        .feedback-item .text {
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            font-weight: 300;
        }

        .feedback-item .meta {
            color: rgba(255, 255, 255, 0.2);
            font-size: 12px;
            margin-top: 4px;
            font-weight: 300;
        }

        .no-feedback {
            color: rgba(255, 255, 255, 0.2);
            font-size: 14px;
            font-style: italic;
            padding: 10px 0;
            font-weight: 300;
        }

        .btn-dashboard {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, #00b894, #00a844);
            color: white;
            text-decoration: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 20px rgba(0, 184, 148, 0.25);
            margin-top: 5px;
        }

        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 184, 148, 0.35);
        }

        .btn-dashboard:active {
            transform: translateY(0) scale(0.98);
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
            .complete-container {
                padding: 30px 25px;
                border-radius: 20px;
                max-width: 100%;
            }

            .complete-container h1 {
                font-size: 26px;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 400px) {
            .complete-container {
                padding: 20px 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

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
                <div class="label"> Total Requests</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo count($feedback_list); ?></div>
                <div class="label"> Feedback Given</div>
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
             Return to Dashboard
        </a>

        <p class="note">
             Thank you for using DeskDeal!
        </p>
    </div>

</body>
</html>