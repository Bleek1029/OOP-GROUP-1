<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $player_choice = $_POST['player_choice'] ?? null;

    if ($player_choice) {
        $_SESSION['player_choice'] = $player_choice;
        header('Location: game.html');
        exit;
    } else {
        $error = 'Please select a player.';
    }
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Fellowship - Player Selection</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Press Start 2P & VT323 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Original Style Sheet -->
    <link rel="stylesheet" href="style.css?v=11">

    <style>
        /* Custom Fonts */
        .font-pixel-header { font-family: 'Press Start 2P', cursive; }
        .font-pixel-body { font-family: 'VT323', monospace; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 12px; }
        ::-webkit-scrollbar-track { background: #1a1c2c; }
        ::-webkit-scrollbar-thumb { background: #5d275d; border: 2px solid #1a1c2c; }

        /* RPG Box Shadow Style */
        .pixel-border {
            box-shadow: 
            -4px 0 0 0 #181425,
            4px 0 0 0 #181425,
            0 -4px 0 0 #181425,
            0 4px 0 0 #181425;
        }
        
        .pixel-inset {
            box-shadow: inset 4px 4px 0px 0px rgba(0,0,0,0.5);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
            100% { transform: translateY(0px); }
        }
        
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        
        /* Selection Colors */
        ::selection {
            background: #d95763;
            color: white;
        }

        /* 3D Perspective from Original Code */
        body {
            perspective: 1200px;
            background-color: #1a1c2c; /* Fallback color */
            overflow-x: hidden;
        }
        .container-3d {
            transform-style: preserve-3d;
        }
        #background-video {
            position: fixed;
            right: 0;
            bottom: 0;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            z-index: -100;
            transform: translate(-50%, -50%) scale(1.1);
            transition: transform 0.1s ease-out;
            top: 50%;
            left: 50%;
            object-fit: cover;
            /* Ensure no blur effect */
            filter: none !important;
            backdrop-filter: none !important;
        }
        #fireCanvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -90;
            pointer-events: none;
             /* Ensure no blur effect */
            filter: none !important;
            backdrop-filter: none !important;
        }
        
        /* Radio Selection Styles */
        input[type="radio"]:checked + div {
            border-color: #d95763;
            background-color: #343a5e;
        }
        input[type="radio"]:checked + div .check-icon {
            opacity: 1;
        }
        input[type="radio"]:checked + div .icon-box {
            background-color: #d95763;
            border-color: #ffcd75;
        }
        input[type="radio"]:checked + div .icon-box svg {
            color: #fff;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <!-- Original Background Video (Fire) & Canvas -->
    <video autoplay muted loop id="background-video">
        <source src="images/retro.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <canvas id="fireCanvas"></canvas>

    <!-- Main Container with 3D Effect -->
    <div class="relative z-10 w-full max-w-md container-3d">
        
        <!-- Wooden Frame Top Decoration -->
        <div class="flex justify-center -mb-6 relative z-20">
            <div class="bg-[#8f563b] px-6 py-2 border-4 border-[#181425] flex items-center gap-2 pixel-border animate-float">
                <i data-lucide="crown" class="w-6 h-6 text-[#ffcd75]" stroke-width="2.5"></i>
                <span class="text-[#ffcd75] font-pixel-header text-xs tracking-widest uppercase">local co-op</span>
                <i data-lucide="crown" class="w-6 h-6 text-[#ffcd75]" stroke-width="2.5"></i>
            </div>
        </div>

        <!-- Main Card Body -->
        <div class="bg-[#e4a672] p-1 border-4 border-[#181425] shadow-[10px_10px_0px_0px_rgba(0,0,0,0.4)]">
            <div class="border-4 border-[#c07448] p-6 md:p-8 bg-[#d28b58]">
                
                <!-- Header -->
                <div class="text-center mb-6 relative">
                    <h1 class="text-2xl md:text-3xl font-pixel-header text-[#3e2731] mb-2 drop-shadow-md leading-relaxed">
                        THE FELLOWSHIP
                    </h1>
                    <div class="h-1 w-full bg-[#8f563b] my-2 opacity-50"></div>
                    <p class="font-pixel-body text-xl text-[#5d275d] font-bold">
                        The Path of the King
                    </p>
                </div>

                <!-- PHP Errors Display -->
                <?php if (isset($error)): ?>
                    <div class="mb-6 bg-[#262b44] border-l-4 border-[#d95763] p-4 pixel-inset">
                        <div class="flex items-start gap-3">
                            <i data-lucide="alert-triangle" class="text-[#d95763] w-6 h-6 shrink-0 mt-1"></i>
                            <div class="text-[#feae34] font-pixel-body text-lg">
                                <?= e($error) ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form action="player selection.php" method="POST" class="space-y-6">
                    
                    <div class="flex gap-4 justify-center">
                        <!-- Player 1 Option -->
                        <label class="cursor-pointer flex-1">
                            <input type="radio" name="player_choice" value="player1" class="hidden">
                            <div class="h-32 bg-[#262b44] hover:bg-[#2d334d] border-4 border-[#181425] pixel-inset flex flex-col items-center justify-center gap-3 transition-colors duration-200 relative group">
                                <div class="absolute top-2 right-2 opacity-0 check-icon transition-opacity">
                                    <i data-lucide="check-circle-2" class="w-5 h-5 text-[#d95763]"></i>
                                </div>
                                <div class="icon-box w-12 h-12 bg-[#5d275d] border-2 border-[#181425] flex items-center justify-center transition-colors">
                                    <i data-lucide="swords" class="w-7 h-7 text-[#ffcd75]"></i>
                                </div>
                                <span class="font-pixel-header text-[10px] text-white tracking-widest uppercase">Player 1</span>
                            </div>
                        </label>

                        <!-- Player 2 Option -->
                        <label class="cursor-pointer flex-1">
                            <input type="radio" name="player_choice" value="player2" class="hidden">
                            <div class="h-32 bg-[#262b44] hover:bg-[#2d334d] border-4 border-[#181425] pixel-inset flex flex-col items-center justify-center gap-3 transition-colors duration-200 relative group">
                                <div class="absolute top-2 right-2 opacity-0 check-icon transition-opacity">
                                    <i data-lucide="check-circle-2" class="w-5 h-5 text-[#d95763]"></i>
                                </div>
                                <div class="icon-box w-12 h-12 bg-[#5d275d] border-2 border-[#181425] flex items-center justify-center transition-colors">
                                    <i data-lucide="crosshair" class="w-7 h-7 text-[#ffcd75]"></i>
                                </div>
                                <span class="font-pixel-header text-[10px] text-white tracking-widest uppercase">Player 2</span>
                            </div>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="
                        w-full relative overflow-hidden group
                        bg-[#d95763] hover:bg-[#ac3232] active:bg-[#ac3232] active:translate-y-1
                        text-white font-pixel-header text-xs md:text-sm py-4 px-6
                        border-b-8 border-r-8 border-[#5d275d] 
                        border-t-4 border-l-4 border-[#ffcd75]
                        transition-all duration-75 flex items-center justify-center gap-3
                    ">
                        <span>Begin Journey</span>
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform" stroke-width="3"></i>
                    </button>
                    
                </form>

            </div>
        </div>

        <!-- Decorative Bottom "Wood" Supports -->
        <div class="flex justify-between px-8 -mt-2 relative z-0">
           <div class="w-4 h-8 bg-[#5d275d] border-l-4 border-r-4 border-b-4 border-[#181425]"></div>
           <div class="w-4 h-8 bg-[#5d275d] border-l-4 border-r-4 border-b-4 border-[#181425]"></div>
        </div>
        
    </div>

    <script>
        // Initialize Icons
        lucide.createIcons();

        // Original 3D Mousemove Script
        document.addEventListener('mousemove', (e) => {
            const container = document.querySelector('.container-3d');
            const bg = document.querySelector('#background-video');
            if (!container) return;

            const { clientX, clientY } = e;
            const { innerWidth, innerHeight } = window;

            const rotateX = (clientY / innerHeight - 0.5) * -3; // Max rotation
            const rotateY = (clientX / innerWidth - 0.5) * 3; // Max rotation

            container.style.transform = `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;

            const bgRotateX = (clientY / innerHeight - 0.5) * -1;
            const bgRotateY = (clientX / innerWidth - 0.5) * 1;
            if(bg) {
                bg.style.transform = `translate(-50%, -50%) scale(1.1) rotateX(${bgRotateX}deg) rotateY(${bgRotateY}deg) translateX(${bgRotateY * -20}px) translateY(${bgRotateX * -20}px)`;
            }
        });
    </script>
</body>
</html>