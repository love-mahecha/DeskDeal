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
            max-width: 900px;
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 24px;
            color: #1a1a2e;
        }

        .header h1 span {
            color: #00a844;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header .user-badge {
            background: #e8f5e9;
            color: #008736;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
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
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 700;
        }

        .stat-card .number.pending { color: #ffc107; }
        .stat-card .number.accepted { color: #28a745; }
        .stat-card .number.rejected { color: #dc3545; }

        .stat-card .label {
            font-size: 13px;
            color: #666;
        }

        .no-applications {
            background: white;
            padding: 50px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .no-applications .emoji {
            font-size: 50px;
            display: block;
            margin-bottom: 15px;
        }

        .no-applications h2 {
            color: #1a1a2e;
            margin-bottom: 10px;
        }

        .no-applications p {
            color: #666;
        }

        .no-applications a {
            color: #00a844;
            font-weight: 600;
            text-decoration: none;
        }

        .no-applications a:hover {
            text-decoration: underline;
        }

        .application-grid {
            display: grid;
            gap: 15px;
        }

        .application-card {
            background: white;
            padding: 20px 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #ffc107;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .application-card.accepted {
            border-left-color: #28a745;
        }

        .application-card.rejected {
            border-left-color: #dc3545;
            opacity: 0.7;
        }

        .application-card .info .subject {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .application-card .info .details {
            font-size: 14px;
            color: #666;
            margin-top: 3px;
        }

        .application-card .info .details strong {
            color: #1a1a2e;
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
            font-size: 13px;
            font-weight: 600;
        }

        .application-card .status .status-tag.pending {
            background: #fff3cd;
            color: #856404;
        }

        .application-card .status .status-tag.accepted {
            background: #d4edda;
            color: #155724;
        }

        .application-card .status .status-tag.rejected {
            background: #f8d7da;
            color: #721c24;
        }

       
        .upload-file-btn {
            display: inline-block;
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            padding: 4px 12px;
            border: 2px solid #ff6b6b;
            border-radius: 6px;
            transition: all 0.3s;
            margin-top: 5px;
        }

        .upload-file-btn:hover {
            background: #ff6b6b;
            color: white;
        }

        .footer-note {
            margin-top: 30px;
            text-align: center;
            color: rgba(255,255,255,0.7);
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .header {
                flex-direction: column;
                text-align: center;
            }

            .application-card {
                flex-direction: column;
                text-align: center;
            }

            .application-card .status {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="header">
            <div>
                <h1>💼 My <span>Applications</span></h1>
                <p style="color: #666; font-size: 14px; margin-top: 3px;">Track all your submitted applications</p>
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
                                💰 $<?php echo number_format($app["price_per_page"], 2); ?> per page • 
                                Total: <strong>$<?php echo number_format($app["total_price"], 2); ?></strong> • 
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
             Track your applications and see if you've been accepted or rejected.
        </div>
    </div>

</body>
</html>