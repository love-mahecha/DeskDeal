<?php
session_start();

// If not logged in, redirect to login
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
    <title>🎉 All Work for Free - DeskDeal</title>
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
            overflow: hidden;
            position: relative;
        }

        /* Floating particles background */
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
            background: radial-gradient(circle, #ff6b6b, #ff4757);
        }

        .bg-blob-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            background: radial-gradient(circle, #ffd93d, #ff6b6b);
        }

        .bg-blob-3 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: radial-gradient(circle, #ff4757, #ffd93d);
            opacity: 0.1;
        }

        .container {
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
            max-width: 550px;
            text-align: center;
            animation: popIn 0.6s ease-out;
        }

        @keyframes popIn {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .container .big-emoji {
            font-size: 70px;
            display: block;
            margin-bottom: 10px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .container h1 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, #ff6b6b, #ffd93d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 4px;
        }

        .container .subtitle {
            color: rgba(255, 255, 255, 0.4);
            font-size: 15px;
            font-weight: 300;
            margin-bottom: 8px;
        }

        .container .user-badge {
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

        .free-button {
            display: inline-block;
            padding: 18px 40px;
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 22px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(255, 71, 87, 0.25);
            position: relative;
            user-select: none;
            margin: 10px 0 5px 0;
            font-family: 'Inter', sans-serif;
        }

        .free-button:hover {
            transform: scale(1.03);
            box-shadow: 0 15px 40px rgba(255, 71, 87, 0.35);
        }

        .free-button .small-text {
            display: block;
            font-size: 13px;
            font-weight: 400;
            opacity: 0.85;
            margin-top: 4px;
        }

        .free-button.evil {
            position: fixed;
            z-index: 999;
            padding: 14px 28px;
            font-size: 18px;
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
            box-shadow: 0 10px 40px rgba(255, 71, 87, 0.4);
            cursor: grab;
            transition: none;
            animation: pulseGlow 1s infinite;
            border-radius: 14px;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 10px 40px rgba(255, 71, 87, 0.4); }
            50% { box-shadow: 0 10px 60px rgba(255, 71, 87, 0.6); }
        }

        .free-button.evil:hover {
            transform: none;
            box-shadow: 0 10px 40px rgba(255, 71, 87, 0.4);
        }

        .trap-message {
            margin-top: 20px;
            padding: 18px 20px;
            background: rgba(255, 193, 7, 0.1);
            border-radius: 14px;
            border-left: 4px solid #ffd93d;
            display: none;
            animation: slideDown 0.5s ease-out;
            border: 1px solid rgba(255, 193, 7, 0.1);
        }

        @keyframes slideDown {
            0% { transform: translateY(-20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }

        .trap-message.show {
            display: block;
        }

        .trap-message .emoji {
            font-size: 30px;
            display: block;
            margin-bottom: 5px;
        }

        .trap-message h3 {
            color: #ffd93d;
            font-size: 18px;
            font-weight: 600;
        }

        .trap-message p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin-top: 5px;
            font-weight: 300;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 10px 25px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            transition: all 0.3s;
            font-family: 'Inter', sans-serif;
        }

        .back-link:hover {
            background: rgba(255, 255, 255, 0.06);
            color: rgba(255, 255, 255, 0.8);
            border-color: rgba(255, 255, 255, 0.12);
        }

        .click-counter {
            margin-top: 15px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.3);
            font-weight: 300;
        }

        .click-counter strong {
            color: #ff6b6b;
            font-size: 18px;
            font-weight: 700;
        }

        .funny-messages {
            margin-top: 15px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.4);
            min-height: 25px;
            font-style: italic;
            background: rgba(255, 255, 255, 0.03);
            padding: 10px 15px;
            border-radius: 12px;
            font-weight: 300;
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        .footer-note {
            margin-top: 15px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.12);
            font-weight: 300;
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 25px;
                border-radius: 20px;
            }

            .container h1 {
                font-size: 26px;
            }

            .free-button {
                padding: 14px 25px;
                font-size: 18px;
            }

            .free-button .small-text {
                font-size: 12px;
            }

            .free-button.evil {
                padding: 12px 20px;
                font-size: 15px;
            }
        }

        @media (max-width: 400px) {
            .container {
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
        <span class="big-emoji">🎉</span>
        <h1>All Work for Free!</h1>
        <p class="subtitle">Click below and win the offer! 😈</p>
        
        <div class="user-badge">
            👤 <?php echo htmlspecialchars($user_email); ?>
        </div>

        <button class="free-button" id="freeButton" onclick="catchButton()">
            🎁 Get All Work Free
            <span class="small-text">✨ Limited Time Offer! ✨</span>
        </button>

        <div class="funny-messages" id="funnyMessage">
            💡 Hover over the button... if you can! 😄
        </div>

        <div class="click-counter">
            Attempts: <strong id="clickCount">0</strong>
        </div>

        <div class="trap-message" id="trapMessage">
            <span class="emoji">😂</span>
            <h3>YOU GOT PRANKED! 🤡</h3>
            <p>Sorry! Nothing is free in this world! 😜<br>
            But here's a virtual cookie for your effort: 🍪</p>
        </div>

        <a href="dashboardS.php" class="back-link">← Back to Dashboard</a>

        <div class="footer-note">
            🔒 100% scam-free guarantee (not really)
        </div>
    </div>

    <script>
        let clickCount = 0;
        let button = document.getElementById('freeButton');
        let funnyMessage = document.getElementById('funnyMessage');
        let trapMessage = document.getElementById('trapMessage');

        button.addEventListener('mouseenter', function(e) {
            const windowWidth = window.innerWidth;
            const windowHeight = window.innerHeight;

            const newX = Math.random() * (windowWidth - 200) + 10;
            const newY = Math.random() * (windowHeight - 100) + 10;

            button.classList.add('evil');
            button.style.left = newX + 'px';
            button.style.top = newY + 'px';
            
            const btnMessages = [
                "😈 Too slow!", "🏃‍♂️ Catch me!", "😂 Nope!",
                "💨 Zoom!", "🤡 Try again!", "🎯 Missed me!",
                "😏 You wish!", "💀 NEVER!", "🔥 Hot pursuit!",
                "🎪 Come on!", "🦄 Too fast!", "💪 Keep trying!"
            ];
            button.innerHTML = btnMessages[Math.floor(Math.random() * btnMessages.length)];

            const randomMsg = [
                "🎣 You almost got me!",
                "🏃‍♂️ I'm too fast for you!",
                "😂 This is so funny!",
                "💨 You'll never catch me!",
                "🤡 Try harder!",
                "🎯 Missed again!",
                "😏 Keep trying!",
                "💀 You can't catch me!",
                "🔥 You're getting warm!",
                "🎪 Welcome to the show!"
            ];
            document.getElementById('funnyMessage').innerHTML = 
                `💬 "${randomMsg[Math.floor(Math.random() * randomMsg.length)]}"`;
        });

        function catchButton() {
            clickCount++;
            document.getElementById('clickCount').textContent = clickCount;

            if (clickCount < 5) {
                document.getElementById('funnyMessage').innerHTML = 
                    `💬 "Nice try! You've attempted ${clickCount} times!"`;
            } else if (clickCount < 10) {
                document.getElementById('funnyMessage').innerHTML = 
                    `💬 "${clickCount} attempts! You're persistent! 😂"`;
            } else if (clickCount < 20) {
                document.getElementById('funnyMessage').innerHTML = 
                    `💬 "${clickCount} clicks! You really want free work! 😭"`;
            } else {
                document.getElementById('funnyMessage').innerHTML = 
                    `💬 "${clickCount} clicks! You're either crazy or dedicated! 🏆"`;
            }

            if (button.classList.contains('evil')) {
                button.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    button.style.transform = 'scale(1)';
                }, 100);
            }

            if (clickCount >= 30) {
                trapMessage.classList.add('show');
                trapMessage.innerHTML = `
                    <span class="emoji">🏆</span>
                    <h3>YOU WIN! 🎉</h3>
                    <p>You're officially the most persistent person ever! 😂<br>
                    Here's your prize: <strong>FREE VIRTUAL HIGH FIVE! ✋</strong><br>
                    (And yes, you still don't get free work 😜)</p>
                `;
                button.classList.remove('evil');
                button.style.position = 'static';
                button.style.left = 'auto';
                button.style.top = 'auto';
                button.innerHTML = '🎉 YOU WON! (Sort of)';
                button.style.background = 'linear-gradient(135deg, #ffd93d, #ff6b6b)';
            }
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('#freeButton') && !e.target.closest('.container')) {
                if (button.classList.contains('evil')) {
                    const windowWidth = window.innerWidth;
                    const windowHeight = window.innerHeight;
                    const newX = Math.random() * (windowWidth - 200) + 10;
                    const newY = Math.random() * (windowHeight - 100) + 10;
                    button.style.left = newX + 'px';
                    button.style.top = newY + 'px';
                }
            }
        });

        console.log('🎉 Welcome to the Free Work Prank!');
        console.log('😂 Try clicking the button... if you can!');
        console.log('💡 Secret: If you click 30 times, something special happens!');
    </script>

</body>
</html>