<?php
session_start();



$error_message = "";
if (isset($_GET["error"])) {
    if ($_GET["error"] == "taken") {
        $error_message = "❌ This request was already taken by another worker!";
    } elseif ($_GET["error"] == "already_applied") {
        $error_message = "❌ You have already applied for this request!";
    }
}






if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];


$requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];


$pending_requests = array_filter($requests, function($req) {
    return $req["status"] == "pending";
});


$success_message = "";
if (isset($_GET["applied"]) && $_GET["applied"] == "success") {
    $success_message = "✅ You have successfully applied for this work!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Work - Student Work</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #4b6aff 0%, #2d3b8f 100%);
            padding: 30px 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 25px 30px;
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
            color: #4b6aff;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header .user-badge {
            background: #e8f0fe;
            color: #4b6aff;
            padding: 8px 18px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        .header .back-link {
            color: #4b6aff;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 18px;
            border: 2px solid #4b6aff;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .header .back-link:hover {
            background: #4b6aff;
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
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 700;
            color: #4b6aff;
        }

        .stat-card .label {
            font-size: 13px;
            color: #666;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            border-left: 4px solid #28a745;
        }

        .no-requests {
            background: white;
            padding: 50px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .no-requests .emoji {
            font-size: 50px;
            display: block;
            margin-bottom: 15px;
        }

        .no-requests h2 {
            color: #1a1a2e;
            margin-bottom: 10px;
        }

        .no-requests p {
            color: #666;
        }

       
        .request-grid {
            display: grid;
            gap: 20px;
        }

        .request-card {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #4b6aff;
            transition: transform 0.2s;
        }

        .request-card:hover {
            transform: translateY(-3px);
        }

        .request-card .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }

        .request-card .subject {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .request-card .status-badge {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #fff3cd;
            color: #856404;
        }

        .request-card .details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin: 12px 0;
            padding: 12px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .request-card .details .item {
            display: flex;
            flex-direction: column;
        }

        .request-card .details .item .label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .request-card .details .item .value {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .request-card .description {
            color: #555;
            font-size: 14px;
            margin: 10px 0 15px 0;
            line-height: 1.6;
        }

        .request-card .btn-apply {
            display: inline-block;
            padding: 10px 25px;
            background: linear-gradient(135deg, #4b6aff, #3a56d4);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .request-card .btn-apply:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(75, 106, 255, 0.3);
        }

        .request-card .meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .request-card .meta .posted-by {
            font-size: 13px;
            color: #888;
        }

        .footer-note {
            margin-top: 30px;
            text-align: center;
            color: rgba(255,255,255,0.7);
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="header">
            <div>
                <h1>💼 <span>Available Work</span></h1>
                <p style="color: #666; font-size: 14px; margin-top: 3px;">Find assignments to work on</p>
            </div>
            <div class="user-info">
                <span class="user-badge">👤 <?php echo htmlspecialchars($user_email); ?></span>
                <a href="dashboardS.php" class="back-link">← Dashboard</a>
            </div>
        </div>

        
        <div class="stats">
            <div class="stat-card">
                <div class="number"><?php echo count($pending_requests); ?></div>
                <div class="label">Available Requests</div>
            </div>
            <div class="stat-card">
                <div class="number"><?php echo count($requests); ?></div>
                <div class="label">Total Requests</div>
            </div>
        </div>


        <?php if ($error_message != "") { ?>
    <div class="error-message" style="background: #f8d7da; color: #721c24; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; border-left: 4px solid #dc3545;">
        <?php echo $error_message; ?>
    </div>
<?php } ?>


        
        <?php if ($success_message != "") { ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php } ?>

        
        <?php if (count($pending_requests) > 0) { ?>
            <div class="request-grid">
                <?php foreach ($pending_requests as $request) { ?>
                    <div class="request-card">
                        <div class="header-row">
                            <span class="subject">📚 <?php echo htmlspecialchars($request["subject"]); ?></span>
                            <span class="status-badge">⏳ Pending</span>
                        </div>

                        <div class="details">
                            <div class="item">
                                <span class="label">📄 Pages</span>
                                <span class="value"><?php echo htmlspecialchars($request["pages"]); ?></span>
                            </div>
                            <div class="item">
                                <span class="label">📅 Deadline</span>
                                <span class="value"><?php echo htmlspecialchars($request["deadline"]); ?></span>
                            </div>
                            <div class="item">
                                <span class="label">📝 Request ID</span>
                                <span class="value">#<?php echo htmlspecialchars($request["id"]); ?></span>
                            </div>
                        </div>

                        <div class="description">
                            <?php echo htmlspecialchars($request["description"]); ?>
                        </div>

                        <div class="meta">
                            <span class="posted-by">Posted by: <?php echo htmlspecialchars($request["buyer_email"]); ?></span>
                            <a href="applyS.php?request_id=<?php echo $request["id"]; ?>" class="btn-apply">
                                💰 Apply to Work
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="no-requests">
                <span class="emoji">🎉</span>
                <h2>No Pending Requests</h2>
                <p>All caught up! Check back later for new assignments.</p>
                <br>
                <a href="dashboardS.php" style="color: #4b6aff; font-weight: 600; text-decoration: none;">← Back to Dashboard</a>
            </div>
        <?php } ?>

        <div class="footer-note">
             Only pending requests are shown here. Applied requests are hidden.
        </div>
    </div>

</body>
</html>