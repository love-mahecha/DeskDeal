<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];

$requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];
$applications = isset($_SESSION["applications"]) ? $_SESSION["applications"] : [];

$my_requests = array_filter($requests, function($req) use ($user_email) {
    return $req["buyer_email"] == $user_email;
});

function getApplicationsForRequest($request_id, $applications) {
    $result = [];
    foreach ($applications as $app) {
        if ($app["request_id"] == $request_id) {
            $result[] = $app;
        }
    }
    return $result;
}

function countPendingApplications($request_id, $applications) {
    $count = 0;
    foreach ($applications as $app) {
        if ($app["request_id"] == $request_id && $app["status"] == "pending") {
            $count++;
        }
    }
    return $count;
}

function hasPendingApplications($request_id, $applications) {
    foreach ($applications as $app) {
        if ($app["request_id"] == $request_id && $app["status"] == "pending") {
            return true;
        }
    }
    return false;
}

function isRequestCompleted($request_id, $applications) {
    foreach ($applications as $app) {
        if ($app["request_id"] == $request_id && $app["status"] == "accepted") {
            return true;
        }
    }
    return false;
}

$success_message = "";
if (isset($_GET["accepted"]) && $_GET["accepted"] == "success") {
    $success_message = "✅ Worker accepted successfully! Deal confirmed.";
}
if (isset($_GET["rejected"]) && $_GET["rejected"] == "success") {
    $success_message = "❌ Worker rejected. Request is now open for other workers.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests - DeskDeal</title>
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
            background: radial-gradient(circle, #00a844, #007e33);
        }

        .bg-blob-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, #6c5ce7, #00a844);
        }

        .bg-blob-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, #6bcb77, #00a844);
            opacity: 0.1;
        }

        .container {
            position: relative;
            z-index: 10;
            max-width: 1000px;
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
            background: linear-gradient(135deg, #ffffff, #6bcb77);
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
            color: #00a844;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        .no-requests a:hover {
            color: #6bcb77;
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
            border-left: 5px solid #00a844;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .request-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .request-card.completed {
            border-left-color: rgba(255, 255, 255, 0.2);
            opacity: 0.7;
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
        }

        .request-card .status-badge.pending {
            background: rgba(255, 193, 7, 0.15);
            color: #ffd93d;
            border: 1px solid rgba(255, 193, 7, 0.1);
        }

        .request-card .status-badge.completed {
            background: rgba(40, 167, 69, 0.15);
            color: #6bcb77;
            border: 1px solid rgba(40, 167, 69, 0.1);
        }

        .request-card .status-badge.applied {
            background: rgba(108, 92, 231, 0.15);
            color: #a8a8ff;
            border: 1px solid rgba(108, 92, 231, 0.1);
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
            margin: 10px 0;
            line-height: 1.7;
            font-weight: 300;
        }

        .applications-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .applications-section h4 {
            color: rgba(255, 255, 255, 0.6);
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .application-item {
            background: rgba(255, 255, 255, 0.03);
            padding: 12px 15px;
            border-radius: 12px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        .application-item .worker-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .application-item .worker-info .worker-email {
            font-weight: 600;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .application-item .worker-info .worker-details {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 300;
        }

        .application-item .worker-info .worker-details strong {
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
        }

        .application-item .actions {
            display: flex;
            gap: 8px;
        }

        .application-item .actions .btn-accept {
            padding: 6px 16px;
            background: rgba(40, 167, 69, 0.2);
            color: #6bcb77;
            border: 1px solid rgba(40, 167, 69, 0.2);
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .application-item .actions .btn-accept:hover {
            background: rgba(40, 167, 69, 0.3);
            border-color: rgba(40, 167, 69, 0.3);
        }

        .application-item .actions .btn-reject {
            padding: 6px 16px;
            background: rgba(255, 71, 87, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 71, 87, 0.15);
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .application-item .actions .btn-reject:hover {
            background: rgba(255, 71, 87, 0.25);
            border-color: rgba(255, 71, 87, 0.25);
        }

        .application-item .status-tag {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .application-item .status-tag.pending {
            background: rgba(255, 193, 7, 0.15);
            color: #ffd93d;
            border: 1px solid rgba(255, 193, 7, 0.1);
        }

        .application-item .status-tag.accepted {
            background: rgba(40, 167, 69, 0.15);
            color: #6bcb77;
            border: 1px solid rgba(40, 167, 69, 0.1);
        }

        .application-item .status-tag.rejected {
            background: rgba(255, 71, 87, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 71, 87, 0.1);
        }

        .no-applications {
            color: rgba(255, 255, 255, 0.25);
            font-size: 14px;
            font-style: italic;
            padding: 10px 0;
            font-weight: 300;
        }

        .upload-file-btn {
            display: inline-block;
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            padding: 6px 14px;
            border: 1px solid rgba(255, 107, 107, 0.2);
            border-radius: 8px;
            transition: all 0.3s;
            margin-top: 10px;
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

            .request-card {
                padding: 20px;
            }

            .application-item {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .application-item .actions {
                justify-content: center;
            }
        }

        @media (max-width: 400px) {
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
                <h1>📋 My Requests</h1>
                <p style="color: rgba(255, 255, 255, 0.3); font-size: 13px; margin-top: 2px; font-weight: 300;">View and manage your posted requests</p>
            </div>
            <div class="user-info">
                <span class="user-badge">👤 <?php echo htmlspecialchars($user_email); ?></span>
                <a href="dashboardS.php" class="back-link">← Dashboard</a>
            </div>
        </div>

        <?php if ($success_message != "") { ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php } ?>

        <?php if (count($my_requests) > 0) { ?>
            <div class="request-grid">
                <?php foreach ($my_requests as $request) { 
                    $is_completed = isRequestCompleted($request["id"], $applications);
                    $has_pending = hasPendingApplications($request["id"], $applications);
                    $pending_count = countPendingApplications($request["id"], $applications);
                    $request_apps = getApplicationsForRequest($request["id"], $applications);
                ?>
                    <div class="request-card <?php echo $is_completed ? 'completed' : ''; ?>">
                        <div class="header-row">
                            <span class="subject">📚 <?php echo htmlspecialchars($request["subject"]); ?></span>
                            <span class="status-badge <?php echo $is_completed ? 'completed' : ($has_pending ? 'applied' : 'pending'); ?>">
                                <?php 
                                    if ($is_completed) echo "✅ Completed";
                                    elseif ($has_pending) echo "⏳ {$pending_count} application(s)";
                                    else echo "⏳ Pending";
                                ?>
                            </span>
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

                        <div class="applications-section">
                            <h4>💼 Applications (<?php echo count($request_apps); ?>)</h4>
                            
                            <?php if (count($request_apps) > 0) { ?>
                                <?php foreach ($request_apps as $app) { ?>
                                    <div class="application-item">
                                        <div class="worker-info">
                                            <span class="worker-email">👤 <?php echo htmlspecialchars($app["worker_email"]); ?></span>
                                            <span class="worker-details">
                                                💰 ₹<?php echo number_format($app["price_per_page"], 2); ?> per page • 
                                                Total: <strong>₹<?php echo number_format($app["total_price"], 2); ?></strong> • 
                                                ⏱️ <?php echo htmlspecialchars($app["duration"]); ?>
                                                <?php if (!empty($app["notes"])) { ?>
                                                    <br>📝 Notes: <?php echo htmlspecialchars($app["notes"]); ?>
                                                <?php } ?>
                                            </span>
                                        </div>
                                        
                                        <?php if ($app["status"] == "pending" && !$is_completed) { ?>
                                            <div class="actions">
                                                <a href="review_applicationS.php?action=accept&request_id=<?php echo $request["id"]; ?>&worker_email=<?php echo urlencode($app["worker_email"]); ?>" class="btn-accept">✅ Accept</a>
                                                <a href="review_applicationS.php?action=reject&request_id=<?php echo $request["id"]; ?>&worker_email=<?php echo urlencode($app["worker_email"]); ?>" class="btn-reject">❌ Reject</a>
                                            </div>
                                        <?php } else { ?>
                                            <span class="status-tag <?php echo $app["status"]; ?>">
                                                <?php 
                                                    if ($app["status"] == "accepted") echo "✅ Accepted";
                                                    elseif ($app["status"] == "rejected") echo "❌ Rejected";
                                                    else echo "⏳ Pending";
                                                ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="no-applications">No applications yet. Be patient! 😊</div>
                            <?php } ?>
                        </div>

                        <a href="upload_fileS.php?request_id=<?php echo $request["id"]; ?>" class="upload-file-btn">
                            📎 Upload File for this Request
                        </a>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="no-requests">
                <span class="emoji">📝</span>
                <h2>No Requests Yet</h2>
                <p>You haven't posted any requests. Click "Get Work Done" to get started!</p>
                <br>
                <a href="buyerS.php">📝 Post a Request →</a>
            </div>
        <?php } ?>

        <div class="footer-note">
            🔒 You can accept or reject applications. Accepted deals are confirmed instantly!
        </div>

        <div class="demo-note">
            🎯 <strong>Demo Mode:</strong> You can accept your own applications for demonstration purposes.
        </div>
    </div>

</body>
</html>S