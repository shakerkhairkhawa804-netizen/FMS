<?php
session_start();
require_once __DIR__ . '/config/config.php';

// د ژبې ټاکل (Session یا Cookie)
$selected_lang = $_SESSION['custom_lang'] ?? $_COOKIE['custom_lang'] ?? 'en';
$translations = include __DIR__ . '/function/localization.php';

// که کارونکی لاګین شوی وي، dashboard ته لاړ شه

if (isset($_SESSION['username'])) {
    header('Location: dashboard.php'); // که مخکې لاګین وي، dashboard ته
    exit;
}


$message = '';

// د login فورم پروسس کول
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
            // ✅ لاګین بریالی شو
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['custom_lang'] = $selected_lang;

            // dashboard ته redirect
            header("Location: dashboard.php");
            exit();
        } else {
            $message = ($selected_lang === 'ps') ? "کارن نوم یا پاسورډ ناسم دی!" : "Username or password is incorrect!";
        }
    } else {
        $message = ($selected_lang === 'ps') ? "لطفاً ټول فیلډونه ډک کړئ!" : "Please fill in all fields!";
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $selected_lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= ($selected_lang === 'ps') ? 'لاګین' : 'Login' ?></title>
<style>
/* ===== Body ===== */
body {
    font-family: Arial, sans-serif;
    background: #f0f0f0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* ===== Login Box ===== */
.login-box {
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

/* ===== Inputs ===== */
input[type="text"],
input[type="password"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 16px;
}

/* ===== Button ===== */
button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 6px;
    background: #007bff;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
    margin-top: 10px;
    transition: 0.3s;
}
button:hover {
    background: #0056b3;
}

/* ===== Error Message ===== */
.error {
    color: red;
    margin-bottom: 15px;
    font-weight: 600;
}

/* ===== Responsive ===== */
@media (max-width: 480px) {
    .login-box {
        padding: 30px 20px;
    }
}
</style>
</head>
<body>
<div class="login-box">
    <h2><?= ($selected_lang === 'ps') ? 'لاګین' : 'Login' ?></h2>
    <?php if($message) echo "<p class='error'>$message</p>"; ?>
    <form method="post">
        <input type="text" name="username" placeholder="<?= ($selected_lang === 'ps') ? 'کارن نوم' : 'Username' ?>" required>
        <input type="password" name="password" placeholder="<?= ($selected_lang === 'ps') ? 'پاسورډ' : 'Password' ?>" required>
        <button type="submit"><?= ($selected_lang === 'ps') ? 'لاګین' : 'Login' ?></button>
    </form>
</div>
</body>
</html>