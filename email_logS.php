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
    <style>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #004d1a, #00a844);
            padding: 30px 20px;
        }

       
        .container {
            max-width: 800px;
            margin: 0 auto;
        }

       
        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .header .left h1 {
            font-size: 24px;
            color: #1a1a2e;
        }

        .header .left h1 span {
            color: #00a844;
        }

        .header .left p {
            color: #666;
            font-size: 14px;
            margin-top: 3px;
        }

        .header .right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header .user-badge {
            background: #e8f5e9;
            color: #008736;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .header .back-link {
            color: #00a844;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 18px;
            border: 2px solid #00a844;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .header .back-link:hover {
            background: #00a844;
            color: white;
        }

        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 700;
            color: #00a844;
        }

        .stat-card .label {
            font-size: 13px;
            color: #666;
        }

        
        .email-card {
            background: white;
            padding: 20px 25px;
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #00a844;
            transition: transform 0.2s;
        }

        .email-card:hover {
            transform: translateX(5px);
        }

       
        .email-card.type-apply {
            border-left-color: #ffc107;
        }

        .email-card.type-accept {
            border-left-color: #28a745;
        }

        .email-card.type-reject {
            border-left-color: #dc3545;
        }

        .email-card.type-confirm {
            border-left-color: #17a2b8;
        }

       
        .email-card .subject {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 3px;
        }

    
        .email-card .to {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
        }

        .email-card .to strong {
            color: #1a1a2e;
        }

        
        .email-card .message {
            font-size: 14px;
            color: #444;
            margin: 8px 0;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            line-height: 1.8;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        
        .email-card .time {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
            text-align: right;
        }

       
        .no-emails {
            background: white;
            padding: 50px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .no-emails .emoji {
            font-size: 60px;
            display: block;
            margin-bottom: 15px;
        }

        .no-emails h2 {
            color: #1a1a2e;
            margin-bottom: 10px;
        }

        .no-emails p {
            color: #666;
            font-size: 15px;
        }

        
        .footer {
            margin-top: 30px;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
        }

        
        .btn-clear {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-clear:hover {
            background: #c82333;
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
    </style>
</head>
<body>

    <div class="container">
        
        <div class="header">
            <div class="left">
                <h1>📧 Email <span>Log</span></h1>
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
                // Determine email type for styling
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
                <p style="margin-top: 5px; color: #888; font-size: 13px;">
                    📩 Worker applies → Email to buyer<br>
                    ✅ Buyer accepts → Email to worker<br>
                    ❌ Buyer rejects → Email to worker
                </p>
            </div>
        <?php } ?>

       
        <div class="footer">
             Emails are stored in your session and will be cleared when you logout.
        </div>
    </div>

</body>
</html>