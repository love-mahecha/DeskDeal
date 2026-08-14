<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$request_id = isset($_GET["request_id"]) ? intval($_GET["request_id"]) : 0;
$worker_email = isset($_GET["worker_email"]) ? $_GET["worker_email"] : "";

if ($request_id == 0 || $worker_email == "") {
    header("Location: my_requestsS.php");
    exit();
}

$requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];
$applications = isset($_SESSION["applications"]) ? $_SESSION["applications"] : [];

$request = null;
foreach ($requests as $req) {
    if ($req["id"] == $request_id) {
        $request = $req;
        break;
    }
}

$application = null;
foreach ($applications as $app) {
    if ($app["request_id"] == $request_id && $app["worker_email"] == $worker_email) {
        $application = $app;
        break;
    }
}

if (!$request || !$application) {
    header("Location: my_requestsS.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deal Confirmed - DeskDeal</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            background: #0a0a1a;
            padding: 20px;
            position: relative;
            overflow: hidden;
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
            background: radial-gradient(circle, #ffd93d, #00a844);
            opacity: 0.1;
        }

        .confirmed-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            padding: 45px 45px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 550px;
            text-align: center;
            animation: popIn 0.6s ease-out;
        }

        @keyframes popIn {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .confirmed-container .big-emoji {
            font-size: 70px;
            display: block;
            margin-bottom: 10px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .confirmed-container h1 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #ffd93d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .confirmed-container .subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            font-weight: 300;
            margin-bottom: 20px;
        }

        .confirmed-container .user-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.7);
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 20px;
        }

        .confetti {
            font-size: 30px;
            margin: 5px 0 15px 0;
            letter-spacing: 8px;
        }

        .deal-details {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 16px;
            padding: 20px 25px;
            margin-bottom: 20px;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .deal-details .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .deal-details .row:last-child {
            border-bottom: none;
        }

        .deal-details .row .label {
            color: rgba(255, 255, 255, 0.3);
            font-size: 13px;
            font-weight: 300;
        }

        .deal-details .row .value {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            font-size: 14px;
        }

        .deal-details .row .value.highlight {
            color: #ffd93d;
            font-size: 18px;
            font-weight: 700;
        }

        .deal-details .row .value.worker {
            color: #a8a8ff;
        }

        .divider-line {
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #ffd93d, #00a844);
            border-radius: 2px;
            margin: 15px auto;
        }

        .info-text {
            color: rgba(255, 255, 255, 0.35);
            font-size: 14px;
            margin-bottom: 18px;
            font-weight: 300;
            line-height: 1.6;
        }

        .btn-dashboard {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, #ffd93d, #f9a825);
            color: #1a1a2e;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 20px rgba(255, 217, 61, 0.25);
        }

        .btn-dashboard:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(255, 217, 61, 0.35);
        }

        .btn-dashboard:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-view-requests {
            display: inline-block;
            padding: 12px 30px;
            background: rgba(255, 255, 255, 0.04);
            color: rgba(255, 255, 255, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            margin-top: 10px;
        }

        .btn-view-requests:hover {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.8);
            border-color: rgba(255, 255, 255, 0.15);
        }

        .note {
            margin-top: 20px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.15);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 15px;
            font-weight: 300;
        }

        @media (max-width: 480px) {
            .confirmed-container {
                padding: 30px 20px;
                border-radius: 20px;
            }

            .confirmed-container h1 {
                font-size: 26px;
            }

            .deal-details {
                padding: 15px;
            }

            .deal-details .row {
                flex-direction: column;
                gap: 2px;
            }

            .confetti {
                font-size: 22px;
                letter-spacing: 4px;
            }
        }

        @media (max-width: 400px) {
            .confirmed-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <div class="confirmed-container">
        <span class="big-emoji">🎉</span>
        <h1>Deal Confirmed!</h1>
        <p class="subtitle">Your request has been accepted by a worker</p>

        <div class="user-badge">
            👤 <?php echo htmlspecialchars($user_email); ?>
        </div>

        <div class="confetti"></div>

        <div class="deal-details">
            <div class="row">
                <span class="label">📚 Subject</span>
                <span class="value"><?php echo htmlspecialchars($request["subject"]); ?></span>
            </div>
            <div class="row">
                <span class="label">📄 Pages</span>
                <span class="value"><?php echo htmlspecialchars($request["pages"]); ?></span>
            </div>
            <div class="row">
                <span class="label">📅 Deadline</span>
                <span class="value"><?php echo htmlspecialchars($request["deadline"]); ?></span>
            </div>
            <div class="row">
                <span class="label">👤 Worker</span>
                <span class="value worker"><?php echo htmlspecialchars($application["worker_email"]); ?></span>
            </div>
            <div class="row">
                <span class="label">💰 Price per Page</span>
                <span class="value">₹<?php echo number_format($application["price_per_page"], 2); ?></span>
            </div>
            <div class="row">
                <span class="label">💵 Total Price</span>
                <span class="value highlight">₹<?php echo number_format($application["total_price"], 2); ?></span>
            </div>
            <div class="row">
                <span class="label">⏱️ Duration</span>
                <span class="value"><?php echo htmlspecialchars($application["duration"]); ?></span>
            </div>
            <?php if (!empty($application["notes"])) { ?>
            <div class="row">
                <span class="label">📝 Notes</span>
                <span class="value"><?php echo htmlspecialchars($application["notes"]); ?></span>
            </div>
            <?php } ?>
        </div>

        <div class="divider-line"></div>

        <p class="info-text">
             The worker will now work on your assignment.<br>
            You can contact them via email.
        </p>

        <a href="dashboardS.php" class="btn-dashboard"> Return to Dashboard</a>
        <br>
        <a href="my_requestsS.php" class="btn-view-requests"> View All My Requests</a>

        <p class="note">
             This deal is confirmed. Worker status has been updated to "Accepted".
        </p>
    </div>

</body>
</html>