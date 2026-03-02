<?php
// ======================
// ✅ 1. Start session
// ======================
if (session_status() === PHP_SESSION_NONE) {
  
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
}

$loc = include_once __DIR__ . '/function/localization.php';
$translations = $loc['translations'];
$dir = $loc['dir'];
$selected_lang = $_SESSION['lang'] ?? $_COOKIE['custom_lang'] ?? 'en';

// ======================
// ✅ 2. DB config & includes
// ======================
require_once __DIR__ . '/auth_check.php'; //
require_once __DIR__ . '/config/config.php'; // ستاسو PDO $pdo
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
require_once __DIR__ . '/includes/mainbar.php';
// ======================


// ======================
// ✅ 3. Redirect if not logged in
// ======================
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: ../auth/login.php");
    exit();
}

// ======================
// ✅ 4. Selected language
// ======================
$selected_lang = $_SESSION['lang'] ?? $_COOKIE['custom_lang'] ?? 'en';
$loc = include __DIR__ . '/function/localization.php';
$translations = $loc['translations'];
$dir = $loc['dir'];

// ======================
// ✅ 5. Month & Year (define before use)
// ======================
$month = $_GET['month'] ?? date('m');
$year  = $_GET['year'] ?? date('Y');

// ======================
// ✅ 6. Fetch total expenses
// ======================
$stmt_total = $pdo->prepare("
    SELECT SUM(amount) AS total 
    FROM expenses 
    WHERE user_id=? AND MONTH(expense_date)=? AND YEAR(expense_date)=?
");
$stmt_total->execute([$user_id, $month, $year]);
$total_expenses = (float)($stmt_total->fetchColumn() ?? 0);

// ======================
// ✅ 7. Fetch total income
// ======================
$stmt_income = $pdo->prepare("
    SELECT SUM(amount) AS total 
    FROM incomes 
    WHERE user_id=? AND MONTH(income_date)=? AND YEAR(income_date)=?
");
$stmt_income->execute([$user_id, $month, $year]);
$total_income = (float)($stmt_income->fetchColumn() ?? 0);

// ======================
// ✅ 8. Fetch monthly data for last 12 months (chart)
// ======================
$months = [];
$expenses_data = [];
$income_data = [];
for ($i = 11; $i >= 0; $i--) {
    $m = date('m', strtotime("-$i months"));
    $y = date('Y', strtotime("-$i months"));
    $months[] = date('M Y', strtotime("$y-$m-01"));

    // Expenses
    $stmt = $pdo->prepare("SELECT SUM(amount) AS total FROM expenses WHERE user_id=? AND MONTH(expense_date)=? AND YEAR(expense_date)=?");
    $stmt->execute([$user_id, $m, $y]);
    $expenses_data[] = (float)($stmt->fetchColumn() ?? 0);

    // Income
    $stmt = $pdo->prepare("SELECT SUM(amount) AS total FROM incomes WHERE user_id=? AND MONTH(income_date)=? AND YEAR(income_date)=?");
    $stmt->execute([$user_id, $m, $y]);
    $income_data[] = (float)($stmt->fetchColumn() ?? 0);
}

// ======================
// ✅ 9. Translation helper
// ======================


?>
<!DOCTYPE html>
<html lang="<?= $selected_lang ?>" dir="<?= $dir ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('dashboard_title', $translations, $selected_lang) ?></title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../../style.css">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<style>
body {
    font-family: 'Bahij Greta Arabic', Arial, sans-serif;
    background: #f9f9f9;
    margin: 0;
    padding: 20px;
    direction: <?= $dir ?>;
    color: #2C3E50;
}

.container {
    max-width: 90%;
    margin: auto;
}

h1 {
    text-align: center;
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 20px;
    color: #34495E;
}

.summary {
    text-align: center;
    font-size: 1.5rem;
    font-weight: 700;
    margin: 10px 0;
    color: #2C3E50;
}

.summary span {
    display: inline-block;
    min-width: 140px;
}

.card {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    text-align: center;
}

.card h3 {
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 15px;
    color: #4A90E2;
}

/* ========================
   Chart Styling
======================== */
#financeChart {
    max-width: 100%;       /* ګراف د container سره برابر شي */
    height: 300px;         /* مناسب height */
    margin: 0 auto;        /* center alignment */
    display: block;
}

/* RTL/LTR adjustments */
body.ltr #financeChart {
    margin-right: auto;
    margin-left: auto;
}

/* Responsive */
@media (max-width: 768px) {
    #financeChart {
        height: 250px; /* موبایل لپاره لږ کوچنی */
    }
}
</style>
</head>
<body>

<!-- H1 عنوان -->
<h1><?= t('welcome', $translations, $selected_lang) ?></h1>

<!-- Summary -->
<p class="summary"><?= t('total_expenses', $translations, $selected_lang) ?>: <span><?= number_format($total_expenses, 2) ?> AFN</span></p>
<p class="summary"><?= t('income_title', $translations, $selected_lang) ?>: <span><?= number_format($total_income, 2) ?> AFN</span></p>

<div class="container">
    <div class="card">
        <h3><?= t('expenses_income_chart', $translations, $selected_lang) ?></h3>
        <canvas id="financeChart"></canvas>
    </div>
</div>

<script>
const ctx = document.getElementById('financeChart').getContext('2d');
const financeChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [
            {
                label: '<?= t("total_expenses", $translations, $selected_lang) ?>',
                data: <?= json_encode($expenses_data) ?>,
                backgroundColor: 'rgba(220, 53, 69, 0.7)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1
            },
            {
                label: '<?= t("income_title", $translations, $selected_lang) ?>',
                data: <?= json_encode($income_data) ?>,
                backgroundColor: 'rgba(40, 167, 69, 0.7)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: {
                display: true,
                text: '<?= t("expenses_income_chart", $translations, $selected_lang) ?>'
            }
        },
        scales: { y: { beginAtZero: true } }
    }
});

</script>
</body>
</html>
