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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #004d1a, #00a844);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

       
        body::before {
            content: '';
            position: absolute;
            width: 1200px;
            height: 1200px;
            top: -400px;
            left: -300px;
            border-radius: 45%;
            background: linear-gradient(135deg, #00e676 0%, #00a844 70%);
            z-index: 1;
            animation: rotateWave 25s infinite linear;
        }

        body::after {
            content: '';
            position: absolute;
            width: 1000px;
            height: 1000px;
            bottom: -300px;
            right: -200px;
            border-radius: 40%;
            background: linear-gradient(135deg, #00c853 0%, #007e33 80%);
            z-index: 1;
            animation: rotateWave 20s infinite linear reverse;
        }

        @keyframes rotateWave {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .confirmed-container {
            position: relative;
            z-index: 10;
            background: white;
            padding: 45px 40px;
            border-radius: 25px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 550px;
            text-align: center;
            animation: popIn 0.6s ease-out;
        }

        @keyframes popIn {
            0% { transform: scale(0.8); opacity: 0; }
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
            color: #1a1a2e;
            margin-bottom: 5px;
        }

        .confirmed-container h1 span {
            color: #00a844;
        }

        .confirmed-container .subtitle {
            color: #666;
            font-size: 15px;
            margin-bottom: 25px;
        }

        .confirmed-container .user-badge {
            display: inline-block;
            background: #e8f5e9;
            color: #008736;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        
        .deal-details {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px 25px;
            margin-bottom: 20px;
            text-align: left;
        }

        .deal-details .row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .deal-details .row:last-child {
            border-bottom: none;
        }

        .deal-details .row .label {
            color: #888;
            font-size: 14px;
        }

        .deal-details .row .value {
            color: #1a1a2e;
            font-weight: 600;
            font-size: 14px;
        }

        .deal-details .row .value.highlight {
            color: #00a844;
            font-size: 18px;
        }

        .deal-details .row .value.worker {
            color: #6c5ce7;
        }

        .divider-line {
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #00e676, #00a844);
            border-radius: 2px;
            margin: 15px auto;
        }

        .btn-dashboard {
            display: inline-block;
            padding: 14px 40px;
            background: linear-gradient(135deg, #00a844, #007e33);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 5px;
        }

        .btn-dashboard:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 168, 68, 0.3);
        }

        .btn-view-requests {
            display: inline-block;
            padding: 12px 30px;
            background: transparent;
            color: #00a844;
            border: 2px solid #00a844;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-view-requests:hover {
            background: #00a844;
            color: white;
        }

        .note {
            margin-top: 20px;
            font-size: 13px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .confetti {
            font-size: 30px;
            margin: 5px 0;
        }

        
        @media (max-width: 480px) {
            .confirmed-container {
                padding: 30px 20px;
            }

            .deal-details {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <div class="confirmed-container">
        <span class="big-emoji">🎉</span>
        <h1>Deal <span>Confirmed!</span></h1>
        <p class="subtitle">Your request has been accepted by a worker</p>

        <div class="user-badge">
            👤 <?php echo htmlspecialchars($user_email); ?>
        </div>

        <div class="confetti">🎊 🥳 🎉 💫 ✨</div>

        <!-- DEAL DETAILS -->
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

        <p style="color: #666; font-size: 14px; margin-bottom: 15px;">
            The worker will now work on your assignment. 
            <br>You can contact them via email.
        </p>

        <a href="dashboardS.php" class="btn-dashboard">🏠 Return to Dashboard</a>
        <br>
        <a href="my_requestsS.php" class="btn-view-requests">📋 View All My Requests</a>

        <p class="note">
             This deal is confirmedjhuj. Worker status has been updated to "Accepted".
        </p>
    </div>

</body>
</html>

