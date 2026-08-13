<?php
session_start();

// If not logged in, redirect to login
if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$request_id = isset($_GET["request_id"]) ? intval($_GET["request_id"]) : 0;

// Get all requests from session
$requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];

// Find the specific request
$selected_request = null;
foreach ($requests as $req) {
    if ($req["id"] == $request_id) {
        $selected_request = $req;
        break;
    }
}

// If request not found or already completed
if (!$selected_request) {
    header("Location: workerS.php");
    exit();
}

// ===== CONFLICT CHECK =====
// Check if request is already "applied" or "completed"
if ($selected_request["status"] == "applied" || $selected_request["status"] == "completed") {
    // Check if this worker already applied
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
        // Redirect with error message
        header("Location: workerS.php?error=taken");
        exit();
    }
}

// Check if this worker already applied to this request
if (isset($_SESSION["applications"])) {
    foreach ($_SESSION["applications"] as $app) {
        if ($app["request_id"] == $request_id && $app["worker_email"] == $user_email) {
            header("Location: workerS.php?error=already_applied");
            exit();
        }
    }
}

$error_message = "";
$success_message = "";

// Handle application submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_application"])) {
    $price_per_page = floatval($_POST["price_per_page"]);
    $duration = htmlspecialchars($_POST["duration"]);
    $notes = htmlspecialchars($_POST["notes"]);
    
    // Validate
    if ($price_per_page <= 0) {
        $error_message = "❌ Please enter a valid price (greater than 0).";
    } elseif (empty($duration)) {
        $error_message = "❌ Please enter the duration needed.";
    } else {
        // ===== DOUBLE CHECK: Request might have been taken while filling form =====
        $current_requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];
        $current_request = null;
        foreach ($current_requests as $req) {
            if ($req["id"] == $request_id) {
                $current_request = $req;
                break;
            }
        }
        
        if ($current_request["status"] == "applied" || $current_request["status"] == "completed") {
            // Check if this worker already applied
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
            // Initialize applications array in session if not exists
            if (!isset($_SESSION["applications"])) {
                $_SESSION["applications"] = [];
            }
            
            // Create application
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
            
            // Add to session
            $_SESSION["applications"][] = $application;
            
            // Mark the request as "applied" so it doesn't show to other workers
            foreach ($_SESSION["requests"] as &$req) {
                if ($req["id"] == $selected_request["id"]) {
                    $req["status"] = "applied";
                    break;
                }
            }
            
            // Redirect with success
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
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 600px;
            width: 100%;
        }

        .apply-card {
            background: white;
            padding: 35px 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .apply-card .back-link {
            display: inline-block;
            color: #00a844;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .apply-card .back-link:hover {
            text-decoration: underline;
        }

        .apply-card h1 {
            font-size: 24px;
            color: #1a1a2e;
            margin-bottom: 5px;
        }

        .apply-card .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .request-summary {
            background: #f8f9fa;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid #00a844;
        }

        .request-summary h3 {
            color: #1a1a2e;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .request-summary .row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            font-size: 14px;
        }

        .request-summary .row .label {
            color: #888;
        }

        .request-summary .row .value {
            color: #1a1a2e;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
        }

        .form-group label .required {
            color: #e63946;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #00a844;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group .hint {
            font-size: 13px;
            color: #888;
            margin-top: 4px;
        }

        .price-display {
            background: #e8f5e9;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            color: #00a844;
            font-weight: 600;
            margin-top: 5px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #00a844, #007e33);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 5px;
        }

        .btn-submit:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 168, 68, 0.3);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            border-left: 4px solid #dc3545;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.applied {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="apply-card">
            <a href="workerS.php" class="back-link">← Back to Available Work</a>
            
            <h1>💰 Apply for Work</h1>
            <p class="subtitle">Set your price and timeline for this assignment</p>

            <!-- REQUEST SUMMARY -->
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
                <div class="row" style="border-top:1px solid #eee; padding-top:8px; margin-top:5px;">
                    <span class="label">Posted by:</span>
                    <span class="value"><?php echo htmlspecialchars($selected_request["buyer_email"]); ?></span>
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

            <!-- APPLICATION FORM -->
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
                <div style="text-align: center; padding: 20px 0;">
                    <p style="font-size: 18px; color: #856404;">⏳ This request is no longer available.</p>
                    <p style="color: #666; margin-top: 10px;">Someone else has already applied for this work.</p>
                    <br>
                    <a href="workerS.php" class="btn-submit" style="display: inline-block; text-decoration: none; padding: 12px 30px; font-size: 16px;">
                        🔍 View Other Requests
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        // Live total price calculation
        document.getElementById('price_per_page').addEventListener('input', function() {
            const price = parseFloat(this.value) || 0;
            const pages = <?php echo $selected_request["pages"]; ?>;
            const total = (price * pages).toFixed(2);
            document.getElementById('totalPriceDisplay').textContent = 'Total: ₹' + total;
        });
    </script>

</body>
</html>