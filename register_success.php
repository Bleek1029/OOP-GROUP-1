<?php
// This page is displayed after a successful registration.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Created Successfully</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Cinzel+Decorative:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=12" />
    <style>
        body { perspective: 1200px; }
        .container { transform-style: preserve-3d; position: relative; z-index: 10; }
        #background-video, #fireCanvas {
            pointer-events: none; /* Prevent video/canvas from blocking clicks */
        }
        #background-video {
            transform: translate(-50%, -50%) scale(1.1);
            transition: transform 0.1s ease-out;
        }
        .success-title {
            font-family: 'Cinzel Decorative', serif;
            font-size: 2.2rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 0;
            background: linear-gradient(to bottom, #fff0c4 0%, #d4af37 50%, #996515 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0px 5px 15px rgba(0,0,0,0.8);
        }
        .subtitle { color: #a0b0a0; margin-top: 0.5rem; }
        .ornament { pointer-events: none; }
        .success-card {
            margin-top: 1.5rem;
            padding: 1.25rem 1rem;
            border: 1px solid rgba(192,152,82,0.35);
            background: rgba(20, 24, 20, 0.6);
            color: #e0e0d1;
            border-radius: 4px;
            box-shadow: 0 0 25px rgba(192, 152, 82, 0.12) inset;
        }
        .btn-primary {
            margin-top: 1.5rem;
            width: 100%;
            padding: 0.9rem 1.2rem;
            background: linear-gradient(to bottom, #ffd700, #8a6e3c);
            border: 1px solid #8a6e3c;
            color: #1a1a1a;
            font-size: 1.05rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-shadow: 0 1px 1px rgba(255,255,255,0.25);
            text-decoration: none;
        }
        .btn-primary:hover {
            background: linear-gradient(to bottom, #ffeca8, #c09852);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }
    .btn-primary { position: relative; z-index: 20; }
    </style>
</head>
<body>
    <video autoplay muted loop id="background-video">
        <source src="images/fire.mp4" type="video/mp4">
    </video>
    <canvas id="fireCanvas"></canvas>

    <div class="container">
        <div class="ornament top-left"></div>
        <div class="ornament top-right"></div>
        <div class="ornament bottom-left"></div>
        <div class="ornament bottom-right"></div>

        <h1 class="success-title">Account Created</h1>
        <p class="subtitle">Your account has been forged. You may now proceed to login.</p>

        <div class="success-card">
            <p>Account created successfully. Click the button below to continue to the login page.</p>
        </div>

        <a href="login.php" class="btn-primary" style="display:inline-block; text-align:center;">Continue to Login</a>
    </div>

    <script>
        document.addEventListener('mousemove', (e) => {
            const container = document.querySelector('.container');
            const bg = document.querySelector('#background-video');
            if (!container) return;

            const { clientX, clientY } = e;
            const { innerWidth, innerHeight } = window;

            const rotateX = (clientY / innerHeight - 0.5) * -3;
            const rotateY = (clientX / innerWidth - 0.5) * 3;

            container.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;

            const bgRotateX = (clientY / innerHeight - 0.5) * -1;
            const bgRotateY = (clientX / innerWidth - 0.5) * 1;
            if (bg) {
                bg.style.transform = `translate(-50%, -50%) scale(1.1) rotateX(${bgRotateX}deg) rotateY(${bgRotateY}deg) translateX(${bgRotateY * -20}px) translateY(${bgRotateX * -20}px)`;
            }
        });
    </script>
</body>
</html>