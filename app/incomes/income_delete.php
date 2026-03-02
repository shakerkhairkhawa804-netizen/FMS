<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../function/localization.php';
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$message = '';
if (isset($_GET['id'])) {
    $income_id = $_GET['id'];

    // Delete only if income belongs to logged-in user
    $stmt = $pdo->prepare("DELETE FROM incomes WHERE id = ? AND user_id = ?");
    $stmt->execute([$income_id, $_SESSION['user_id']]);

    $message = "عاید په بریالیتوب سره حذف شو!";
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>حذف عاید</title>
<style>
/* ===== Body ===== */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f2f5;
    padding: 20px;
    margin:0;
}

/* ===== Container ===== */
.container {
    max-width: 600px;
    margin: 30px auto;
    background: #fff;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    text-align: center;
    transition: all 0.3s ease;
}

/* ===== Heading ===== */
h2 {
    color: #333;
    margin-bottom: 25px;
}

/* ===== Message ===== */
.message {
    color: green;
    font-weight: 600;
    margin-bottom: 20px;
}

/* ===== Buttons & Links ===== */
button, a {
    display: inline-block;
    width: 100%;
    padding: 12px 0;
    border-radius: 8px;
    font-weight: 600;
    font-size: 16px;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

button {
    border: none;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: #fff;
    margin-top: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

button:hover {
    background: linear-gradient(135deg, #1e40af, #2563eb);
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

a {
    background: #e5f0ff;
    color: #2563eb;
    margin-top: 15px;
    padding: 12px 0;
}

a:hover {
    background: #d0e0ff;
    text-decoration: none;
}

/* ===== Responsive ===== */
@media (max-width: 480px) {
    .container { padding: 20px; }
    button, a { font-size: 14px; padding: 10px 0; }
}
</style>
</head>
<body>
<div class="container">
    <h2>حذف عاید</h2>
    <?php if ($message): ?>
        <div class="message"><?= $message ?></div>
    <?php else: ?>
        <div class="message">د حذف پروسه ترسره شوه.</div>
    <?php endif; ?>
    <a href="income_add.php">بېرته عایداتو ته لاړ شئ</a>
</div>
</body>
</html>