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
            background: #004d1a;
            position: relative;
            overflow: hidden;
            padding: 20px;
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

       
        .dashboard-container {
            position: relative;
            z-index: 10;
            background: white;
            padding: 35px 40px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 700px;
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
            gap: 15px;
        }

        .header .left .emoji {
            font-size: 40px;
        }

        .header .left h1 {
            font-size: 26px;
            color: #1a1a2e;
        }

        .header .left h1 span {
            color: #00a844;
        }

        .header .right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header .user-badge {
            background: #e8f5e9;
            color: #008736;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .header .logout-link {
            color: #ff4b4b;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            padding: 6px 16px;
            border: 2px solid #ff4b4b;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .header .logout-link:hover {
            background: #ff4b4b;
            color: white;
        }

       
        .welcome-text {
            font-size: 15px;
            color: #555;
            margin-bottom: 25px;
            text-align: center;
        }

        
        .role-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .btn-role {
            padding: 25px 20px;
            border: none;
            border-radius: 15px;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            color: white;
            text-decoration: none;
            text-align: center;
        }

        .btn-role:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .btn-buyer {
            background: linear-gradient(135deg, #ff4b4b, #e63946);
        }

        .btn-worker {
            background: linear-gradient(135deg, #6c5ce7, #5a4bda);
        }

        .btn-role .icon {
            font-size: 28px;
            display: block;
            margin-bottom: 5px;
        }

        .btn-role small {
            display: block;
            font-weight: 400;
            font-size: 13px;
            opacity: 0.9;
            margin-top: 4px;
        }

        
        .quick-links {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 15px;
        }

        .quick-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 10px;
            background: #f8f9fa;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            color: #1a1a2e;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .quick-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .quick-link.green {
            border-color: #00a844;
            color: #00a844;
        }
        .quick-link.green:hover {
            background: #00a844;
            color: white;
        }

        .quick-link.purple {
            border-color: #6c5ce7;
            color: #6c5ce7;
        }
        .quick-link.purple:hover {
            background: #6c5ce7;
            color: white;
        }

        .quick-link.yellow {
            border-color: #ffc107;
            color: #856404;
        }
        .quick-link.yellow:hover {
            background: #ffc107;
            color: white;
        }

        .quick-link.pink {
            border-color: #fd79a8;
            color: #e84393;
        }
        .quick-link.pink:hover {
            background: #fd79a8;
            color: white;
        }

        .quick-link.orange {
            border-color: #fdcb6e;
            color: #e17055;
        }
        .quick-link.orange:hover {
            background: #fdcb6e;
            color: white;
        }

        
        .free-work-link {
            display: block;
            text-align: center;
            padding: 12px;
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 600;
            border: 2px dashed #ff6b6b;
            border-radius: 10px;
            transition: all 0.3s;
            margin-bottom: 20px;
        }

        .free-work-link:hover {
            background: #fff5f5;
        }

        
        .note {
            text-align: center;
            font-size: 13px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        
        @media (max-width: 600px) {
            .dashboard-container {
                padding: 25px 20px;
                max-width: 100%;
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
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        
        <div class="header">
            <div class="left">
                <span class="emoji">🎓</span>
                <h1>Desk<span>Deal</span></h1>
            </div>
            <div class="right">
                <span class="user-badge">👤 <?php echo htmlspecialchars($user_email); ?></span>
                <a href="logoutS.php" class="logout-link">🚪 Logout</a>
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
            <a href="my_requestsS.php" class="quick-link green">📋 My Requests</a>
            <a href="my_applicationsS.php" class="quick-link purple">💼 My Applications</a>
            <a href="email_logS.php" class="quick-link yellow">📧 Email Log</a>
            <a href="feedbackS.php" class="quick-link pink">💬 Feedback</a>
            <a href="completedS.php" class="quick-link orange">✅ Completed</a>
        </div>

        
        <a href="freeWorkS.php" class="free-work-link">
             Click here for free work!
        </a>

        
        <p class="note">
            💡 Choose your path: <strong>Get Work Done</strong> (hire help) or <strong>Do Work</strong> (earn money). Let's go! 
        </p>
    </div>

</body>
</html>