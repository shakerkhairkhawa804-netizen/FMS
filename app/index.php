<?php
session_start();

require_once __DIR__ . '/config/config.php';
$translations = include __DIR__ . '/function/localization.php';

// Translation helper function
function t($key) {
    global $translations;
    $lang = $_SESSION['custom_lang'] ?? 'en';
    return $translations[$key][$lang] ?? $key;
}

// Language & direction
$lang = $_SESSION['custom_lang'] ?? 'en';
$dir = in_array($lang, ['ps','fa','ar']) ? 'rtl' : 'ltr';

// Login check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('dashboard') ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    
</head>
<body>

<?php include __DIR__ . '/includes/sidebar.php'; ?>
<?php include __DIR__ . '/includes/header.php'; ?>

   
</div>
<div class="main-content">
    <?php include __DIR__ . '/includes/mainbar.php'; ?>
</div>

<!-- د body پای کې، د </body> مخکې -->

</body>
</html>

<script src="assets/js/main.js"></script>
</body>
</html>