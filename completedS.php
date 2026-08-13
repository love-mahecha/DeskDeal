<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];

$requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];
$applications = isset($_SESSION["applications"]) ? $_SESSION["applications"] : [];

$completed_requests = [];

foreach ($requests as $request) {
    if ($request["status"] == "completed") {
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
            background: radial-gradient(circle, #28a745, #00a844);
        }

        .bg-blob-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, #6c5ce7, #28a745);
        }

        .bg-blob-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, #6bcb77, #28a745);
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
            color: #6bcb77;
        }

        .stat-card .label {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 300;
            margin-top: 2px;
        }

        .request-card {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 22px 28px;
            border-radius: 18px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-left: 5px solid #28a745;
            transition: all 0.3s;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .request-card:hover {
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
        }

        .request-card .top-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .request-card .subject {
            font-size: 18px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.9);
        }

        .request-card .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            background: rgba(40, 167, 69, 0.15);
            color: #6bcb77;
            border: 1px solid rgba(40, 167, 69, 0.1);
        }

        .request-card .role-tag {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .request-card .role-tag.buyer {
            background: rgba(255, 107, 107, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 107, 107, 0.1);
        }

        .request-card .role-tag.worker {
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

        .request-card .actions {
            margin-top: 10px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .request-card .actions .view-files {
            color: #6bcb77;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            transition: all 0.3s;
        }

        .request-card .actions .view-files:hover {
            color: #a8e6a8;
        }

        .request-card .actions .completed-date {
            color: rgba(255, 255, 255, 0.2);
            font-size: 13px;
            font-weight: 300;
        }

        .request-card .actions .separator {
            color: rgba(255, 255, 255, 0.1);
        }

        .no-completed {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 50px 30px;
            border-radius: 18px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .no-completed .emoji {
            font-size: 50px;
            display: block;
            margin-bottom: 15px;
        }

        .no-completed h2 {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .no-completed p {
            color: rgba(255, 255, 255, 0.3);
            font-weight: 300;
        }

        .no-completed .links {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .no-completed .links a {
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
        }

        .no-completed .links .buyer-link {
            color: #ff6b6b;
        }

        .no-completed .links .buyer-link:hover {
            color: #ff8a8a;
        }

        .no-completed .links .worker-link {
            color: #a8a8ff;
        }

        .no-completed .links .worker-link:hover {
            color: #c8c8ff;
        }

        .no-completed .links .separator {
            color: rgba(255, 255, 255, 0.1);
        }

        .footer-note {
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

            .header .user-info {
                flex-wrap: wrap;
                justify-content: center;
            }

            .request-card {
                padding: 18px 20px;
            }

            .request-card .top-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats {
                grid-template-columns: 1fr 1fr;
            }

            .no-completed .links {
                flex-direction: column;
                gap: 10px;
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
                <h1>✅ Completed Deals</h1>
                <p style="color: rgba(255, 255, 255, 0.3); font-size: 13px; margin-top: 2px; font-weight: 300;">All your successfully completed deals</p>
            </div>
            <div class="user-info">
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
                    <div class="top-row">
                        <span class="subject">📚 <?php echo htmlspecialchars($request["subject"]); ?></span>
                        <span class="status">✅ Completed</span>
                        <span class="role-tag <?php echo $is_buyer ? 'buyer' : 'worker'; ?>">
                            <?php echo $is_buyer ? '📤 You posted this' : '📥 You worked on this'; ?>
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

                    <div class="actions">
                        <a href="upload_fileS.php?request_id=<?php echo $request["id"]; ?>" class="view-files">📎 View Files</a>
                        <span class="separator">|</span>
                        <span class="completed-date">🕐 Completed on: <?php echo date("M d, Y", strtotime($request["created_at"] ?? date("Y-m-d"))); ?></span>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="no-completed">
                <span class="emoji">📭</span>
                <h2>No Completed Deals Yet</h2>
                <p>Complete a deal by posting a request, applying, and accepting a worker!</p>
                <div class="links">
                    <a href="buyerS.php" class="buyer-link">📝 Post a Request →</a>
                    <span class="separator">|</span>
                    <a href="workerS.php" class="worker-link">💼 Find Work →</a>
                </div>
            </div>
        <?php } ?>

        <div class="footer-note">
            🔒 Completed deals are stored in your session and will be cleared on logout.
        </div>

    </div>

</body>
</html>