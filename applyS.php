<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$request_id = isset($_GET["request_id"]) ? intval($_GET["request_id"]) : 0;

$requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];

$selected_request = null;
foreach ($requests as $req) {
    if ($req["id"] == $request_id) {
        $selected_request = $req;
        break;
    }
}

if (!$selected_request) {
    header("Location: workerS.php");
    exit();
}

// ===== DEMO MODE: Allow users to apply to their own requests =====
// Security check removed for demo purposes

if ($selected_request["status"] == "applied" || $selected_request["status"] == "completed") {
    $already_applied = false;
    if (isset($_SESSION["applications"])) {
        foreach ($_SESSION["applications"] as $app) {
            if ($app["request_id"] == $request_id && $app["worker_email"] == $user_email) {
                $already_applied = true;
                break;
            }
        }
    }
    
    if (!$already_applied) {
        header("Location: workerS.php?error=taken");
        exit();
    }
}

if (isset($_SESSION["applications"])) {
    foreach ($_SESSION["applications"] as $app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] == $user_email) {
            header("Location: workerS.php?error=already_applied");
            exit();
        }
    }
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_application"])) {
    $price_per_page = floatval($_POST["price_per_page"]);
    $duration = htmlspecialchars($_POST["duration"]);
    $notes = htmlspecialchars($_POST["notes"]);
    
    if ($price_per_page <= 0) {
        $error_message = "❌ Please enter a valid price (greater than 0).";
    } elseif (empty($duration)) {
        $error_message = "❌ Please enter the duration needed.";
    } else {
        $current_requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];
        $current_request = null;
        foreach ($current_requests as $req) {
            if ($req["id"] == $request_id) {
                $current_request = $req;
                break;
            }
        }
        
        if ($current_request["status"] == "applied" || $current_request["status"] == "completed") {
            $already_applied = false;
            if (isset($_SESSION["applications"])) {
                foreach ($_SESSION["applications"] as $app) {
                    if ($app["request_id"] == $request_id && $app["worker_email"] == $user_email) {
                        $already_applied = true;
                        break;
                    }
                }
            }
            
            if (!$already_applied) {
                $error_message = "❌ This request was already taken by another worker while you were filling the form!";
            }
        }
        
        if ($error_message == "") {
            if (!isset($_SESSION["applications"])) {
                $_SESSION["applications"] = [];
            }
            
            $application = [
                "request_id" => $selected_request["id"],
                "request_subject" => $selected_request["subject"],
                "worker_email" => $user_email,
                "price_per_page" => $price_per_page,
                "total_price" => $price_per_page * $selected_request["pages"],
                "duration" => $duration,
                "notes" => $notes,
                "status" => "pending",
                "applied_at" => date("Y-m-d H:i:s")
            ];
            
            $_SESSION["applications"][] = $application;
            
            foreach ($_SESSION["requests"] as &$req) {
                if ($req["id"] == $selected_request["id"]) {
                    $req["status"] = "applied";
                    break;
                }
            }
            
            header("Location: workerS.php?applied=success");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Work - DeskDeal</title>
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
            background: radial-gradient(circle, #6bcb77, #00a844);
            opacity: 0.1;
        }

        .container {
            position: relative;
            z-index: 10;
            max-width: 600px;
            width: 100%;
        }

        .apply-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            padding: 40px 45px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }

        .apply-card .back-link {
            display: inline-block;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 20px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .apply-card .back-link:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .apply-card h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #6bcb77);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .apply-card .subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            font-weight: 300;
            margin-bottom: 25px;
        }

        .request-summary {
            background: rgba(255, 255, 255, 0.04);
            padding: 18px 20px;
            border-radius: 16px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-left: 4px solid #00a844;
        }

        .request-summary h3 {
            color: rgba(255, 255, 255, 0.7);
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .request-summary .row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            font-size: 14px;
        }

        .request-summary .row .label {
            color: rgba(255, 255, 255, 0.3);
            font-weight: 300;
        }

        .request-summary .row .value {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .request-summary .row .value.worker {
            color: #a8a8ff;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-badge.applied {
            background: rgba(255, 193, 7, 0.15);
            color: #ffd93d;
            border: 1px solid rgba(255, 193, 7, 0.1);
        }

        .status-badge.completed {
            background: rgba(40, 167, 69, 0.15);
            color: #6bcb77;
            border: 1px solid rgba(40, 167, 69, 0.1);
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 5px;
            letter-spacing: 0.3px;
        }

        .form-group label .required {
            color: #ff6b6b;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 13px 16px;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            outline: none;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.25);
            font-weight: 300;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(0, 168, 68, 0.3);
            box-shadow: 0 0 25px rgba(0, 168, 68, 0.05);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group .hint {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.25);
            margin-top: 4px;
            font-weight: 300;
        }

        .price-display {
            background: rgba(0, 168, 68, 0.08);
            padding: 10px 15px;
            border-radius: 10px;
            font-size: 14px;
            color: #6bcb77;
            font-weight: 600;
            margin-top: 5px;
            border: 1px solid rgba(0, 168, 68, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #00a844, #007e33);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 20px rgba(0, 168, 68, 0.25);
            margin-top: 5px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 168, 68, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0) scale(0.98);
        }

        .error-message {
            background: rgba(255, 71, 87, 0.1);
            color: #ff6b6b;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid rgba(255, 71, 87, 0.1);
        }

        .not-available {
            text-align: center;
            padding: 30px 0;
        }

        .not-available p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
        }

        .not-available .sub {
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
            margin-top: 8px;
            font-weight: 300;
        }

        .not-available .btn-other {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #6c5ce7, #5a4bda);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            margin-top: 15px;
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.25);
        }

        .not-available .btn-other:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(108, 92, 231, 0.35);
        }

        .demo-note {
            text-align: center;
            margin-top: 18px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 12px;
            color: rgba(255, 255, 255, 0.15);
            font-weight: 300;
        }

        @media (max-width: 600px) {
            .apply-card {
                padding: 25px 20px;
                border-radius: 20px;
            }

            .apply-card h1 {
                font-size: 24px;
            }

            .request-summary .row {
                flex-direction: column;
            }
        }

        @media (max-width: 400px) {
            .apply-card {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <div class="container">
        <div class="apply-card">
            <a href="workerS.php" class="back-link">← Back to Available Work</a>
            
            <h1>💰 Apply for Work</h1>
            <p class="subtitle">Set your price and timeline for this assignment</p>

            <div class="request-summary">
                <h3>📋 Assignment Details</h3>
                <div class="row">
                    <span class="label">Subject:</span>
                    <span class="value"><?php echo htmlspecialchars($selected_request["subject"]); ?></span>
                </div>
                <div class="row">
                    <span class="label">Pages:</span>
                    <span class="value"><?php echo htmlspecialchars($selected_request["pages"]); ?></span>
                </div>
                <div class="row">
                    <span class="label">Deadline:</span>
                    <span class="value"><?php echo htmlspecialchars($selected_request["deadline"]); ?></span>
                </div>
                <div class="row" style="border-top:1px solid rgba(255,255,255,0.05); padding-top:8px; margin-top:5px;">
                    <span class="label">Posted by:</span>
                    <span class="value worker"><?php echo htmlspecialchars($selected_request["buyer_email"]); ?></span>
                </div>
                <?php if ($selected_request["status"] == "applied") { ?>
                <div class="row" style="margin-top:5px;">
                    <span class="label">Status:</span>
                    <span class="value"><span class="status-badge applied">⏳ Someone has applied</span></span>
                </div>
                <?php } ?>
            </div>

            <?php if ($error_message != "") { ?>
                <div class="error-message">
                    <?php echo $error_message; ?>
                </div>
            <?php } ?>

            <?php if ($selected_request["status"] != "applied" && $selected_request["status"] != "completed") { ?>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="price_per_page">Price per Page (₹) <span class="required">*</span></label>
                        <input type="number" id="price_per_page" name="price_per_page" min="0.01" step="0.01" placeholder="e.g., 10.00" required>
                        <div class="price-display" id="totalPriceDisplay">
                            Total: ₹0.00
                        </div>
                        <div class="hint">Based on <?php echo htmlspecialchars($selected_request["pages"]); ?> pages</div>
                    </div>

                    <div class="form-group">
                        <label for="duration">Duration Needed <span class="required">*</span></label>
                        <input type="text" id="duration" name="duration" placeholder="e.g., 2 days, 5 hours, 1 week" required>
                        <div class="hint">How much time do you need to complete this work?</div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" placeholder="Any special instructions or qualifications..."></textarea>
                    </div>

                    <button type="submit" name="submit_application" class="btn-submit">
                        📤 Submit Application
                    </button>
                </form>
            <?php } else { ?>
                <div class="not-available">
                    <p>⏳ This request is no longer available.</p>
                    <p class="sub">Someone else has already applied for this work.</p>
                    <a href="workerS.php" class="btn-other">
                        🔍 View Other Requests
                    </a>
                </div>
            <?php } ?>

            <div class="demo-note">
                🎯 <strong>Demo Mode:</strong> You can apply to your own requests for demonstration purposes.
            </div>
        </div>
    </div>

    <script>
        document.getElementById('price_per_page').addEventListener('input', function() {
            const price = parseFloat(this.value) || 0;
            const pages = <?php echo $selected_request["pages"]; ?>;
            const total = (price * pages).toFixed(2);
            document.getElementById('totalPriceDisplay').textContent = 'Total: ₹' + total;
        });
    </script>

</body>
</html>