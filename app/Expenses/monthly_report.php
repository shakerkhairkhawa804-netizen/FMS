<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// دلته خپل localization یا $_SESSION['user_id'] وغیره وکاروئ

require_once __DIR__ . '/../auth_check.php'; //
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/mainbar.php';
$localization = include __DIR__ . '/../function/localization.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Month/Year selector
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m');  
$year  = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Fetch monthly expenses grouped by category safely
$stmt = $pdo->prepare("
    SELECT c.name AS category, SUM(e.amount) AS total_amount
    FROM expenses e
    JOIN categories c ON e.category_id = c.id
    WHERE e.user_id = ? AND MONTH(e.expense_date) = ? AND YEAR(e.expense_date) = ?
    GROUP BY e.category_id
");
$stmt->execute([$_SESSION['user_id'], $month, $year]);
$monthly_expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total for the month
$stmt_total = $pdo->prepare("
    SELECT SUM(amount) AS total
    FROM expenses
    WHERE user_id = ? AND MONTH(expense_date) = ? AND YEAR(expense_date) = ?
");
$stmt_total->execute([$_SESSION['user_id'], $month, $year]);
$total = $stmt_total->fetchColumn();
$total = $total !== null ? (float)$total : 0;
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monthly Expense Report</title>
<link rel="stylesheet" href="../../style.css">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

<style>
body { font-family: Arial, sans-serif; background:#f9f9f9; margin:0; padding:20px; }
.container { max-width:65%; margin:auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
h2 { text-align:center; color:#333; margin-bottom:20px; }
form { text-align:center; margin-bottom:20px; }
input[type=number] { padding:8px 12px; margin:0 5px; border-radius:6px; border:1px solid #ccc; width:100px; }
button { padding:8px 15px; border:none; background:#007bff; color:#fff; border-radius:6px; cursor:pointer; }
button:hover { background:#0056b3; }

table { width:100%; border-collapse: collapse; margin-top:20px; }
th, td { padding:12px; text-align:center; border-bottom:1px solid #ddd; }
th { background:#007bff; color:#fff; border-radius:6px; }
tr:nth-child(even) { background:#f2f2f2; }
.total-row { font-weight:bold; background:#e8f0fe; }

@media(max-width:600px){
    table, th, td { font-size:14px; }
    input[type=number], button { margin:5px 0; display:block; width:90%; }
}

/* Show button with icon */
.btn-show {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 18px;
    background: #007bff;
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
}

.btn-show i { margin-right: 8px; font-size: 18px; }
.btn-show:hover { background: #0056b3; transform: translateY(-2px); }
</style>
</head>
<body>

<div class="container">
    <h2><?= t('monthly_report_title', $translations, $selected_lang) ?></h2>

    <!-- Month/Year selector -->
    <form method="get">
        <input type="number" name="year" value="<?= htmlspecialchars($year) ?>" min="2020" max="<?= date('Y') ?>" placeholder="<?= t('monthly_report_year', $translations, $selected_lang) ?>" required>
        <input type="number" name="month" value="<?= htmlspecialchars($month) ?>" min="1" max="12" placeholder="<?= t('monthly_report_month', $translations, $selected_lang) ?>" required>
        <button type="submit" class="btn-show"><?= t('monthly_report_show', $translations, $selected_lang) ?></button>
    </form>

    <!-- Expense Table -->
    <?php if(!empty($monthly_expenses)): ?>
    <table>
        <tr>
            <th><?= t('monthly_report_category', $translations, $selected_lang) ?></th>
            <th><?= t('monthly_report_total', $translations, $selected_lang) ?> (AFN)</th>
        </tr>
        <?php foreach($monthly_expenses as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= number_format($row['total_amount'] ?? 0, 2) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total-row">
            <td><?= t('monthly_report_total', $translations, $selected_lang) ?></td>
            <td><?= number_format($total, 2) ?></td>
        </tr>
    </table>
    <?php else: ?>
        <p style="text-align:center; font-weight:bold;"><?= t('monthly_report_no_data', $translations, $selected_lang) ?></p>
    <?php endif; ?>
</div>

<script src="/home/assets/js/main.js"></script>


</body>
</html>
