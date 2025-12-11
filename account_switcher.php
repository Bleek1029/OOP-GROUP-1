<?php
session_start();

// Read remembered accounts from cookie
$accounts = [];
if (isset($_COOKIE['remembered_accounts'])) {
    $decoded = json_decode($_COOKIE['remembered_accounts'], true);
    if (is_array($decoded)) {
        // Ensure accounts are indexed numerically
        $accounts = array_values($decoded);
    }
}

// If no accounts, redirect to the login page
if (empty($accounts)) {
    header('Location: login.php');
    exit;
}

// Helper function to escape output
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Hero - Middle Earth</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Press Start 2P & VT323 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Original Style Sheet Reference -->
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
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <!-- Original Background Video & Canvas -->
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
                <i data-lucide="swords" class="w-6 h-6 text-[#ffcd75]" stroke-width="2.5"></i>
                <span class="text-[#ffcd75] font-pixel-header text-xs tracking-widest uppercase">local co-op</span>
                <i data-lucide="swords" class="w-6 h-6 text-[#ffcd75]" stroke-width="2.5"></i>
            </div>
        </div>

        <!-- Main Card Body -->
        <div class="bg-[#e4a672] p-1 border-4 border-[#181425] shadow-[10px_10px_0px_0px_rgba(0,0,0,0.4)]">
            <div class="border-4 border-[#c07448] p-6 md:p-8 bg-[#d28b58]">
                
                <!-- Header -->
                <div class="text-center mb-6 relative">
                    <h1 class="text-2xl md:text-3xl font-pixel-header text-[#3e2731] mb-2 drop-shadow-md leading-relaxed">
                        CHOOSE YOUR HERO
                    </h1>
                    <div class="h-1 w-full bg-[#8f563b] my-2 opacity-50"></div>
                    <p class="font-pixel-body text-xl text-[#5d275d] font-bold">
                        Select an account to continue
                    </p>
                </div>

                <!-- Account List -->
                <div class="space-y-4 mb-6">
                    <?php foreach ($accounts as $acc): ?>
                        <?php
                            $name = e($acc['fullname'] ?? $acc['name'] ?? $acc['email'] ?? 'Unknown');
                            $email = e($acc['email'] ?? '');
                            $initial = e(strtoupper(substr($name, 0, 1)));
                        ?>
                        <div class="relative group">
                            <!-- Clickable Card Area -->
                            <a href="login.php?email=<?php echo urlencode($email); ?>" 
                               class="block w-full bg-[#262b44] hover:bg-[#343a5e] border-4 border-[#181425] hover:border-[#d95763] pixel-inset transition-all duration-100 p-3 flex items-center gap-4 group">
                                
                                <!-- Avatar Box -->
                                <div class="w-12 h-12 bg-[#5d275d] border-2 border-[#181425] flex items-center justify-center shrink-0">
                                    <span class="font-pixel-header text-[#ffcd75] text-xl pt-1"><?php echo $initial; ?></span>
                                </div>
                                
                                <!-- Info -->
                                <div class="overflow-hidden">
                                    <div class="font-pixel-header text-xs text-[#ffcd75] truncate mb-1"><?php echo $name; ?></div>
                                    <div class="font-pixel-body text-lg text-white/70 truncate leading-none"><?php echo $email; ?></div>
                                </div>

                                <!-- Arrow Indicator (Visible on Hover) -->
                                <i data-lucide="chevron-right" class="ml-auto text-[#d95763] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </a>

                            <!-- Remove Button (Floating absolute) -->
                            <form action="remove_account.php" method="post" class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                <input type="hidden" name="email" value="<?php echo $email; ?>">
                                <button type="submit" 
                                        class="bg-[#d95763] hover:bg-[#ac3232] text-white p-1 border-2 border-[#181425] shadow-md flex items-center justify-center"
                                        title="Banished from the realm">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Add Account Button -->
                <a href="login.php?from=switcher" class="
                    block w-full relative overflow-hidden group
                    bg-[#8f563b] hover:bg-[#a06245] active:bg-[#a06245] active:translate-y-1
                    text-[#ffcd75] font-pixel-header text-xs md:text-sm py-4 px-6
                    border-b-8 border-r-8 border-[#5d275d] 
                    border-t-4 border-l-4 border-[#ffcd75]
                    transition-all duration-75 text-center flex items-center justify-center gap-3
                ">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    <span>Summon New Hero</span>
                </a>

                <!-- Footer Links -->
                <div class="mt-6 flex justify-center text-[#3e2731] font-pixel-body text-lg opacity-80">
                     <span class="mr-2">New to these lands?</span>
                     <a href="register.php?from=switcher" class="font-bold hover:text-[#d95763] hover:underline flex items-center gap-1">
                        Create Account
                     </a>
                </div>

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