<?php
require_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'];
    $site_description = $_POST['site_description'];
    $gemini_api_key = $_POST['gemini_api_key'];
    $footer_text = $_POST['footer_text'];
    $contact_email = $_POST['contact_email'];

    $stmt = $conn->prepare("UPDATE settings SET site_name = ?, site_description = ?, gemini_api_key = ?, footer_text = ?, contact_email = ? WHERE id = 1");
    $stmt->bind_param("sssss", $site_name, $site_description, $gemini_api_key, $footer_text, $contact_email);

    if ($stmt->execute()) {
        $success = "Settings updated successfully!";
        $settings = getSettings($conn); // Refresh
    } else {
        $error = "Error updating settings: " . $conn->error;
    }
}
?>

<div class="max-w-2xl">
    <?php if (isset($success)): ?>
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 p-4 rounded-xl text-sm mb-6 flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6">
        <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Site Name</label>
            <input type="text" name="site_name" value="<?php echo $settings['site_name']; ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-4 px-6 text-slate-900 focus:ring-2 focus:ring-emerald-500/30 outline-none transition-all">
        </div>

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Site Description</label>
            <textarea name="site_description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-4 px-6 text-slate-900 focus:ring-2 focus:ring-emerald-500/30 outline-none transition-all"><?php echo $settings['site_description']; ?></textarea>
        </div>

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Google Gemini API Key</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-6 flex items-center text-slate-400">
                    <i class="fas fa-key"></i>
                </span>
                <input type="password" name="gemini_api_key" value="<?php echo $settings['gemini_api_key']; ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-4 pl-14 pr-6 text-slate-900 focus:ring-2 focus:ring-emerald-500/30 outline-none transition-all">
            </div>
            <p class="text-[10px] text-slate-400 mt-2">Get your API key from <a href="https://aistudio.google.com/app/apikey" target="_blank" class="text-emerald-600 underline">Google AI Studio</a></p>
        </div>

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Footer Text</label>
            <input type="text" name="footer_text" value="<?php echo $settings['footer_text']; ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-4 px-6 text-slate-900 focus:ring-2 focus:ring-emerald-500/30 outline-none transition-all">
        </div>

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Contact Email</label>
            <input type="email" name="contact_email" value="<?php echo $settings['contact_email']; ?>" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-4 px-6 text-slate-900 focus:ring-2 focus:ring-emerald-500/30 outline-none transition-all">
        </div>

        <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition-all shadow-lg shadow-slate-900/10">
            Save All Changes
        </button>
    </form>
</div>

<?php require_once 'footer.php'; ?>
