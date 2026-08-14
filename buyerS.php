<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];

if (!isset($_SESSION["requests"])) {
    $_SESSION["requests"] = [];
}

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_request"])) {
    $subject = htmlspecialchars($_POST["subject"]);
    $pages = htmlspecialchars($_POST["pages"]);
    $deadline = htmlspecialchars($_POST["deadline"]);
    $description = htmlspecialchars($_POST["description"]);
   
    if (empty($subject) || empty($pages) || empty($deadline) || empty($description)) {
        $error_message = "❌ Please fill in all fields!";
    } elseif ($pages < 1) {
        $error_message = "❌ Number of pages must be at least 1!";
    } else {
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
        
        $_SESSION["requests"][] = $new_request;
        
        header("Location: feedbackS.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Work Done - DeskDeal</title>
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
            background: radial-gradient(circle, #ff4b4b, #e63946);
        }

        .bg-blob-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, #6c5ce7, #ff4b4b);
        }

        .bg-blob-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, #ff6b6b, #6c5ce7);
            opacity: 0.1;
        }

        .buyer-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 600px;
            padding: 40px 45px;
        }

        .buyer-container .back-link {
            display: inline-block;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 20px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .buyer-container .back-link:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .buyer-container .back-link .arrow {
            margin-right: 6px;
        }

        .buyer-container h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .buyer-container .subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            font-weight: 300;
            margin-bottom: 25px;
        }

        .buyer-container .user-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.7);
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.06);
            margin-bottom: 25px;
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
        .form-group textarea,
        .form-group select {
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
        .form-group textarea:focus,
        .form-group select:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 75, 75, 0.3);
            box-shadow: 0 0 25px rgba(255, 75, 75, 0.05);
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

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #ff4b4b, #e63946);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 20px rgba(255, 75, 75, 0.25);
            margin-top: 5px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(255, 75, 75, 0.35);
        }

        .btn-submit:active {
            transform: translateY(0) scale(0.98);
        }

        .success-message {
            background: rgba(40, 167, 69, 0.1);
            color: #6bcb77;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid rgba(40, 167, 69, 0.15);
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

        .request-summary {
            background: rgba(255, 255, 255, 0.04);
            padding: 18px 20px;
            border-radius: 14px;
            margin-top: 20px;
            border: 1px solid rgba(40, 167, 69, 0.15);
        }

        .request-summary h3 {
            color: rgba(255, 255, 255, 0.8);
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .request-summary p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
            margin: 4px 0;
            font-weight: 300;
        }

        .request-summary strong {
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        .request-summary .status-pending {
            color: #ffd93d;
        }

        .note {
            margin-top: 20px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.2);
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 15px;
            font-weight: 300;
        }

        @media (max-width: 600px) {
            .buyer-container {
                padding: 25px 20px;
                max-width: 100%;
                border-radius: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .buyer-container h1 {
                font-size: 24px;
            }
        }

        @media (max-width: 400px) {
            .buyer-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <div class="buyer-container">
        <a href="dashboardS.php" class="back-link"><span class="arrow">←</span> Back to Dashboard</a>
        
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
                 Post Request
            </button>
        </form>

        <?php if ($success_message != "" && !empty($_SESSION["requests"])) { 
            $last_request = end($_SESSION["requests"]);
        ?>
            <div class="request-summary">
                <h3>📋 Your Request Summary</h3>
                <p><strong>Subject:</strong> <?php echo htmlspecialchars($last_request["subject"]); ?></p>
                <p><strong>Pages:</strong> <?php echo htmlspecialchars($last_request["pages"]); ?></p>
                <p><strong>Deadline:</strong> <?php echo htmlspecialchars($last_request["deadline"]); ?></p>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($last_request["description"]); ?></p>
                <p><strong>Status:</strong> <span class="status-pending">⏳ Pending</span></p>
            </div>
        <?php } ?>

        <p class="note">
             Your request will be visible to all workers. They'll apply with their price and timeline.
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('deadline').setAttribute('min', today);
        });
    </script>

</body>
</html>