<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../function/localization.php';
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/mainbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$selected_lang = $_SESSION['custom_lang'] ?? $translations['lang'] ?? 'en';
$dir = in_array($selected_lang, ['ps','fa','ar']) ? 'rtl' : 'ltr';
$message = '';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM client_payments WHERE client_id=?");
    $stmt->execute([$id]);
    if ($stmt->fetchColumn() > 0) {
        $message = t('consumer_has_expenses', $translations, $selected_lang);
    } else {
        $pdo->prepare("DELETE FROM clients WHERE id=?")->execute([$id]);
        header("Location: clients.php");
        exit;
    }
}

// EDIT
$editClient = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id=?");
    $stmt->execute([(int)$_GET['edit']]);
    $editClient = $stmt->fetch(PDO::FETCH_ASSOC);
}

// ADD / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    if (!empty($_POST['id'])) {
        $stmt = $pdo->prepare("UPDATE clients SET name=?, phone=?, email=? WHERE id=?");
        $stmt->execute([$name, $phone, $email, $_POST['id']]);
        $message = t('consumer_updated', $translations, $selected_lang);
    } else {
        $stmt = $pdo->prepare("INSERT INTO clients (name, phone, email, user_id) VALUES (?,?,?,?)");
        $stmt->execute([$name, $phone, $email, $_SESSION['user_id']]);
        $message = t('consumer_added', $translations, $selected_lang);
    }
}

// FETCH
$clients = $pdo->prepare("SELECT * FROM clients WHERE user_id=? ORDER BY id DESC");
$clients->execute([$_SESSION['user_id']]);
$clients = $clients->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($selected_lang) ?>" dir="<?= $dir ?>">
<head>
<meta charset="UTF-8">
<title><?= t('consumers_title',$translations,$selected_lang) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<style>
:root{
    --primary:#2563eb;
    --primary-dark:#1e40af;
    --danger:#dc2626;
    --bg:#f1f5f9;
    --card:#ffffff;
    --text:#1f2937;
    --radius:14px;
}

body{
    font-family:"Segoe UI",Tahoma,sans-serif;
    background:var(--bg);
    padding:30px;
    color:var(--text);
    direction: <?= $dir ?>;
    box-sizing: border-box;
    margin:0;
}

.container{
    max-width:900px; /* کمپیوټر لپاره پراخ */
    margin:auto;
    width:100%;
}

.box{
    background:var(--card);
    border-radius:var(--radius);
    padding:25px;
    box-shadow:0 15px 35px rgba(0,0,0,.08);
    margin-bottom:30px;
    box-sizing: border-box;
    transition: max-width 0.3s ease;
}

/* Heading */
h2{
    margin-bottom:20px;
    font-size:22px;
    border-bottom:2px solid #e5e7eb;
    padding-bottom:10px;
}

/* Messages */
.alert{
    background:#ecfdf5;
    color:#065f46;
    padding:12px;
    border-radius:10px;
    margin-bottom:15px;
    text-align:center;
    font-weight:600;
}

/* Inputs, select, textarea & button */
input, textarea, select, button{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:10px;
    border:1px solid #d1d5db;
    font-size:15px;
    box-sizing: border-box;
    background:#fff;
    color:#1f2937;
}

select{
    background:#f9fafb; /* لږ روښانه */
    appearance:none;
    -webkit-appearance:none;
    -moz-appearance:none;
}

/* Textarea */
textarea{
    min-height:80px;
    resize: vertical;
}

/* Buttons */
button{
    background:var(--primary);
    color:#fff;
    font-weight:600;
    cursor:pointer;
    border:none;
    transition: all 0.2s ease;
}

button:hover{
    background:var(--primary-dark);
    transform: translateY(-2px);
}

/* Table */
table{
    width:100%;
    border-collapse:collapse;
    font-size:14px;
}

th{
    background:var(--primary);
    color:#fff;
    padding:12px;
}

td{
    padding:12px;
    border-bottom:1px solid #e5e7eb;
    text-align:center;
}

/* Action Buttons */
.action-btn{
    padding:6px 12px;
    border-radius:8px;
    color:#fff;
    text-decoration:none;
    font-size:13px;
    font-weight:600;
}

.edit-btn{background:#16a34a;}
.delete-btn{background:var(--danger);}

/* ===== Responsive adjustments ===== */

/* کمپیوټر */
@media (min-width: 1025px){
    .box{
        max-width:60%; /* فارم بکس پراخ */
    }
}

/* لپ ټاپ / منځني ډیسپلې */
@media (max-width: 1024px){
    .box{
        max-width:75%;
        padding:22px;
    }
    input, select, textarea, button{
        font-size:14px;
        padding:12px;
    }
    th, td{
        font-size:13px;
        padding:10px;
    }
}

/* ټابلیټ */
@media (max-width: 768px){
    .box{
        max-width:90%;
        padding:20px;
    }
    input, select, textarea, button{
        font-size:14px;
        padding:10px;
    }
    th, td{
        font-size:13px;
        padding:10px;
    }
}

/* موبایل */
@media (max-width: 480px){
    body{padding:15px;}
    .box{
        max-width:100%;
        padding:15px;
    }
    input, select, textarea, button{
        font-size:13px;
        padding:8px;
    }
    th, td{
        font-size:12px;
        padding:8px;
    }
    h2{font-size:18px;}
}
</style>
</head>
<body>
<div class="container">

<div class="box">
<h2><?= $editClient ? t('edit_consumer', $translations, $selected_lang) : t('add_consumer', $translations, $selected_lang) ?></h2>

<?php if ($message): ?><div class="alert"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<form method="post">
<?php if ($editClient): ?><input type="hidden" name="id" value="<?= $editClient['id'] ?>"><?php endif; ?>
<input type="text" name="name" required placeholder="<?= t('placeholder_name', $translations, $selected_lang) ?>" value="<?= htmlspecialchars($editClient['name'] ?? '') ?>">
<input type="text" name="phone" placeholder="<?= t('placeholder_phone', $translations, $selected_lang) ?>" value="<?= htmlspecialchars($editClient['phone'] ?? '') ?>">
<input type="email" name="email" placeholder="<?= t('placeholder_email', $translations, $selected_lang) ?>" value="<?= htmlspecialchars($editClient['email'] ?? '') ?>">
<button type="submit"><?= $editClient ? t('update', $translations, $selected_lang) : t('add_consumer', $translations, $selected_lang) ?></button>
</form>
</div>

<div class="box">
<h2><?= t('consumer_list', $translations, $selected_lang) ?></h2>
<table>
<tr>
<th>#</th><th><?= t('name', $translations, $selected_lang) ?></th>
<th><?= t('phone', $translations, $selected_lang) ?></th>
<th><?= t('email', $translations, $selected_lang) ?></th>
<th><?= t('actions', $translations, $selected_lang) ?></th>
</tr>
<?php foreach ($clients as $c): ?>
<tr>
<td><?= $c['id'] ?></td>
<td><?= htmlspecialchars($c['name']) ?></td>
<td><?= htmlspecialchars($c['phone']) ?></td>
<td><?= htmlspecialchars($c['email']) ?></td>
<td>
<a class="action-btn edit-btn" href="?edit=<?= $c['id'] ?>"><?= t('update', $translations, $selected_lang) ?></a>
<a class="action-btn delete-btn" href="?delete=<?= $c['id'] ?>" onclick="return confirm('<?= t('delete_confirm', $translations, $selected_lang) ?>')"><?= t('delete', $translations, $selected_lang) ?></a>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>

</div>
</body>
</html>
<?php ob_end_flush(); ?>