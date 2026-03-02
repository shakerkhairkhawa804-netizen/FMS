<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/config.php';
include __DIR__ . '/../function/localization.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/mainbar.php';

$message = '';

// د GET id چیک
if (!isset($_GET['id'])) {
    header("Location: clients.php");
    exit();
}

$client_id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$client_id]);
$client = $stmt->fetch();
$editClient = $client;

if (!$client) {
    header("Location: clients.php");
    exit();
}

// Update client
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name) {
        $stmt = $pdo->prepare("UPDATE clients SET name=?, phone=?, email=? WHERE id=?");
        $stmt->execute([$name, $phone, $email, $client_id]);
        $message = $translations['client_updated'] ?? "Client updated successfully!";
        header("Location: clients.php");
        exit();
    } else {
        $message = $translations['enter_client_name'] ?? "Please enter client name!";
    }
}

// د ژبې مطابق HTML direction
$selected_lang = $_SESSION['custom_lang'] ?? 'en';
$dir_class = in_array($selected_lang, ['ps','fa']) ? 'rtl' : 'ltr';

// د پښتو/انګلیسي ترجمه
$title = $selected_lang == 'ps' ? 'د مراجع تعدیل' : 'Edit Client';
$label_name = $selected_lang == 'ps' ? 'نوم' : 'Name';
$label_phone = $selected_lang == 'ps' ? 'ټیلیفون' : 'Phone';
$label_email = $selected_lang == 'ps' ? 'بریښنالیک' : 'Email';
$button_text = $selected_lang == 'ps' ? 'تعدیل کول' : 'Update Client';
$back_text = $selected_lang == 'ps' ? 'بېرته مراجعو ته' : 'Back to Clients';
?>

<!DOCTYPE html>
<html lang="<?= $selected_lang ?>" dir="<?= $dir_class ?>">
<head>
<meta charset="UTF-8">
<title><?= $title ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../../style.css">
<style>
body{
    font-family: Arial, sans-serif;
    background:#f4f7f8;
    padding:20px;
    margin:0;
    box-sizing: border-box;
}

.form-box{
    background:#fff;
    padding:25px 30px;
    border-radius:12px;
    box-shadow:0 8px 20px rgba(0,0,0,.1);
    width:100%;
    max-width:500px; /* کمپیوټر کې فارم بکس اعظمي */
    margin:auto;
    box-sizing: border-box;
}

/* ===== Labels ===== */
label{
    display:block;
    margin-bottom:5px;
    font-weight:500;
}

/* ===== Inputs, Select, Textarea & Button ===== */
input, select, textarea, button{
    width:100%;
    padding:12px;
    margin-bottom:12px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:15px;
    box-sizing: border-box;
}

textarea{
    min-height:70px;
}

/* ===== Button styling ===== */
button{
    background:#007bff;
    color:#fff;
    border:none;
    cursor:pointer;
    font-weight:bold;
    transition: background 0.3s ease;
}

button:hover{
    background:#0056b3;
}

/* ===== Messages ===== */
.message{
    text-align:center;
    color:green;
    font-weight:bold;
    margin-bottom:10px;
}

/* ===== Responsive adjustments ===== */
@media (max-width: 1024px){
    .form-box{
        max-width:70%; /* کمپیوټر لږ کوچنی */
    }
}

@media (max-width: 768px){
    .form-box{
        max-width:90%;
        padding:20px;
    }
    input, select, textarea, button{
        font-size:14px;
        padding:10px;
    }
}

@media (max-width: 480px){
    body{
        padding:15px;
    }
    .form-box{
        max-width:100%;
        padding:15px;
    }
    input, select, textarea, button{
        font-size:13px;
        padding:8px;
    }
    label{
        font-size:14px;
    }
}
</style>
</head>
<body class="<?= $dir_class ?>">
<div class="form-box">
    <h2><?= $title ?></h2>

    <?php if($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <label><?= $label_name ?></label>
        <input type="text" name="name" value="<?= htmlspecialchars($client['name']) ?>" required>

        <label><?= $label_phone ?></label>
        <input type="text" name="phone" value="<?= htmlspecialchars($client['phone']) ?>">

        <label><?= $label_email ?></label>
        <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>">

        <button type="submit"><?= $button_text ?></button>
    </form>

    <p style="text-align:center;"><a href="clients.php"><?= $back_text ?></a></p>
</div>
<script src="/home/assets/js/main.js"></script>
</body>
</html>
