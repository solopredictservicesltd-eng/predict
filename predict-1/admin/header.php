<?php
require_once 'auth.php';
$settings = getSettings($conn);
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo $settings['site_name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '<?php echo $settings['primary_color'] ?: '#059669'; ?>',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-slate-400 p-6 flex flex-col hidden lg:flex">
            <div class="mb-10 px-2">
                <h1 class="text-xl font-black text-white">SurePredictor<span class="text-emerald-500">.</span></h1>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-600 mt-1">Management Portal</p>
            </div>

            <nav class="space-y-2 flex-1">
                <a href="dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'dashboard' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="fas fa-grid-2"></i>
                    <span class="font-bold">Dashboard</span>
                </a>
                <a href="history" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'history' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="fas fa-history"></i>
                    <span class="font-bold">Prediction History</span>
                </a>
                <a href="settings" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'settings' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="fas fa-brain"></i>
                    <span class="font-bold">Gemini Settings</span>
                </a>
                <a href="ads" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'ads' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="fas fa-ad"></i>
                    <span class="font-bold">Ads Management</span>
                </a>
                <a href="seo" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'seo' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="fas fa-search"></i>
                    <span class="font-bold">SEO Manager</span>
                </a>
                <a href="theme" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'theme' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="fas fa-palette"></i>
                    <span class="font-bold">Theme Settings</span>
                </a>
            </nav>

            <div class="pt-6 border-t border-slate-800 space-y-2">
                <a href="profile" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'profile' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i class="fas fa-user-circle"></i>
                    <span class="font-bold">My Profile</span>
                </a>
                <a href="logout" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-500/10 hover:text-red-500 transition-all">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="font-bold">Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            <header class="flex justify-between items-center mb-10">
                <div>
                    <h2 class="text-3xl font-black text-slate-900"><?php echo ucwords(str_replace('_', ' ', $current_page)); ?></h2>
                    <p class="text-slate-500">Welcome back, <?php echo $_SESSION['admin_user']; ?></p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="../" target="_blank" class="p-3 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </header>
