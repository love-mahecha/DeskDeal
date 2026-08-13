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
    <title>🎉 All Work for Free - Student Work</title>
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
            background: linear-gradient(135deg, #2d3436 0%, #1a1a2e 100%);
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* Floating particles background */
        body::before {
            content: "🎉💰🎊💵🎈💲🥳💸🎁";
            position: absolute;
            font-size: 40px;
            opacity: 0.05;
            width: 200%;
            height: 200%;
            animation: floatParticles 20s infinite linear;
            pointer-events: none;
        }

        @keyframes floatParticles {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .container {
            position: relative;
            z-index: 10;
            background: white;
            padding: 50px 40px;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 550px;
            text-align: center;
            animation: popIn 0.6s ease-out;
        }

        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
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
            color: #1a1a2e;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .container .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 10px;
        }

        .container .user-badge {
            display: inline-block;
            background: #fff0f0;
            color: #ff4757;
            padding: 5px 18px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        /* ---------- THE BUTTON ---------- */
        .free-button {
            display: inline-block;
            padding: 18px 40px;
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 24px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 10px 30px rgba(255, 71, 87, 0.3);
            position: relative;
            user-select: none;
            margin: 10px 0 5px 0;
        }

        .free-button:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(255, 71, 87, 0.4);
        }

        .free-button .small-text {
            display: block;
            font-size: 14px;
            font-weight: 400;
            opacity: 0.9;
            margin-top: 3px;
        }

        /* ---------- THE RUNNING BUTTON (EVIL MODE) ---------- */
        .free-button.evil {
            position: fixed;
            z-index: 999;
            padding: 14px 30px;
            font-size: 18px;
            background: linear-gradient(135deg, #ff6b6b, #ff4757);
            color: white;
            box-shadow: 0 10px 40px rgba(255, 71, 87, 0.5);
            cursor: grab;
            transition: none;
            animation: pulseGlow 1s infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 10px 40px rgba(255, 71, 87, 0.5); }
            50% { box-shadow: 0 10px 60px rgba(255, 71, 87, 0.8); }
        }

        .free-button.evil:hover {
            transform: none;
            box-shadow: 0 10px 40px rgba(255, 71, 87, 0.5);
        }

        /* ---------- TRAP MESSAGE ---------- */
        .trap-message {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-radius: 12px;
            border-left: 4px solid #ffc107;
            display: none;
            animation: slideDown 0.5s ease-out;
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
            color: #856404;
            font-size: 18px;
        }

        .trap-message p {
            color: #856404;
            font-size: 14px;
            margin-top: 5px;
        }

        /* ---------- BACK LINK ---------- */
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #4b6aff;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            padding: 10px 25px;
            border: 2px solid #4b6aff;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .back-link:hover {
            background: #4b6aff;
            color: white;
        }

        /* ---------- COUNTER ---------- */
        .click-counter {
            margin-top: 15px;
            font-size: 14px;
            color: #999;
        }

        .click-counter strong {
            color: #ff4757;
            font-size: 18px;
        }

        .funny-messages {
            margin-top: 15px;
            font-size: 14px;
            color: #666;
            min-height: 25px;
            font-style: italic;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
        }

        .funny-messages .highlight {
            color: #ff4757;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="container">
        <span class="big-emoji">🎉</span>
        <h1>All Work for Free!</h1>
        <p class="subtitle">click below! And Win offer </p>
        
        <div class="user-badge">
            👤 <?php echo htmlspecialchars($user_email); ?>
        </div>

        <!-- THE BUTTON -->
        <button class="free-button" id="freeButton" onclick="catchButton()">
            🎁 Get All Work Free
            <span class="small-text">✨ Limited Time Offer! ✨</span>
        </button>

        <!-- Funny Messages -->
        <div class="funny-messages" id="funnyMessage">
            💡 Hover over the button... if you can! 😄
        </div>

        <!-- Click Counter -->
        <div class="click-counter">
            Attempts: <strong id="clickCount">0</strong>
        </div>

        <!-- TRAP MESSAGE -->
        <div class="trap-message" id="trapMessage">
            <span class="emoji">😂</span>
            <h3>YOU GOT PRANKED! 🤡</h3>
            <p>Sorry! Nothing is free in this world! 😜<br>
            But here's a virtual cookie for your effort: 🍪</p>
        </div>

        <a href="dashboardS.php" class="back-link">← Back to Dashboard</a>

        <div style="margin-top:15px; font-size:12px; color:#bbb;">
            🔒 100% scam-free guarantee
        </div>
    </div>

    <script>
        let clickCount = 0;
        let button = document.getElementById('freeButton');
        let funnyMessage = document.getElementById('funnyMessage');
        let trapMessage = document.getElementById('trapMessage');

        // Button hover - make it run away!
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

            // Special surprise at 30 clicks
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

        // Click on page moves button
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