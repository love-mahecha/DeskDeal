<?php
session_start();

// If not logged in, redirect to login
if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];

// Get all requests
$requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];
$applications = isset($_SESSION["applications"]) ? $_SESSION["applications"] : [];

// Get only completed requests (where buyer is this user OR worker is this user)
$completed_requests = [];

foreach ($requests as $request) {
    if ($request["status"] == "completed") {
        // Check if this user is the buyer OR worker
        $is_buyer = ($request["buyer_email"] == $user_email);
        $is_worker = false;
        
        foreach ($applications as $app) {
            if ($app["request_id"] == $request["id"] && $app["status"] == "accepted" && $app["worker_email"] == $user_email) {
                $is_worker = true;
                break;
            }
        }
        
        if ($is_buyer || $is_worker) {
            $completed_requests[] = $request;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Deals - DeskDeal</title>
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
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: 700;
            color: #28a745;
        }

        .stat-card .label {
            font-size: 13px;
            color: #666;
        }

        .request-card {
            background: white;
            padding: 20px 25px;
            border-radius: 15px;
            margin-bottom: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            border-left: 5px solid #28a745;
        }

        .request-card .subject {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
        }

        .request-card .details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin: 10px 0;
            padding: 10px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .request-card .details .item .label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
        }

        .request-card .details .item .value {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .request-card .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            background: #d4edda;
            color: #155724;
        }

        .request-card .role-tag {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-left: 10px;
        }

        .request-card .role-tag.buyer {
            background: #ffe0e0;
            color: #e63946;
        }

        .request-card .role-tag.worker {
            background: #e0e0ff;
            color: #4b6aff;
        }

        .no-completed {
            background: white;
            padding: 50px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .no-completed .emoji {
            font-size: 50px;
            display: block;
            margin-bottom: 15px;
        }

        .no-completed h2 {
            color: #1a1a2e;
            margin-bottom: 10px;
        }

        .no-completed p {
            color: #666;
        }

        .footer {
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
                <h1>✅ <span>Completed Deals</span></h1>
                <p style="color: #666; font-size: 14px; margin-top: 3px;">All your successfully completed deals</p>
            </div>
            <div>
                <span class="user-badge">👤 <?php echo htmlspecialchars($user_email); ?></span>
                <a href="dashboardS.php" class="back-link">← Dashboard</a>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="number"><?php echo count($completed_requests); ?></div>
                <div class="label">✅ Completed Deals</div>
            </div>
        </div>

        <?php if (count($completed_requests) > 0) { ?>
            <?php foreach ($completed_requests as $request) { 
                // Get the accepted worker
                $worker_email = "";
                foreach ($applications as $app) {
                    if ($app["request_id"] == $request["id"] && $app["status"] == "accepted") {
                        $worker_email = $app["worker_email"];
                        break;
                    }
                }
                
                $is_buyer = ($request["buyer_email"] == $user_email);
            ?>
                <div class="request-card">
                    <div>
                        <span class="subject">📚 <?php echo htmlspecialchars($request["subject"]); ?></span>
                        <span class="status">✅ Completed</span>
                        <span class="role-tag <?php echo $is_buyer ? 'buyer' : 'worker'; ?>">
                            <?php echo $is_buyer ? 'You posted this' : 'You worked on this'; ?>
                        </span>
                    </div>

                    <div class="details">
                        <div class="item">
                            <div class="label">📄 Pages</div>
                            <div class="value"><?php echo htmlspecialchars($request["pages"]); ?></div>
                        </div>
                        <div class="item">
                            <div class="label">📅 Deadline</div>
                            <div class="value"><?php echo htmlspecialchars($request["deadline"]); ?></div>
                        </div>
                        <div class="item">
                            <div class="label">👤 <?php echo $is_buyer ? 'Worker' : 'Buyer'; ?></div>
                            <div class="value"><?php echo $is_buyer ? htmlspecialchars($worker_email) : htmlspecialchars($request["buyer_email"]); ?></div>
                        </div>
                    </div>

                    <div style="margin-top: 10px; display: flex; gap: 10px; flex-wrap: wrap;">
                        <a href="upload_fileS.php?request_id=<?php echo $request["id"]; ?>" style="color: #00a844; text-decoration: none; font-weight: 600; font-size: 13px;">
                            📎 View Files
                        </a>
                        <span style="color: #999; font-size: 13px;">|</span>
                        <span style="color: #999; font-size: 13px;">🕐 Completed on: <?php echo date("M d, Y", strtotime($request["created_at"] ?? date("Y-m-d"))); ?></span>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="no-completed">
                <span class="emoji">📭</span>
                <h2>No Completed Deals Yet</h2>
                <p>Complete a deal by posting a request, applying, and accepting a worker!</p>
                <br>
                <a href="buyerS.php" style="color: #00a844; font-weight: 600; text-decoration: none;">📝 Post a Request →</a>
                <span style="color: #ccc; margin: 0 10px;">or</span>
                <a href="workerS.php" style="color: #6c5ce7; font-weight: 600; text-decoration: none;">💼 Find Work →</a>
            </div>
        <?php } ?>

        <div class="footer">
            🔒 Completed deals are stored in your session and will be cleared on logout.
        </div>
    </div>

</body>
</html>