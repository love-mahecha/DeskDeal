<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$emails = isset($_SESSION["email_log"]) ? $_SESSION["email_log"] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Log - DeskDeal</title>
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
            background: #0a0a1a;
            padding: 30px 20px;
            position: relative;
            overflow-x: hidden;
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
            background: radial-gradient(circle, #ffc107, #f9a825);
        }

        .bg-blob-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, #6c5ce7, #ffc107);
        }

        .bg-blob-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, #ffd93d, #ffc107);
            opacity: 0.1;
        }

        .container {
            position: relative;
            z-index: 10;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            padding: 20px 30px;
            border-radius: 20px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .header .left h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #ffd93d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header .left p {
            color: rgba(255, 255, 255, 0.3);
            font-size: 13px;
            margin-top: 2px;
            font-weight: 300;
        }

        .header .right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header .user-badge {
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.7);
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 13px;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .header .back-link {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            padding: 6px 16px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 8px;
            transition: all 0.3s;
        }

        .header .back-link:hover {
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.8);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 18px 20px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.06);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 700;
            color: #ffd93d;
        }

        .stat-card .label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 300;
            margin-top: 2px;
        }

        .email-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 20px 25px;
            border-radius: 18px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-left: 5px solid #ffc107;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .email-card:hover {
            transform: translateX(5px);
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .email-card.type-apply {
            border-left-color: #ffc107;
        }

        .email-card.type-accept {
            border-left-color: #6bcb77;
        }

        .email-card.type-reject {
            border-left-color: #ff6b6b;
        }

        .email-card.type-confirm {
            border-left-color: #4bc0c0;
        }

        .email-card .subject {
            font-size: 16px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 3px;
        }

        .email-card .to {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.4);
            margin-bottom: 8px;
            font-weight: 300;
        }

        .email-card .to strong {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        .email-card .message {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            margin: 8px 0;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            line-height: 1.8;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-weight: 300;
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        .email-card .time {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.2);
            margin-top: 5px;
            text-align: right;
            font-weight: 300;
        }

        .no-emails {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 50px 30px;
            border-radius: 18px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .no-emails .emoji {
            font-size: 60px;
            display: block;
            margin-bottom: 15px;
        }

        .no-emails h2 {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .no-emails p {
            color: rgba(255, 255, 255, 0.3);
            font-size: 15px;
            font-weight: 300;
        }

        .no-emails .info-list {
            margin-top: 10px;
            color: rgba(255, 255, 255, 0.2);
            font-size: 13px;
            font-weight: 300;
            line-height: 1.8;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: rgba(255, 255, 255, 0.15);
            font-size: 13px;
            font-weight: 300;
        }

        @media (max-width: 600px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .header .right {
                flex-wrap: wrap;
                justify-content: center;
            }

            .email-card {
                padding: 15px 18px;
            }

            .email-card .message {
                font-size: 13px;
                padding: 10px;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 400px) {
            .header {
                padding: 15px 18px;
            }

            .email-card {
                padding: 12px 15px;
            }

            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <div class="container">

        <div class="header">
            <div class="left">
                <h1>📧 Email Log</h1>
                <p>All notifications sent</p>
            </div>
            <div class="right">
                <span class="user-badge">👤 <?php echo htmlspecialchars($user_email); ?></span>
                <a href="dashboardS.php" class="back-link">← Dashboard</a>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="number"><?php echo count($emails); ?></div>
                <div class="label">📧 Total Emails</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php 
                    $unread = 0;
                    foreach ($emails as $email) {
                        if (!isset($email["read"]) || !$email["read"]) $unread++;
                    }
                    echo $unread;
                ?></div>
                <div class="label">📬 Unread</div>
            </div>
        </div>

        <?php if (count($emails) > 0) { ?>
            <?php foreach (array_reverse($emails) as $email) { 
                $type = "type-apply";
                if (strpos($email["subject"], "accepted") !== false || strpos($email["subject"], "Accepted") !== false) {
                    $type = "type-accept";
                } elseif (strpos($email["subject"], "rejected") !== false || strpos($email["subject"], "Rejected") !== false) {
                    $type = "type-reject";
                } elseif (strpos($email["subject"], "confirmed") !== false || strpos($email["subject"], "Confirmed") !== false) {
                    $type = "type-confirm";
                }
            ?>
                <div class="email-card <?php echo $type; ?>">
                    <div class="subject">📧 <?php echo htmlspecialchars($email["subject"]); ?></div>
                    <div class="to">To: <strong><?php echo htmlspecialchars($email["to"]); ?></strong></div>
                    <div class="message"><?php echo nl2br(htmlspecialchars($email["message"])); ?></div>
                    <div class="time">🕐 <?php echo htmlspecialchars($email["time"]); ?></div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="no-emails">
                <span class="emoji">📭</span>
                <h2>No Emails Yet</h2>
                <p>Emails will appear here when actions happen:</p>
                <div class="info-list">
                    📩 Worker applies → Email to buyer<br>
                    ✅ Buyer accepts → Email to worker<br>
                    ❌ Buyer rejects → Email to worker
                </div>
            </div>
        <?php } ?>

        <div class="footer">
            🔒 Emails are stored in your session and will be cleared when you logout.
        </div>
    </div>

</body>
</html>