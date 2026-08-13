<?php
// Start session to check if user is logged in
session_start();

// If not logged in, redirect to login
if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];

// Initialize requests array in session if it doesn't exist
if (!isset($_SESSION["requests"])) {
    $_SESSION["requests"] = [];
}

$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_request"])) {
    // Get form data
    $subject = htmlspecialchars($_POST["subject"]);
    $pages = htmlspecialchars($_POST["pages"]);
    $deadline = htmlspecialchars($_POST["deadline"]);
    $description = htmlspecialchars($_POST["description"]);
    
    // Validate
    if (empty($subject) || empty($pages) || empty($deadline) || empty($description)) {
        $error_message = "❌ Please fill in all fields!";
    } elseif ($pages < 1) {
        $error_message = "❌ Number of pages must be at least 1!";
    } else {
        // Create new request
        $new_request = [
            "id" => count($_SESSION["requests"]) + 1,
            "subject" => $subject,
            "pages" => $pages,
            "deadline" => $deadline,
            "description" => $description,
            "buyer_email" => $user_email,
            "status" => "pending",
            "created_at" => date("Y-m-d H:i:s")
        ];
        
        // Add to session array
        $_SESSION["requests"][] = $new_request;
        // Redirect to feedback page after successful submission
        header("Location: feedbackS.php");
        exit();
        // $success_message = "✅ Your request has been posted successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Work Done - Student Work</title>
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
            background: linear-gradient(135deg, #ff4b4b 0%, #cc2233 100%);
            padding: 20px;
        }

        .buyer-container {
            background: white;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 550px;
        }

        .buyer-container .back-link {
            display: inline-block;
            color: #4b6aff;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .buyer-container .back-link:hover {
            text-decoration: underline;
        }

        .buyer-container h1 {
            font-size: 28px;
            color: #1a1a2e;
            margin-bottom: 5px;
        }

        .buyer-container .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .buyer-container .user-badge {
            display: inline-block;
            background: #fff0f0;
            color: #e63946;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* ---------- FORM ---------- */
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
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 15px;
            transition: border-color 0.3s;
            background: white;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ff4b4b;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* ---------- BUTTONS ---------- */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ff4b4b, #e63946);
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
            box-shadow: 0 8px 25px rgba(255, 75, 75, 0.3);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        /* ---------- MESSAGES ---------- */
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            border-left: 4px solid #28a745;
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

        /* ---------- REQUEST SUMMARY ---------- */
        .request-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            border-left: 4px solid #28a745;
        }

        .request-summary h3 {
            color: #1a1a2e;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .request-summary p {
            color: #555;
            font-size: 14px;
            margin: 3px 0;
        }

        .request-summary strong {
            color: #1a1a2e;
        }

        .note {
            margin-top: 20px;
            font-size: 13px;
            color: #999;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>

    <div class="buyer-container">
        <a href="dashboardS.php" class="back-link">← Back to Dashboard</a>
        
        <h1>📝 Get Work Done</h1>
        <p class="subtitle">Submit your homework or assignment request</p>
        
        <div class="user-badge">
            👤 <?php echo htmlspecialchars($user_email); ?>
        </div>

        <?php if ($success_message != "") { ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php } ?>

        <?php if ($error_message != "") { ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php } ?>

        <!-- SUBMIT REQUEST FORM -->
        <form action="" method="POST">
            <div class="form-group">
                <label for="subject">Subject <span class="required">*</span></label>
                <input type="text" id="subject" name="subject" placeholder="e.g., Mathematics, Physics, History" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="pages">Number of Pages <span class="required">*</span></label>
                    <input type="number" id="pages" name="pages" min="1" placeholder="e.g., 5" required>
                </div>

                <div class="form-group">
                    <label for="deadline">Deadline <span class="required">*</span></label>
                    <input type="date" id="deadline" name="deadline" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description <span class="required">*</span></label>
                <textarea id="description" name="description" placeholder="Describe your assignment in detail..." required></textarea>
            </div>

            <button type="submit" name="submit_request" class="btn-submit">
                🚀 Post Request
            </button>
        </form>

        <!-- SHOW LAST SUBMITTED REQUEST -->
        <?php if ($success_message != "" && !empty($_SESSION["requests"])) { 
            $last_request = end($_SESSION["requests"]);
        ?>
            <div class="request-summary">
                <h3>📋 Your Request Summary</h3>
                <p><strong>Subject:</strong> <?php echo htmlspecialchars($last_request["subject"]); ?></p>
                <p><strong>Pages:</strong> <?php echo htmlspecialchars($last_request["pages"]); ?></p>
                <p><strong>Deadline:</strong> <?php echo htmlspecialchars($last_request["deadline"]); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($last_request["description"]); ?></p>
                <p><strong>Status:</strong> <span style="color: #ffc107;">⏳ Pending</span></p>
            </div>
        <?php } ?>

        <!-- <p class="note">
            🔒 After posting, you'll be redirected to the feedback page.
        </p> -->
        <p class="note">
        Your request will be visible to all workers. They'll apply with their price and timeline.
        </p>
    </div>

    <script>
        // Set minimum date to today for deadline
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('deadline').setAttribute('min', today);
        });
    </script>

</body>
</html>