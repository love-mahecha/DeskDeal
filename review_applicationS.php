<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$request_id = isset($_GET["request_id"]) ? intval($_GET["request_id"]) : 0;
$action = isset($_GET["action"]) ? $_GET["action"] : "";
$worker_email = isset($_GET["worker_email"]) ? $_GET["worker_email"] : "";

if ($action == "" || $request_id == 0 || $worker_email == "") {
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

if (!$request) {
    header("Location: my_requestsS.php");
    exit();
}

if ($request["buyer_email"] != $user_email) {
    header("Location: my_requestsS.php");
    exit();
}

if ($action == "accept") {
    foreach ($_SESSION["applications"] as &$app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] == $worker_email) {
            $app["status"] = "accepted";
        }
    }
    
    foreach ($_SESSION["applications"] as &$app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] != $worker_email && $app["status"] == "pending") {
            $app["status"] = "rejected";
        }
    }
    
    foreach ($_SESSION["requests"] as &$req) {
        if ($req["id"] == $request_id) {
            $req["status"] = "completed";
            break;
        }
    }
    
    header("Location: deal_confirmedS.php?request_id=" . $request_id . "&worker_email=" . urlencode($worker_email));
    exit();
    
} elseif ($action == "reject") {
    foreach ($_SESSION["applications"] as &$app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] == $worker_email) {
            $app["status"] = "rejected";
            break;
        }
    }
    
    $has_pending = false;
    foreach ($_SESSION["applications"] as $app) {
        if ($app["request_id"] == $request_id && $app["status"] == "pending") {
            $has_pending = true;
            break;
        }
    }
    
    if (!$has_pending) {
        foreach ($_SESSION["requests"] as &$req) {
            if ($req["id"] == $request_id) {
                $req["status"] = "pending";
                break;
            }
        }
    }
    
    header("Location: my_requestsS.php?rejected=success");
    exit();
    
} else {
    header("Location: my_requestsS.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing - DeskDeal</title>
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

        .processing-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            padding: 50px 45px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .spinner {
            width: 60px;
            height: 60px;
            margin: 0 auto 25px auto;
            border: 4px solid rgba(255, 255, 255, 0.05);
            border-top-color: #6c5ce7;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .processing-container .status {
            font-size: 18px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 8px;
        }

        .processing-container .sub-status {
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
            font-weight: 300;
        }

        .processing-container .action-tag {
            display: inline-block;
            padding: 6px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 15px;
        }

        .action-tag.accept {
            background: rgba(40, 167, 69, 0.15);
            color: #6bcb77;
            border: 1px solid rgba(40, 167, 69, 0.1);
        }

        .action-tag.reject {
            background: rgba(255, 71, 87, 0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255, 71, 87, 0.1);
        }

        @media (max-width: 480px) {
            .processing-container {
                padding: 30px 25px;
                border-radius: 20px;
            }

            .spinner {
                width: 45px;
                height: 45px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <div class="processing-container">
        <div class="spinner"></div>
        <div class="status">
            <?php if ($action == "accept") { ?>
                ✅ Accepting Application...
            <?php } elseif ($action == "reject") { ?>
                ❌ Rejecting Application...
            <?php } else { ?>
                Processing...
            <?php } ?>
        </div>
        <div class="sub-status">
            <?php if ($action == "accept") { ?>
                Please wait while we confirm the deal
            <?php } elseif ($action == "reject") { ?>
                Please wait while we process your decision
            <?php } else { ?>
                Please wait...
            <?php } ?>
        </div>
        <?php if ($action == "accept") { ?>
            <span class="action-tag accept">✅ Accept</span>
        <?php } elseif ($action == "reject") { ?>
            <span class="action-tag reject">❌ Reject</span>
        <?php } ?>
    </div>

</body>
</html>