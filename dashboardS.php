<?php
session_start();

if (!isset($_SESSION["user_email"])) {
    header("Location: loginS.php");
    exit();
}

$user_email = $_SESSION["user_email"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DeskDeal</title>
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
            background: radial-gradient(circle, #6c5ce7, #00a844);
        }

        .bg-blob-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, #00a844, #6c5ce7);
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

        .dashboard-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 700px;
            padding: 40px 45px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .header .left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header .left .emoji {
            font-size: 36px;
        }

        .header .left h1 {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ffffff, #a8a8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header .right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header .user-badge {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.8);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .header .logout-link {
            color: rgba(255, 71, 87, 0.7);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 6px 16px;
            border: 1px solid rgba(255, 71, 87, 0.2);
            border-radius: 8px;
            transition: all 0.3s;
        }

        .header .logout-link:hover {
            background: rgba(255, 71, 87, 0.15);
            color: #ff6b6b;
            border-color: rgba(255, 71, 87, 0.3);
        }

        .welcome-text {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 28px;
            text-align: center;
            font-weight: 300;
        }

        .role-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .btn-role {
            padding: 22px 20px;
            border: none;
            border-radius: 16px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            color: white;
            text-decoration: none;
            text-align: center;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .btn-role::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: all 0.6s;
        }

        .btn-role:hover::before {
            left: 100%;
        }

        .btn-role:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .btn-role:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-buyer {
            background: linear-gradient(135deg, #ff4b4b, #e63946);
            box-shadow: 0 4px 20px rgba(255, 75, 75, 0.25);
        }

        .btn-buyer:hover {
            box-shadow: 0 8px 30px rgba(255, 75, 75, 0.35);
        }

        .btn-worker {
            background: linear-gradient(135deg, #6c5ce7, #5a4bda);
            box-shadow: 0 4px 20px rgba(108, 92, 231, 0.25);
        }

        .btn-worker:hover {
            box-shadow: 0 8px 30px rgba(108, 92, 231, 0.35);
        }

        .btn-role .icon {
            font-size: 26px;
            display: block;
            margin-bottom: 4px;
        }

        .btn-role small {
            display: block;
            font-weight: 400;
            font-size: 12px;
            opacity: 0.85;
            margin-top: 4px;
        }

        .quick-links {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 18px;
        }

        .quick-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 8px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.06);
            font-family: 'Inter', sans-serif;
        }

        .quick-link:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.12);
            color: white;
        }

        .quick-link .icon {
            font-size: 16px;
        }

        .quick-link.green { border-color: rgba(0, 168, 68, 0.2); color: #6bcb77; }
        .quick-link.green:hover { background: rgba(0, 168, 68, 0.1); border-color: rgba(0, 168, 68, 0.3); }

        .quick-link.purple { border-color: rgba(108, 92, 231, 0.2); color: #a8a8ff; }
        .quick-link.purple:hover { background: rgba(108, 92, 231, 0.1); border-color: rgba(108, 92, 231, 0.3); }

        .quick-link.yellow { border-color: rgba(255, 193, 7, 0.2); color: #ffd93d; }
        .quick-link.yellow:hover { background: rgba(255, 193, 7, 0.1); border-color: rgba(255, 193, 7, 0.3); }

        .quick-link.pink { border-color: rgba(253, 121, 168, 0.2); color: #fd79a8; }
        .quick-link.pink:hover { background: rgba(253, 121, 168, 0.1); border-color: rgba(253, 121, 168, 0.3); }

        .quick-link.orange { border-color: rgba(253, 203, 110, 0.2); color: #fdcb6e; }
        .quick-link.orange:hover { background: rgba(253, 203, 110, 0.1); border-color: rgba(253, 203, 110, 0.3); }

        .free-work-link {
            display: block;
            text-align: center;
            padding: 12px;
            color: rgba(255, 107, 107, 0.6);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            border: 1px dashed rgba(255, 107, 107, 0.2);
            border-radius: 12px;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .free-work-link:hover {
            background: rgba(255, 107, 107, 0.05);
            border-color: rgba(255, 107, 107, 0.3);
            color: #ff6b6b;
        }

        .note {
            text-align: center;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.2);
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 15px;
            font-weight: 300;
        }

        .note strong {
            color: rgba(255, 255, 255, 0.4);
            font-weight: 500;
        }

        @media (max-width: 600px) {
            .dashboard-container {
                padding: 25px 20px;
                max-width: 100%;
                border-radius: 20px;
            }

            .header {
                flex-direction: column;
                text-align: center;
            }

            .header .left {
                flex-direction: column;
            }

            .role-grid {
                grid-template-columns: 1fr;
            }

            .quick-links {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 400px) {
            .quick-links {
                grid-template-columns: 1fr;
            }

            .header .right {
                flex-wrap: wrap;
                justify-content: center;
            }

            .dashboard-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>

    <div class="bg-blob bg-blob-1"></div>
    <div class="bg-blob bg-blob-2"></div>
    <div class="bg-blob bg-blob-3"></div>

    <div class="dashboard-container">

        <div class="header">
            <div class="left">
                <span class="emoji">🎓</span>
                <h1>DeskDeal</h1>
            </div>
            <div class="right">
                <span class="user-badge">👤 <?php echo htmlspecialchars($user_email); ?></span>
                <a href="logoutS.php" class="logout-link"> Logout</a>
            </div>
        </div>

        <p class="welcome-text">What would you like to do today?</p>

        <div class="role-grid">
            <a href="buyerS.php" class="btn-role btn-buyer">
                <span class="icon">📝</span>
                Get Work Done
                <small>Pay someone to do your homework</small>
            </a>
            <a href="workerS.php" class="btn-role btn-worker">
                <span class="icon">💼</span>
                Do Work / Apply
                <small>Earn money by doing assignments</small>
            </a>
        </div>

        <div class="quick-links">
            <a href="my_requestsS.php" class="quick-link green"><span class="icon"></span> My Requests</a>
            <a href="my_applicationsS.php" class="quick-link purple"><span class="icon"></span> My Apps</a>
            <a href="email_logS.php" class="quick-link yellow"><span class="icon"></span> Email Log</a>
            <a href="feedbackS.php" class="quick-link pink"><span class="icon"></span> Feedback</a>
            <a href="completedS.php" class="quick-link orange"><span class="icon"></span> Completed</a>
        </div>

        <a href="freeWorkS.php" class="free-work-link">
             Click here for free work!
        </a>

        <p class="note">
            Choose your path: <strong>Get Work Done</strong> (hire help) or <strong>Do Work</strong> (earn money). Let's go! 
        </p>
    </div>

</body>
</html>