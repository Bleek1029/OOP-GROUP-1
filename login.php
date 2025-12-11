<?php
session_start();

if (isset($_SESSION["user"])) {
   header("Location: player selection.php");
   exit();
}

// Corrected the redirect logic to check for 'from=switcher' or 'from=register'
if (isset($_COOKIE['remembered_accounts']) && !isset($_GET['from']) && !isset($_GET['email']) && !isset($_POST['login'])) {
    $accounts = json_decode($_COOKIE['remembered_accounts'], true);
    if (!empty($accounts)) {
        header('Location: player selection.php');
        exit();
    }
}

// Assuming database.php exists, otherwise mock for UI demo
if (file_exists('database.php')) {
    require_once "database.php";
} else {
    // Fallback mock
    $conn = true;
}

$errors = array();

if (isset($_POST["login"])) {
    // Mocking DB connection for UI demo if needed
    if (!isset($conn)) $conn = true;

    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';

    if (empty($email) || empty($password)) {
        array_push($errors, "Email and password are required");
    } else {
        // Only run real DB checks if file exists, otherwise simulate for UI
        if (file_exists('database.php')) {
            $sql = "SELECT * FROM users WHERE Email = ?";
            $stmt = mysqli_stmt_init($conn);
            if (mysqli_stmt_prepare($stmt, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);

                if ($user && isset($user["Password"]) && $password === $user["Password"]) {
                    $_SESSION["user"] = "yes";
                    $_SESSION["user_id"] = $user["user_id"];
                    $_SESSION["user_name"] = $user["Fullname"];

                    $remembered_accounts = isset($_COOKIE['remembered_accounts']) ? json_decode($_COOKIE['remembered_accounts'], true) : [];
                    if (!is_array($remembered_accounts)) {
                        $remembered_accounts = [];
                    }
                    
                    $account_exists = false;
                    foreach ($remembered_accounts as $account) {
                        if ($account['user_id'] === $user['user_id']) {
                            $account_exists = true;
                            break;
                        }
                    }
                    if (!$account_exists) {
                        $remembered_accounts[] = ['user_id' => $user['user_id'], 'fullname' => $user['Fullname'], 'email' => $user['Email']];
                    }
                    setcookie('remembered_accounts', json_encode(array_values($remembered_accounts)), time() + (86400 * 365), "/");

                    header("Location: player selection.php");
                    exit();
                } else {
                    array_push($errors, "Wrong email or password");
                }
            } else {
                array_push($errors, "Something went wrong");
            }
        }
    }
}

$prefill_email = '';
if (isset($_GET['email'])) {
    $prefill_email = $_GET['email'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guild Login</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Press Start 2P & VT323 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&family=VT323&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Original Style Sheet Reference -->
    <link rel="stylesheet" href="style.css?v=10">

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
                        LOGIN
                    </h1>
                    <div class="h-1 w-full bg-[#8f563b] my-2 opacity-50"></div>
                    <p class="font-pixel-body text-xl text-[#5d275d] font-bold">
                        Enter your credentials, traveler.
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
                <form action="login.php" method="POST" class="space-y-4 relative z-10">
                    
                    <!-- Email -->
                    <div class="space-y-1 group">
                        <label for="email" class="font-pixel-header text-[10px] text-[#3e2731] uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="mail" size="14"></i> Email Address
                        </label>
                        <div class="relative">
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($prefill_email) ?>" required
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

                    <!-- Submit Button -->
                    <button type="submit" name="login" class="
                        w-full relative overflow-hidden group mt-6
                        bg-[#d95763] hover:bg-[#ac3232] active:bg-[#ac3232] active:translate-y-1
                        text-white font-pixel-header text-xs md:text-sm py-4 px-6
                        border-b-8 border-r-8 border-[#5d275d] 
                        border-t-4 border-l-4 border-[#ffcd75]
                        transition-all duration-75 flex items-center justify-center gap-3
                    ">
                        <span>Open Gate</span>
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform" stroke-width="3"></i>
                    </button>
                    
                </form>

                <!-- Footer Links -->
                <div class="mt-6 flex justify-center text-[#3e2731] font-pixel-body text-lg opacity-80">
                     <span class="mr-2">New Hero?</span>
                     <a href="register.php" class="font-bold hover:text-[#d95763] hover:underline flex items-center gap-1">
                        Register
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