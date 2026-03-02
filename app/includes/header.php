<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$selected_lang = $_SESSION['custom_lang'] ?? 'en';
$dir = in_array($selected_lang, ['ps','fa','ar']) ? 'rtl' : 'ltr';
?>
<!DOCTYPE html>
<html lang="<?= $selected_lang ?>" dir="<?= $dir ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>

<link rel="stylesheet" href="/home/style.css?v=<?= time() ?>">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

<style>
/* ===== Header ===== */
.header-user {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px;
    background: #111827;
    color: #fff;
    position: fixed;
    width: 100%;
    top: 0;
    left: 0;
    z-index: 1000;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    overflow-x: auto; /* افقي سکرول که اړتیا وي */
}

/* Menu button */
.menu-btn {
    display: none;
    font-size: 24px;
    cursor: pointer;
}

/* Header Right */
.header-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap; /* ensures items wrap on small screens */
}

/* Logout Button */
.logout-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    background: #2563EB;
    color: #fff;
    transition: 0.3s;
    white-space: nowrap; /* prevents text wrap */
}
.logout-btn:hover { background: #1d4ed8; }

/* Language Switcher */
.language-switcher a {
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 700;
    background: #3498db;
    color: #fff;
    transition: 0.3s;
    white-space: nowrap; /* prevents text wrap */
}
.language-switcher a.active { background: #2563EB; }
.language-switcher a:hover { background: #1d4ed8; }

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .header-user {
        flex-wrap: wrap;
        justify-content: space-between;
        overflow-x: auto; /* افقي سکرول فعال */
    }
    .menu-btn { display: block; }
    .header-right {
        width: 100%;
        justify-content: flex-start;
        gap: 6px;
    }
    .logout-btn, .language-switcher a {
        width: auto; /* allow them to shrink */
        flex: 1 1 auto;
        text-align: center;
    }
}

/* ===== Optional: ensure body scroll works ===== */
body {
    overflow-x: auto; /* افقي سکرول فعال */
    overflow-y: auto; /* عمودي سکرول فعال */
}
</style>
</head>
<body class="<?= $dir ?>">

<div class="header-user">
    <i id="menu-toggle" class="menu-btn las la-bars"></i>
    <div class="header-right">
        <a href="/home/app/logout.php" class="logout-btn">
            <span class="las la-power-off"></span>
            <?= ($selected_lang === 'ps') ? 'وتل' : 'Logout' ?>
        </a>

        <div class="language-switcher">
            <a href="/home/app/auth/set-language.php?lang=en" class="<?= ($selected_lang==='en')?'active':'' ?>">EN</a>
            <a href="/home/app/auth/set-language.php?lang=ps" class="<?= ($selected_lang==='ps')?'active':'' ?>">پښتو</a>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const menuBtn = document.getElementById("menu-toggle");
    const sidebar = document.querySelector(".sidebar");

    if(menuBtn && sidebar){
        menuBtn.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    }
});
</script>
</body>
</html>