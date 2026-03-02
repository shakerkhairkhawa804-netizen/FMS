<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$selected_lang = $_SESSION['custom_lang'] ?? 'en';
$dir = in_array($selected_lang, ['ps','fa','ar']) ? 'rtl' : 'ltr';
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
?>

<!-- Sidebar Toggle (for mobile) -->
<input type="checkbox" id="menu-toggle" style="display:none;">

<aside class="sidebar <?= $dir ?>">
    <!-- Profile -->
    <div class="profile">
        <div class="profile-img" style="background-image:url('../img/rafiullah.JPG')"></div>
        <h4><?= $username ?></h4>
        <small><?= ($selected_lang==='ps')?'سرپرست':'Head of Household' ?></small>
    </div>

    <!-- Menu -->
    <nav class="side-menu">
        <a href="/home/app/dashboard.php" class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php'?'active':'' ?>">
            <i class="las la-home"></i> <?= ($selected_lang==='ps')?'کنترول':'Dashboard' ?>
        </a>
        <a href="/home/app/Expenses/expenses_add.php" class="<?= basename($_SERVER['PHP_SELF'])=='expenses_add.php'?'active':'' ?>">
            <i class="las la-money-bill"></i> <?= ($selected_lang==='ps')?'مصارف اضافه':'Add Expenses' ?>
        </a>
        <a href="/home/app/Expenses/monthly_report.php" class="<?= basename($_SERVER['PHP_SELF'])=='monthly_report.php'?'active':'' ?>">
            <i class="las la-chart-bar"></i> <?= ($selected_lang==='ps')?'راپور':'Reports' ?>
        </a>
        <a href="/home/app/incomes/income_add.php" class="<?= basename($_SERVER['PHP_SELF'])=='income_add.php'?'active':'' ?>">
            <i class="las la-wallet"></i> <?= ($selected_lang==='ps')?'عاید اضافه':'Add Income' ?>
        </a>
        <a href="/home/app/client/clients.php" class="<?= basename($_SERVER['PHP_SELF'])=='clients.php'?'active':'' ?>">
            <i class="las la-user-plus"></i> <?= ($selected_lang==='ps')?'مشتریان':'Clients' ?>
        </a>
        <a href="/home/app/client/client_payments.php" class="<?= basename($_SERVER['PHP_SELF'])=='client_payments.php'?'active':'' ?>">
            <i class="las la-receipt"></i> <?= ($selected_lang==='ps')?'تادیات':'Payments' ?>
        </a>
        <a href="/home/app/client/monthly_report.php" class="<?= basename($_SERVER['PHP_SELF'])=='monthly_report.php'?'active':'' ?>">
            <i class="las la-calendar-alt"></i> <?= ($selected_lang==='ps')?'میاشتنۍ راپور':'Monthly Report' ?>
        </a>
    </nav>
</aside>

<!-- Sidebar CSS -->
<style>
/* ===== Sidebar ===== */
.sidebar {
    position: fixed;
    top: 60px; /* header height */
    left: 0;
    width: 220px;
    height: calc(100vh - 60px);
    background: #111827;
    color: #fff;
    overflow-y: auto;  /* vertical scroll */
    overflow-x: hidden; /* hide horizontal overflow */
    padding-top: 20px;
    transform: translateX(-100%); /* hidden by default */
    transition: transform 0.3s ease;
    z-index: 999;
}

.sidebar.active {
    transform: translateX(0); /* visible when active */
}

body.rtl .sidebar {
    left: auto;
    right: 0;
    transform: translateX(100%);
}

body.rtl .sidebar.active {
    transform: translateX(0);
}

/* ===== Profile Section ===== */
.profile {
    text-align: center;
    padding-bottom: 20px;
}
.profile-img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin: 0 auto 8px;
    background-size: cover;
    background-position: center;
}
.profile h4 { 
    margin-bottom: 4px; 
    font-size: 1.2rem; 
}
.profile small { 
    color: #6B7280; 
    font-size: 0.9rem; 
}

/* ===== Sidebar Menu ===== */
.side-menu a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    color: #BDC3C7;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    transition: 0.3s;
    white-space: nowrap; /* prevent text wrap */
}
.side-menu a.active,
.side-menu a:hover {
    background: #2563EB;
    color: #fff;
}
.side-menu i { font-size: 1.2rem; }

/* ===== Responsive ===== */
@media (max-width: 1024px) {
    .sidebar {
        width: 200px;
        height: calc(100vh - 60px);
        transform: translateX(-100%);
        padding-top: 15px;
    }
    body.rtl .sidebar { transform: translateX(100%); }
    .sidebar.active { transform: translateX(0); }
    .side-menu a { padding: 10px 16px; font-size: 14px; }
    .profile-img { width: 80px; height: 80px; }
    .profile h4 { font-size: 1rem; }
}

@media (max-width: 768px) {
    .sidebar {
        width: 220px;
        top: 60px;
        transform: translateX(-100%);
        overflow-y: auto;
    }
    body.rtl .sidebar { transform: translateX(100%); }
    .sidebar.active { transform: translateX(0); }
    .side-menu a { padding: 10px 14px; font-size: 13px; }
    .profile-img { width: 70px; height: 70px; }
    .profile h4 { font-size: 0.95rem; }
}

@media (max-width: 480px) {
    .sidebar {
        width: 180px;
        transform: translateX(-100%);
    }
    body.rtl .sidebar { transform: translateX(100%); }
    .sidebar.active { transform: translateX(0); }
    .side-menu a { padding: 8px 12px; font-size: 12px; }
    .profile-img { width: 60px; height: 60px; }
    .profile h4 { font-size: 0.9rem; }
}
</style>

<!-- Sidebar JS -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const menuToggle = document.getElementById("menu-toggle");
    const sidebar = document.querySelector(".sidebar");

    menuToggle.addEventListener("change", function() {
        sidebar.classList.toggle("active");
    });
});
</script>