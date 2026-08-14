<?php
session_start();

$error_message = "";
if (isset($_GET["error"])) {
    if ($_GET["error"] == "taken") {
        $error_message = "❌ This request was already taken by another worker!";
    } elseif ($_GET["error"] == "already_applied") {
        $error_message = "❌ You have already applied for this request!";
    } elseif ($_GET["error"] == "self_request") {
        $error_message = "❌ You cannot apply to your own request!";
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
    <title>Available Work - DeskDeal</title>
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
            background: radial-gradient(circle, #4b6aff, #6c5ce7);
        }

        .bg-blob-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, #a8a8ff, #6c5ce7);
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
            color: #a8a8ff;
        }

        .stat-card .label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 300;
            margin-top: 2px;
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

        .request-grid {
            display: grid;
            gap: 20px;
        }

        .request-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 25px 30px;
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-left: 5px solid #6c5ce7;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .request-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
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
            color: rgba(255, 255, 255, 0.9);
        }

        .request-card .status-badge {
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background: rgba(255, 193, 7, 0.15);
            color: #ffd93d;
            border: 1px solid rgba(255, 193, 7, 0.1);
        }

        .request-card .details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin: 12px 0;
            padding: 12px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .request-card .details .item {
            display: flex;
            flex-direction: column;
        }

        .request-card .details .item .label {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 400;
        }

        .request-card .details .item .value {
            font-size: 15px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
        }

        .request-card .description {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
            margin: 10px 0 15px 0;
            line-height: 1.7;
            font-weight: 300;
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
            color: rgba(255, 255, 255, 0.3);
        }

        .request-card .btn-apply {
            display: inline-block;
            padding: 10px 28px;
            background: linear-gradient(135deg, #6c5ce7, #5a4bda);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.25);
        }

        .request-card .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(108, 92, 231, 0.35);
        }

        .no-requests {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 50px 30px;
            border-radius: 18px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .no-requests .emoji {
            font-size: 50px;
            display: block;
            margin-bottom: 15px;
        }

        .no-requests h2 {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .no-requests p {
            color: rgba(255, 255, 255, 0.3);
            font-weight: 300;
        }

        .no-requests a {
            color: #6c5ce7;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        .no-requests a:hover {
            color: #a8a8ff;
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
            margin-top: 20px;
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

            .request-card {
                padding: 20px;
            }

            .request-card .header-row {
                flex-direction: column;
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

            .request-card {
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
                <h1>💼 Available Work</h1>
                <p style="color: rgba(255, 255, 255, 0.3); font-size: 13px; margin-top: 2px; font-weight: 300;">Find assignments to work on</p>
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
            <div class="error-message">
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
                <a href="dashboardS.php">← Back to Dashboard</a>
            </div>
        <?php } ?>

        <div class="footer-note">
            🔒 All pending requests are shown here (including your own for demo purposes).
        </div>

        <div class="demo-note">
            🎯 <strong>Demo Mode:</strong> You can apply to your own requests for demonstration purposes.
        </div>
    </div>

</body>
</html>