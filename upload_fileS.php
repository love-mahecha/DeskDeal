<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$request_id = isset($_GET["request_id"]) ? intval($_GET["request_id"]) : 0;

$success_message = "";
$error_message = "";

$request_subject = "";
if ($request_id > 0) {
    $requests = isset($_SESSION["requests"]) ? $_SESSION["requests"] : [];
    foreach ($requests as $req) {
        if ($req["id"] == $request_id) {
            $request_subject = $req["subject"];
            break;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload_file"])) {
    $request_id = intval($_POST["request_id"]);
    
    if (isset($_FILES["assignment_file"]) && $_FILES["assignment_file"]["error"] == 0) {
        $file = $_FILES["assignment_file"];
        $file_name = $file["name"];
        $file_tmp = $file["tmp_name"];
        $file_size = $file["size"];
        
        $allowed_extensions = ["pdf", "doc", "docx", "txt", "jpg", "jpeg", "png", "gif"];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $max_file_size = 5 * 1024 * 1024;
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $error_message = "❌ Invalid file type. Allowed: PDF, DOC, DOCX, TXT, JPG, PNG, GIF";
        } elseif ($file_size > $max_file_size) {
            $error_message = "❌ File is too large. Max size is 5MB.";
        } else {
            $new_file_name = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', $user_email) . "_" . $file_name;
            $upload_path = "uploads/" . $new_file_name;
            
            if (!is_dir("uploads")) {
                mkdir("uploads", 0755, true);
            }
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                if (!isset($_SESSION["uploaded_files"])) {
                    $_SESSION["uploaded_files"] = [];
                }
                
                $_SESSION["uploaded_files"][] = [
                    "file_name" => $file_name,
                    "saved_name" => $new_file_name,
                    "uploaded_by" => $user_email,
                    "request_id" => $request_id,
                    "uploaded_at" => date("Y-m-d H:i:s"),
                    "file_size" => $file_size
                ];
                
                $success_message = "✅ File uploaded successfully!";
            } else {
                $error_message = "❌ Failed to upload file. Please try again.";
            }
        }
    } else {
        $error_message = "❌ Please select a file to upload.";
    }
}

$uploaded_files = [];
if (isset($_SESSION["uploaded_files"])) {
    foreach ($_SESSION["uploaded_files"] as $file) {
        if ($file["request_id"] == $request_id || $request_id == 0) {
            $uploaded_files[] = $file;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File - DeskDeal</title>
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
            background: radial-gradient(circle, #ff6b6b, #e63946);
        }

        .bg-blob-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, #6c5ce7, #ff6b6b);
        }

        .bg-blob-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, #ff4757, #6c5ce7);
            opacity: 0.1;
        }

        .container {
            position: relative;
            z-index: 10;
            max-width: 600px;
            width: 100%;
        }

        .upload-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            padding: 40px 45px;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
        }

        .upload-card .back-link {
            display: inline-block;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 20px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .upload-card .back-link:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .upload-card h1 {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .upload-card .subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 14px;
            font-weight: 300;
            margin-bottom: 20px;
        }

        .upload-card .user-badge {
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

        .request-context {
            padding: 12px 16px;
            border-radius: 14px;
            margin-bottom: 18px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .request-context.has-request {
            background: rgba(0, 168, 68, 0.08);
            border-left: 4px solid #6bcb77;
            color: rgba(255, 255, 255, 0.7);
        }

        .request-context.has-request strong {
            color: #6bcb77;
            font-weight: 600;
        }

        .request-context.no-request {
            background: rgba(255, 193, 7, 0.08);
            border-left: 4px solid #ffd93d;
            color: rgba(255, 255, 255, 0.5);
        }

        .request-context.no-request strong {
            color: #ffd93d;
            font-weight: 600;
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

        .form-group input[type="file"] {
            width: 100%;
            padding: 14px;
            border: 2px dashed rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            font-size: 14px;
            transition: all 0.3s;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.03);
            color: rgba(255, 255, 255, 0.6);
            font-family: 'Inter', sans-serif;
        }

        .form-group input[type="file"]:hover {
            border-color: rgba(255, 107, 107, 0.3);
            background: rgba(255, 255, 255, 0.05);
        }

        .form-group .file-hint {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.2);
            margin-top: 4px;
            font-weight: 300;
        }

        .btn-upload {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #ff6b6b, #e63946);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 20px rgba(255, 71, 87, 0.25);
            margin-top: 5px;
        }

        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(255, 71, 87, 0.35);
        }

        .btn-upload:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        .success-message {
            background: rgba(40, 167, 69, 0.1);
            color: #6bcb77;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 15px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid rgba(40, 167, 69, 0.15);
        }

        .error-message {
            background: rgba(255, 71, 87, 0.1);
            color: #ff6b6b;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 15px;
            font-weight: 500;
            font-size: 14px;
            border: 1px solid rgba(255, 71, 87, 0.1);
        }

        .file-list {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .file-list h3 {
            color: rgba(255, 255, 255, 0.6);
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 12px;
        }

        .file-item {
            background: rgba(255, 255, 255, 0.03);
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.04);
            transition: all 0.3s;
        }

        .file-item:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.08);
        }

        .file-item .file-name {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .file-item .file-meta {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.3);
            font-weight: 300;
        }

        .file-item .btn-download {
            color: #6bcb77;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            padding: 4px 14px;
            border: 1px solid rgba(107, 203, 119, 0.2);
            border-radius: 8px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .file-item .btn-download:hover {
            background: rgba(107, 203, 119, 0.1);
            border-color: rgba(107, 203, 119, 0.3);
        }

        .no-files {
            color: rgba(255, 255, 255, 0.2);
            font-size: 14px;
            font-style: italic;
            padding: 10px 0;
            font-weight: 300;
        }

        .footer-note {
            margin-top: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.15);
            font-size: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 15px;
            font-weight: 300;
        }

        .file-size-info {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.3);
            margin-bottom: 15px;
            font-weight: 300;
        }

        .file-size-info span {
            background: rgba(255, 255, 255, 0.03);
            padding: 4px 14px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        @media (max-width: 600px) {
            .upload-card {
                padding: 25px 20px;
                border-radius: 20px;
            }

            .upload-card h1 {
                font-size: 24px;
            }

            .file-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 400px) {
            .upload-card {
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
        <div class="upload-card">
            <a href="dashboardS.php" class="back-link">← Back to Dashboard</a>
            
            <h1>📎 Upload File</h1>
            <p class="subtitle">Upload your assignment files</p>

            <div class="user-badge">
                👤 <?php echo htmlspecialchars($user_email); ?>
            </div>

            <?php if ($request_subject != "") { ?>
                <div class="request-context has-request">
                    📚 Uploading for: <strong><?php echo htmlspecialchars($request_subject); ?></strong>
                </div>
            <?php } else { ?>
                <div class="request-context no-request">
                    💡 Go to <strong>My Requests</strong> and click "Upload File" for a specific request.
                </div>
            <?php } ?>

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

            <div class="file-size-info">
                <span>📄 Max size: 5MB</span>
                <span>📁 Allowed: PDF, DOC, DOCX, TXT, JPG, PNG, GIF</span>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="request_id" value="<?php echo $request_id; ?>">
                
                <div class="form-group">
                    <label for="assignment_file">Choose File <span class="required">*</span></label>
                    <input type="file" id="assignment_file" name="assignment_file" required>
                    <div class="file-hint">📌 Select a file from your computer</div>
                </div>

                <button type="submit" name="upload_file" class="btn-upload" id="uploadBtn">
                    📤 Upload File
                </button>
            </form>

            <div class="file-list">
                <h3>📋 Uploaded Files (<?php echo count($uploaded_files); ?>)</h3>
                
                <?php if (count($uploaded_files) > 0) { ?>
                    <?php foreach ($uploaded_files as $file) { ?>
                        <div class="file-item">
                            <div>
                                <div class="file-name">📄 <?php echo htmlspecialchars($file["file_name"]); ?></div>
                                <div class="file-meta">
                                    By: <?php echo htmlspecialchars($file["uploaded_by"]); ?> • 
                                    <?php echo date("M d, Y", strtotime($file["uploaded_at"])); ?> • 
                                    <?php echo round($file["file_size"] / 1024, 2); ?> KB
                                    <?php if ($file["request_id"] > 0) { ?>
                                        • Request #<?php echo $file["request_id"]; ?>
                                    <?php } ?>
                                </div>
                            </div>
                            <a href="uploads/<?php echo htmlspecialchars($file["saved_name"]); ?>" class="btn-download" download target="_blank">
                                ⬇️ Download
                            </a>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="no-files">📭 No files uploaded yet.</div>
                <?php } ?>
            </div>

            <div class="footer-note">
                🔒 Files are stored securely and can only be accessed by you.
            </div>
        </div>
    </div>

    <script>
        document.getElementById('assignment_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file selected';
            const fileSize = e.target.files[0]?.size || 0;
            const maxSize = 5 * 1024 * 1024;
            
            if (fileSize > maxSize) {
                alert('⚠️ File is too large! Maximum size is 5MB.');
                e.target.value = '';
                document.getElementById('uploadBtn').disabled = true;
            } else {
                document.getElementById('uploadBtn').disabled = false;
            }
        });
    </script>

</body>
</html>