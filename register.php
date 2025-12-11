<?php
session_start();

// This block attempts to redirect logged-in users from accessing the register page directly
// It checks for a 'remembered_accounts' cookie and redirects to an account switcher if found.
$isDirectAccess = !isset($_GET['from']);
if ($_SERVER["REQUEST_METHOD"] == "GET" && $isDirectAccess) {
    if (isset($_COOKIE['remembered_accounts'])) {
        $accounts = json_decode($_COOKIE['remembered_accounts'], true);
        if (is_array($accounts) && !empty($accounts)) {
            header("Location: account_switcher.php");
            exit;
        }
    }
}

$errors = [];
$fullname = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establishes database connection
    require 'database.php';

    // Retrieves and trims form data
    $fullname = trim($_POST["name"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $password_repeat = $_POST["confirm-password"] ?? '';

    // --- Form Validation ---
    if (empty($fullname) || empty($email) || empty($password) || empty($password_repeat)) {
        $errors[] = "All fields are required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }
    if (strlen($password) < 5) {
        $errors[] = "Password must be at least 5 characters long.";
    }
    if ($password !== $password_repeat) {
        $errors[] = "The passwords do not match.";
    }

    // --- Database Interaction ---
    if (empty($errors)) {
        // Check if email already exists to prevent duplicates
        $sql = "SELECT user_id FROM users WHERE Email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $errors[] = "An account with this email already exists.";
        }
        mysqli_stmt_close($stmt);
    }

    if (empty($errors)) {
        // Inserts the new user into the database
        $sql = "INSERT INTO users (Fullname, Email, Password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        // Binds parameters to the SQL query
        mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $password);

        // Executes the statement and redirects on success
        if (mysqli_stmt_execute($stmt)) {
            header("Location: login.php?registration=success");
            exit();
        } else {
            // Generic error if insertion fails
            $errors[] = "Something went wrong. Please try again later.";
            // For debugging: you can log the specific error like this:
            // error_log(mysqli_error($conn));
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join the Guild - Registration</title>
    
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
                <i data-lucide="shield" class="w-6 h-6 text-[#ffcd75]" stroke-width="2.5"></i>
                <span class="text-[#ffcd75] font-pixel-header text-xs tracking-widest uppercase">The Rusty Pixel</span>
                <i data-lucide="shield" class="w-6 h-6 text-[#ffcd75]" stroke-width="2.5"></i>
            </div>
        </div>

        <!-- Main Card Body -->
        <div class="bg-[#e4a672] p-1 border-4 border-[#181425] shadow-[10px_10px_0px_0px_rgba(0,0,0,0.4)]">
            <div class="border-4 border-[#c07448] p-6 md:p-8 bg-[#d28b58]">
                
                <!-- Header -->
                <div class="text-center mb-6 relative">
                    <h1 class="text-3xl md:text-4xl font-pixel-header text-[#3e2731] mb-2 drop-shadow-md">
                        RECRUIT
                    </h1>
                    <div class="h-1 w-full bg-[#8f563b] my-2 opacity-50"></div>
                    <p class="font-pixel-body text-xl text-[#5d275d] font-bold">
                        Forge your legacy, adventurer.
                    </p>
                </div>

                <!-- PHP Errors Display -->
                <?php if (!empty($errors)): ?>
                    <div class="mb-6 bg-[#262b44] border-l-4 border-[#d95763] p-4 pixel-inset">
                        <div class="flex items-start gap-3">
                            <i data-lucide="alert-triangle" class="text-[#d95763] w-6 h-6 shrink-0 mt-1"></i>
                            <div class="text-[#feae34] font-pixel-body text-lg">
                                <?php foreach ($errors as $error): ?>
                                    <p><?= htmlspecialchars($error) ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form action="register.php" method="POST" class="space-y-4 relative z-10">
                    
                    <!-- Full Name -->
                    <div class="space-y-1 group">
                        <label for="name" class="font-pixel-header text-[10px] text-[#3e2731] uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="user" size="14"></i> Full Name
                        </label>
                        <div class="relative">
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($fullname) ?>" required
                                class="w-full bg-[#262b44] text-white font-pixel-body text-2xl px-4 py-2 border-4 border-[#181425] focus:outline-none focus:border-[#d95763] placeholder-white placeholder-opacity-100 transition-colors shadow-inner pixel-inset"
                                placeholder="Sir Lancelot">
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1 group">
                        <label for="email" class="font-pixel-header text-[10px] text-[#3e2731] uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="mail" size="14"></i> Email Address
                        </label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required
                                class="w-full bg-[#262b44] text-white font-pixel-body text-2xl px-4 py-2 border-4 border-[#181425] focus:outline-none focus:border-[#d95763] placeholder-white placeholder-opacity-100 transition-colors shadow-inner pixel-inset"
                                placeholder="scrolls@kingdom.com">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1 group">
                        <label for="password" class="font-pixel-header text-[10px] text-[#3e2731] uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="key" size="14"></i> Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="w-full bg-[#262b44] text-white font-pixel-body text-2xl px-4 py-2 border-4 border-[#181425] focus:outline-none focus:border-[#d95763] placeholder-white placeholder-opacity-100 transition-colors shadow-inner pixel-inset pr-12"
                                placeholder="••••••••">
                            <button type="button" onclick="toggleVisibility('password')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-[#d95763] focus:outline-none z-20">
                                <i data-lucide="eye" id="eye-password" class="w-6 h-6"></i>
                                <i data-lucide="eye-off" id="eye-off-password" class="w-6 h-6 hidden"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-1 group">
                        <label for="confirm-password" class="font-pixel-header text-[10px] text-[#3e2731] uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="shield-check" size="14"></i> Confirm Rune
                        </label>
                        <div class="relative">
                            <input type="password" id="confirm-password" name="confirm-password" required
                                class="w-full bg-[#262b44] text-white font-pixel-body text-2xl px-4 py-2 border-4 border-[#181425] focus:outline-none focus:border-[#d95763] placeholder-white placeholder-opacity-100 transition-colors shadow-inner pixel-inset pr-12"
                                placeholder="••••••••">
                            <button type="button" onclick="toggleVisibility('confirm-password')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-white hover:text-[#d95763] focus:outline-none z-20">
                                <i data-lucide="eye" id="eye-confirm-password" class="w-6 h-6"></i>
                                <i data-lucide="eye-off" id="eye-off-confirm-password" class="w-6 h-6 hidden"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="
                        w-full relative overflow-hidden group mt-6
                        bg-[#d95763] hover:bg-[#ac3232] active:bg-[#ac3232] active:translate-y-1
                        text-white font-pixel-header text-xs md:text-sm py-4 px-6
                        border-b-8 border-r-8 border-[#5d275d] 
                        border-t-4 border-l-4 border-[#ffcd75]
                        transition-all duration-75 flex items-center justify-center gap-3
                    ">
                        <span>Sign Manual</span>
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform" stroke-width="3"></i>
                    </button>
                    
                </form>

                <!-- Footer Links -->
                <div class="mt-6 flex justify-center text-[#3e2731] font-pixel-body text-lg opacity-80">
                     <span class="mr-2">Already guilded?</span>
                     <a href="login.php?from=register" class="font-bold hover:text-[#d95763] hover:underline flex items-center gap-1">
                        Log In
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

        // Toggle Password Visibility
        function toggleVisibility(fieldId) {
            const field = document.getElementById(fieldId);
            const eyeIcon = document.getElementById('eye-' + fieldId);
            const eyeOffIcon = document.getElementById('eye-off-' + fieldId);

            if (field.type === 'password') {
                field.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                field.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }

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