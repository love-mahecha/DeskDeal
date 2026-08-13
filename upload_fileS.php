<?php
session_start();

// If not logged in, redirect to login
if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$request_id = isset($_GET["request_id"]) ? intval($_GET["request_id"]) : 0;

$success_message = "";
$error_message = "";

// Get request details if request_id is provided
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

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload_file"])) {
    $request_id = intval($_POST["request_id"]);
    
    // Check if file was uploaded
    if (isset($_FILES["assignment_file"]) && $_FILES["assignment_file"]["error"] == 0) {
        $file = $_FILES["assignment_file"];
        $file_name = $file["name"];
        $file_tmp = $file["tmp_name"];
        $file_size = $file["size"];
        
        // File validation
        $allowed_extensions = ["pdf", "doc", "docx", "txt", "jpg", "jpeg", "png", "gif"];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Max file size: 5MB
        $max_file_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file_extension, $allowed_extensions)) {
            $error_message = "❌ Invalid file type. Allowed: PDF, DOC, DOCX, TXT, JPG, PNG, GIF";
        } elseif ($file_size > $max_file_size) {
            $error_message = "❌ File is too large. Max size is 5MB.";
        } else {
            // Create unique file name
            $new_file_name = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', $user_email) . "_" . $file_name;
            $upload_path = "uploads/" . $new_file_name;
            
            // Make sure uploads folder exists
            if (!is_dir("uploads")) {
                mkdir("uploads", 0755, true);
            }
            
            // Move file to uploads folder
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Store file info in session
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

// Get uploaded files for this request
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

        .upload-card {
            background: white;
            padding: 35px 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .upload-card .back-link {
            display: inline-block;
            color: #00a844;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .upload-card .back-link:hover {
            text-decoration: underline;
        }

        .upload-card h1 {
            font-size: 24px;
            color: #1a1a2e;
            margin-bottom: 5px;
        }

        .upload-card .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .upload-card .user-badge {
            display: inline-block;
            background: #e8f5e9;
            color: #008736;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* ===== REQUEST CONTEXT BOX ===== */
        .request-context {
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .request-context.has-request {
            background: #e8f5e9;
            border-left: 4px solid #00a844;
            color: #1a1a2e;
        }

        .request-context.no-request {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }

        .request-context strong {
            font-weight: 700;
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

        .form-group input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: border-color 0.3s;
            cursor: pointer;
            background: #fafafa;
        }

        .form-group input[type="file"]:hover {
            border-color: #00a844;
            background: #f0fdf4;
        }

        .form-group .file-hint {
            font-size: 12px;
            color: #999;
            margin-top: 4px;
        }

        .btn-upload {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #00a844, #007e33);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 5px;
        }

        .btn-upload:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 168, 68, 0.3);
        }

        .btn-upload:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-weight: 600;
            border-left: 4px solid #28a745;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-weight: 600;
            border-left: 4px solid #dc3545;
        }

        /* Uploaded files list */
        .file-list {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }

        .file-list h3 {
            color: #1a1a2e;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .file-item {
            background: #f8f9fa;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .file-item .file-name {
            font-size: 14px;
            color: #1a1a2e;
            font-weight: 500;
        }

        .file-item .file-meta {
            font-size: 12px;
            color: #999;
        }

        .file-item .btn-download {
            color: #00a844;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 4px 12px;
            border: 1px solid #00a844;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .file-item .btn-download:hover {
            background: #00a844;
            color: white;
        }

        .no-files {
            color: #999;
            font-size: 14px;
            font-style: italic;
            padding: 10px 0;
        }

        .footer-note {
            margin-top: 20px;
            text-align: center;
            color: #999;
            font-size: 13px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .file-size-info {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }

        .file-size-info span {
            background: #f0fdf4;
            padding: 4px 12px;
            border-radius: 12px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="upload-card">
            <a href="dashboardS.php" class="back-link">← Back to Dashboard</a>
            
            <h1>📎 Upload File</h1>
            <p class="subtitle">Upload your assignment files</p>

            <div class="user-badge">
                👤 <?php echo htmlspecialchars($user_email); ?>
            </div>

            <!-- ===== REQUEST CONTEXT ===== -->
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

            <!-- File size info -->
            <div class="file-size-info">
                <span>📄 Max size: 5MB</span>
                <span>📁 Allowed: PDF, DOC, DOCX, TXT, JPG, PNG, GIF</span>
            </div>

            <!-- Upload Form -->
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

            <!-- Uploaded Files List -->
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
        // Show file name when selected
        document.getElementById('assignment_file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file selected';
            const fileSize = e.target.files[0]?.size || 0;
            const maxSize = 5 * 1024 * 1024; // 5MB
            
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