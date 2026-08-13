<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];

$applications = isset($_SESSION["applications"]) ? $_SESSION["applications"] : [];

$my_applications = array_filter($applications, function($app) use ($user_email) {
    return $app["worker_email"] == $user_email;
});

$pending_count = 0;
$accepted_count = 0;
$rejected_count = 0;

foreach ($my_applications as $app) {
    if ($app["status"] == "pending") $pending_count++;
    elseif ($app["status"] == "accepted") $accepted_count++;
    elseif ($app["status"] == "rejected") $rejected_count++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - DeskDeal</title>
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

        .container {
            position: relative;
            z-index: 10;
            max-width: 900px;
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

        .header h1 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #a8a8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header .user-info {
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
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
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
        }

        .stat-card .number.pending {
            color: #ffd93d;
        }

        .stat-card .number.accepted {
            color: #6bcb77;
        }

        .stat-card .number.rejected {
            color: #ff6b6b;
        }

        .stat-card .label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 300;
            margin-top: 2px;
        }

        .no-applications {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 50px 30px;
            border-radius: 18px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .no-applications .emoji {
            font-size: 50px;
            display: block;
            margin-bottom: 15px;
        }

        .no-applications h2 {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .no-applications p {
            color: rgba(255, 255, 255, 0.3);
            font-weight: 300;
        }

        .no-applications a {
            color: #a8a8ff;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        .no-applications a:hover {
            color: #c8c8ff;
        }

        .application-grid {
            display: grid;
            gap: 15px;
        }

        .application-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 20px 25px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-left: 5px solid #ffd93d;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .application-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .application-card.accepted {
            border-left-color: #6bcb77;
        }

        .application-card.rejected {
            border-left-color: #ff6b6b;
            opacity: 0.6;
        }

        .application-card .info .subject {
            font-size: 18px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.9);
        }

        .application-card .info .details {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.4);
            margin-top: 4px;
            font-weight: 300;
        }

        .application-card .info .details strong {
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
        }

        .application-card .status {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .application-card .status .status-tag {
            padding: 4px 14px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .application-card .status .status-tag.pending {
            background: rgba(255, 193, 7, 0.15);
            color: #ffd93d;
            border: 1px solid rgba(255, 193, 7, 0.1);
        }

        .application-card .status .status-tag.accepted {
            background: rgba(40, 167, 69, 0.15);
            color: #6bcb77;
            border: 1px solid rgba(40, 167, 69, 0.1);
        }

        .application-card .status .status-tag.rejected {
            background: rgba(255, 71, 87, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 71, 87, 0.1);
        }

        .upload-file-btn {
            display: inline-block;
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 500;
            font-size: 12px;
            padding: 4px 12px;
            border: 1px solid rgba(255, 107, 107, 0.2);
            border-radius: 8px;
            transition: all 0.3s;
            margin-top: 6px;
            font-family: 'Inter', sans-serif;
        }

        .upload-file-btn:hover {
            background: rgba(255, 107, 107, 0.1);
            border-color: rgba(255, 107, 107, 0.3);
            color: #ff6b6b;
        }

        .footer-note {
            margin-top: 30px;
            text-align: center;
            color: rgba(255, 255, 255, 0.15);
            font-size: 13px;
            font-weight: 300;
        }

        .demo-note {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 12px;
            color: rgba(255, 255, 255, 0.15);
            font-weight: 300;
        }

        @media (max-width: 600px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .header .user-info {
                flex-wrap: wrap;
                justify-content: center;
            }

            .application-card {
                flex-direction: column;
                text-align: center;
                padding: 18px 20px;
            }

            .application-card .status {
                justify-content: center;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 400px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 15px 18px;
            }

            .application-card {
                padding: 15px;
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
            <div>
                <h1>💼 My Applications</h1>
                <p style="color: rgba(255, 255, 255, 0.3); font-size: 13px; margin-top: 2px; font-weight: 300;">Track all your submitted applications</p>
            </div>
            <div class="user-info">
                <span class="user-badge">👤 <?php echo htmlspecialchars($user_email); ?></span>
                <a href="dashboardS.php" class="back-link">← Dashboard</a>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="number pending"><?php echo $pending_count; ?></div>
                <div class="label">⏳ Pending</div>
            </div>
            <div class="stat-card">
                <div class="number accepted"><?php echo $accepted_count; ?></div>
                <div class="label">✅ Accepted</div>
            </div>
            <div class="stat-card">
                <div class="number rejected"><?php echo $rejected_count; ?></div>
                <div class="label">❌ Rejected</div>
            </div>
        </div>

        <?php if (count($my_applications) > 0) { ?>
            <div class="application-grid">
                <?php foreach ($my_applications as $app) { ?>
                    <div class="application-card <?php echo $app["status"]; ?>">
                        <div class="info">
                            <div class="subject">📚 <?php echo htmlspecialchars($app["request_subject"]); ?></div>
                            <div class="details">
                                Request #<?php echo $app["request_id"]; ?> • 
                                💰 ₹<?php echo number_format($app["price_per_page"], 2); ?> per page • 
                                Total: <strong>₹<?php echo number_format($app["total_price"], 2); ?></strong> • 
                                ⏱️ <?php echo htmlspecialchars($app["duration"]); ?>
                                <?php if (!empty($app["notes"])) { ?>
                                    <br>📝 Notes: <?php echo htmlspecialchars($app["notes"]); ?>
                                <?php } ?>
                            </div>
                            <a href="upload_fileS.php?request_id=<?php echo $app["request_id"]; ?>" class="upload-file-btn">
                                📎 Upload File
                            </a>
                        </div>
                        <div class="status">
                            <span class="status-tag <?php echo $app["status"]; ?>">
                                <?php 
                                    if ($app["status"] == "accepted") echo "✅ Accepted";
                                    elseif ($app["status"] == "rejected") echo "❌ Rejected";
                                    else echo "⏳ Pending";
                                ?>
                            </span>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="no-applications">
                <span class="emoji">🔍</span>
                <h2>No Applications Yet</h2>
                <p>You haven't applied to any requests yet. Check out available work!</p>
                <br>
                <a href="workerS.php">🔍 Find Work →</a>
            </div>
        <?php } ?>

        <div class="footer-note">
            🔒 Track your applications and see if you've been accepted or rejected.
        </div>

        <div class="demo-note">
            🎯 <strong>Demo Mode:</strong> You can apply to your own requests for demonstration purposes.
        </div>
    </div>

</body>
</html>