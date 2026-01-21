<?php
session_start();

if (file_exists('../includes/config.php')) {
    header('Location: ../index');
    exit;
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;

$php_version = phpversion();
$curl_enabled = function_exists('curl_version');
$mysqli_enabled = extension_loaded('mysqli');
$pdo_enabled = extension_loaded('pdo_mysql');
$json_enabled = extension_loaded('json');

$requirements_met = version_compare($php_version, '8.1.0', '>=') && $curl_enabled && ($mysqli_enabled || $pdo_enabled) && $json_enabled;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 2) {
        $db_host = $_POST['db_host'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_pass'];
        $db_name = $_POST['db_name'];

        try {
            $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
            if ($conn->connect_error) {
                $error = "Connection failed: " . $conn->connect_error;
            } else {
                $_SESSION['db_details'] = [
                    'host' => $db_host,
                    'user' => $db_user,
                    'pass' => $db_pass,
                    'name' => $db_name
                ];

                // Create tables
                $sql = file_get_contents('schema.sql');
                if ($conn->multi_query($sql)) {
                    do {
                        if ($result = $conn->store_result()) {
                            $result->free();
                        }
                    } while ($conn->more_results() && $conn->next_result());
                    header('Location: ?step=3');
                    exit;
                } else {
                    $error = "Error creating tables: " . $conn->error;
                }
            }
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    } elseif ($step == 3) {
        $admin_user = $_POST['admin_user'];
        $admin_pass = password_hash($_POST['admin_pass'], PASSWORD_DEFAULT);

        $db = $_SESSION['db_details'];
        $conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);

        $stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
        $stmt->bind_param("ss", $admin_user, $admin_pass);

        if ($stmt->execute()) {
            // Generate config.php
            $config_content = "<?php\n";
            $config_content .= "define('DB_HOST', '" . addslashes($db['host']) . "');\n";
            $config_content .= "define('DB_USER', '" . addslashes($db['user']) . "');\n";
            $config_content .= "define('DB_PASS', '" . addslashes($db['pass']) . "');\n";
            $config_content .= "define('DB_NAME', '" . addslashes($db['name']) . "');\n\n";
            $config_content .= "\$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);\n";
            $config_content .= "if (\$conn->connect_error) {\n";
            $config_content .= "    die('Connection failed: ' . \$conn->connect_error);\n";
            $config_content .= "}\n";

            file_put_contents('../includes/config.php', $config_content);
            header('Location: ?step=4');
            exit;
        } else {
            $error = "Error creating admin account: " . $stmt->error;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SurePredictor Installation - Step <?php echo $step; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl p-8 border border-slate-100">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center p-3 bg-emerald-50 rounded-2xl mb-4 border border-emerald-100">
                <i class="fas fa-magic text-2xl text-emerald-600"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900">SurePredictor <span class="text-emerald-600">Setup</span></h1>
            <p class="text-slate-500 text-sm">Follow the steps to install your application</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-xl text-sm mb-6 flex items-center gap-3">
                <i class="fas fa-circle-exclamation"></i>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <div class="space-y-6">
                <h2 class="font-bold text-slate-800 border-b pb-2">System Requirements</h2>
                <ul class="space-y-3">
                    <li class="flex justify-between items-center text-sm">
                        <span class="text-slate-600">PHP Version (8.1+)</span>
                        <span class="<?php echo version_compare($php_version, '8.1.0', '>=') ? 'text-emerald-600' : 'text-red-600'; ?> font-bold">
                            <?php echo $php_version; ?> <?php echo version_compare($php_version, '8.1.0', '>=') ? '✓' : '✗'; ?>
                        </span>
                    </li>
                    <li class="flex justify-between items-center text-sm">
                        <span class="text-slate-600">cURL Extension</span>
                        <span class="<?php echo $curl_enabled ? 'text-emerald-600' : 'text-red-600'; ?> font-bold">
                            <?php echo $curl_enabled ? 'Enabled ✓' : 'Disabled ✗'; ?>
                        </span>
                    </li>
                    <li class="flex justify-between items-center text-sm">
                        <span class="text-slate-600">MySQLi / PDO</span>
                        <span class="<?php echo ($mysqli_enabled || $pdo_enabled) ? 'text-emerald-600' : 'text-red-600'; ?> font-bold">
                            <?php echo ($mysqli_enabled || $pdo_enabled) ? 'Enabled ✓' : 'Disabled ✗'; ?>
                        </span>
                    </li>
                    <li class="flex justify-between items-center text-sm">
                        <span class="text-slate-600">JSON Extension</span>
                        <span class="<?php echo $json_enabled ? 'text-emerald-600' : 'text-red-600'; ?> font-bold">
                            <?php echo $json_enabled ? 'Enabled ✓' : 'Disabled ✗'; ?>
                        </span>
                    </li>
                </ul>

                <?php if ($requirements_met): ?>
                    <a href="?step=2" class="block w-full text-center py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-600/20">
                        Continue to Database Setup
                    </a>
                <?php else: ?>
                    <div class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-xl text-xs">
                        Please resolve the requirements above to continue.
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($step == 2): ?>
            <form method="POST" class="space-y-4">
                <h2 class="font-bold text-slate-800 border-b pb-2">Database Details</h2>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">DB Host</label>
                    <input type="text" name="db_host" value="localhost" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">DB Name</label>
                    <input type="text" name="db_name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">DB User</label>
                    <input type="text" name="db_user" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">DB Password</label>
                    <input type="password" name="db_pass" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                </div>
                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-600/20">
                    Install Schema & Continue
                </button>
            </form>

        <?php elseif ($step == 3): ?>
            <form method="POST" class="space-y-4">
                <h2 class="font-bold text-slate-800 border-b pb-2">Admin Account</h2>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Admin Username</label>
                    <input type="text" name="admin_user" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Admin Password</label>
                    <input type="password" name="admin_pass" required class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-emerald-500/30 outline-none">
                </div>
                <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-600/20">
                    Complete Installation
                </button>
            </form>

        <?php elseif ($step == 4): ?>
            <div class="text-center space-y-6">
                <div class="h-20 w-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-4xl">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">Congratulations!</h2>
                <p class="text-slate-500 text-sm">SurePredictor has been successfully installed. You can now access your dashboard and manage your site.</p>

                <div class="bg-amber-50 border border-amber-100 text-amber-700 p-4 rounded-xl text-xs text-left">
                    <p class="font-bold mb-1"><i class="fas fa-triangle-exclamation mr-1"></i> Security Note:</p>
                    Please delete the <strong>install/</strong> directory from your server for security reasons.
                </div>

                <div class="space-y-3">
                    <a href="../admin/" class="block w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition-all">
                        Go to Admin Panel
                    </a>
                    <a href="../" class="block w-full py-3 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold rounded-xl transition-all border border-emerald-200">
                        View Homepage
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-8 flex justify-between items-center">
            <div class="flex gap-1">
                <?php for($i=1; $i<=4; $i++): ?>
                    <div class="h-1.5 w-8 rounded-full <?php echo $i <= $step ? 'bg-emerald-500' : 'bg-slate-100'; ?>"></div>
                <?php endfor; ?>
            </div>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Step <?php echo $step; ?> of 4</span>
        </div>
    </div>
</body>
</html>
