<?php
require_once 'config.php';
require_once 'functions.php';

$settings = getSettings($conn);
$seo = getSeoSettings($conn);

$primary_color = $settings['primary_color'] ?? '#059669';
$site_name = $settings['site_name'] ?? 'SurePredictor';
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $settings['dark_mode'] ? 'dark' : ''; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $seo['meta_title'] ?: $site_name; ?></title>
    <meta name="description" content="<?php echo $seo['meta_description']; ?>">

    <!-- PWA -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="<?php echo $primary_color; ?>">
    <link rel="apple-touch-icon" href="<?php echo $settings['site_icon'] ?: 'assets/images/logo.png'; ?>">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $primary_color; ?>',
                        'primary-dark': '<?php echo $primary_color; ?>e6', // approximate
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes roll {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-roll {
            animation: roll 1s ease-in-out;
        }
        .bg-primary { background-color: <?php echo $primary_color; ?>; }
        .text-primary { color: <?php echo $primary_color; ?>; }
        .border-primary { border-color: <?php echo $primary_color; ?>; }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 transition-colors duration-300">

<!-- Splash Screen -->
<div id="splash-screen" class="fixed inset-0 bg-white dark:bg-slate-950 z-[200] flex items-center justify-center transition-opacity duration-500">
    <div class="text-center">
        <img src="<?php echo $settings['site_logo'] ?: 'assets/images/logo.png'; ?>" alt="Logo" class="h-24 w-24 mx-auto mb-4 animate-roll shadow-2xl rounded-3xl">
        <h2 class="text-2xl font-black text-slate-900 dark:text-white">SurePredictor<span class="text-emerald-600">.com</span></h2>
    </div>
</div>

<script>
    window.addEventListener('load', () => {
        setTimeout(() => {
            const splash = document.getElementById('splash-screen');
            if (splash) {
                splash.style.opacity = '0';
                setTimeout(() => splash.remove(), 500);
            }
        }, 1500);
    });
</script>
