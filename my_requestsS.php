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
            max-width: 1000px;
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

        .no-requests a {
            color: #00a844;
            font-weight: 600;
            text-decoration: none;
        }

        .no-requests a:hover {
            text-decoration: underline;
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
            border-left: 5px solid #00a844;
        }

        .request-card.completed {
            border-left-color: #6c757d;
            opacity: 0.8;
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
        }

        .request-card .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .request-card .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }

        .request-card .status-badge.applied {
            background: #cce5ff;
            color: #004085;
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
            margin: 10px 0;
            line-height: 1.6;
        }

       
        .applications-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }

        .applications-section h4 {
            color: #1a1a2e;
            font-size: 15px;
            margin-bottom: 10px;
        }

        .application-item {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .application-item .worker-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
        }

        .application-item .worker-info .worker-email {
            font-weight: 600;
            color: #1a1a2e;
            font-size: 14px;
        }

        .application-item .worker-info .worker-details {
            font-size: 13px;
            color: #666;
        }

        .application-item .worker-info .worker-details strong {
            color: #1a1a2e;
        }

        .application-item .actions {
            display: flex;
            gap: 8px;
        }

        .application-item .actions .btn-accept {
            padding: 6px 16px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.3s;
        }

        .application-item .actions .btn-accept:hover {
            background: #218838;
        }

        .application-item .actions .btn-reject {
            padding: 6px 16px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            transition: background 0.3s;
        }

        .application-item .actions .btn-reject:hover {
            background: #c82333;
        }

        .application-item .status-tag {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .application-item .status-tag.pending {
            background: #fff3cd;
            color: #856404;
        }

        .application-item .status-tag.accepted {
            background: #d4edda;
            color: #155724;
        }

        .application-item .status-tag.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .no-applications {
            color: #999;
            font-size: 14px;
            font-style: italic;
            padding: 10px 0;
        }

       
        .upload-file-btn {
            display: inline-block;
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 6px 14px;
            border: 2px solid #ff6b6b;
            border-radius: 8px;
            transition: all 0.3s;
            margin-top: 10px;
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

            .application-item {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .application-item .actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="header">
            <div>
                <h1>📋 My <span>Requests</span></h1>
                <p style="color: #666; font-size: 14px; margin-top: 3px;">View and manage your posted requests</p>
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
             You can accept or reject applications. Accepted deals are confirmed instantly!
        </div>
    </div>

</body>
</html>

