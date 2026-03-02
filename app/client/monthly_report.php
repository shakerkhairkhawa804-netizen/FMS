<?php
ob_start();
if(session_status()===PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../function/localization.php';
require_once __DIR__ . '/../auth_check.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/mainbar.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$selected_lang = $_SESSION['custom_lang'] ?? $translations['lang'] ?? 'en';
$dir = in_array($selected_lang,['ps','fa','ar'])?'rtl':'ltr';

// Fetch clients
$clients_stmt = $pdo->prepare("SELECT id,name FROM clients WHERE user_id=? ORDER BY name ASC");
$clients_stmt->execute([$_SESSION['user_id']]);
$clients = $clients_stmt->fetchAll(PDO::FETCH_ASSOC);

// Selected client and month (default: current month)
$client_id = $_GET['client_id'] ?? '';
$month = $_GET['month'] ?? date('Y-m');

$payments = [];
$total_payment = 0;

if($client_id){
    // Fetch all payments for this client in selected month
    $stmt_payment = $pdo->prepare("
        SELECT cp.*, c.name AS client_name
        FROM client_payments cp
        JOIN clients c ON cp.client_id=c.id
        WHERE cp.client_id=? 
          AND c.user_id=? 
          AND DATE_FORMAT(cp.payment_date,'%Y-%m')=?
        ORDER BY cp.payment_date ASC
    ");
    $stmt_payment->execute([$client_id,$_SESSION['user_id'],$month]);
    $payments = $stmt_payment->fetchAll(PDO::FETCH_ASSOC);

    // Total payments (0 if no payments)
    $stmt_payment_total = $pdo->prepare("
        SELECT COALESCE(SUM(cp.amount),0) as total_payment
        FROM client_payments cp
        JOIN clients c ON cp.client_id=c.id
        WHERE cp.client_id=? 
          AND c.user_id=? 
          AND DATE_FORMAT(cp.payment_date,'%Y-%m')=?
    ");
    $stmt_payment_total->execute([$client_id,$_SESSION['user_id'],$month]);
    $total_payment = (float) $stmt_payment_total->fetchColumn();
}

// Translation helper
function t(string $key,array $translations,string $lang):string{
    $keyWithLang = $lang==='ps'?$key.'_ps':$key;
    return $translations[$keyWithLang]??$translations[$key]??$key;
}
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($selected_lang) ?>" dir="<?= $dir ?>">
<head>
<meta charset="UTF-8">
<title><?= t('monthly_report_title',$translations,$selected_lang) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../../style.css">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<style>
body{font-family:Arial,sans-serif;background:#f4f7f8;padding:20px;margin:0;}
.container{max-width:90%; margin:auto;}
.box{background:#fff;padding:20px 25px;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,0.1);margin-bottom:25px;overflow-x:auto;}
h2{text-align:center;margin-bottom:20px;font-size:1.5rem;}

/* Form: کمپیوټر لپاره 60% او مرکز ته */
form{
    display:flex;
    flex-wrap:wrap;
    gap:15px;
    justify-content:space-between;
    width:60%;
    margin:auto;
}

form label{flex:1 1 100%; margin-bottom:5px; font-weight:bold;}
form select, form input, form button{
    flex:1 1 calc(48% - 10px);
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:1rem;
}

form button{
    background:#007bff;
    color:#fff;
    border:none;
    cursor:pointer;
    font-weight:bold;
    transition:background 0.3s;
}
form button:hover{background:#0056b3;}

table{width:100%;border-collapse:collapse;margin-top:15px;min-width:300px;}
th,td{padding:10px;border-bottom:1px solid #ddd;text-align:center;font-size:0.95rem;}
th{background:#007bff;color:#fff;}
tr:nth-child(even){background:#f9f9f9;}
.total-row td{font-weight:bold;background:#eee;}

/* Responsive adjustments */
@media (max-width:768px){
    form{width:100%;} /* موبایل لپاره 100% */
    form select, form input, form button{flex:1 1 100%;}
    h2{font-size:1.2rem;}
    th, td{font-size:0.85rem; padding:8px;}
}
</style>
</head>
<body>

<div class="container">

<!-- Form -->
<div class="box">
<h2><?= t('monthly_report_title',$translations,$selected_lang) ?></h2>
<form method="get">
<label><?= t('select_client',$translations,$selected_lang) ?></label>
<select name="client_id" required>
<option value=""><?= t('client_placeholder',$translations,$selected_lang) ?></option>
<?php foreach($clients as $c): ?>
<option value="<?= $c['id'] ?>" <?= ($client_id==$c['id'])?'selected':'' ?>>
<?= htmlspecialchars($c['name']) ?></option>
<?php endforeach; ?>
</select>

<label><?= t('select_month',$translations,$selected_lang) ?></label>
<input type="month" name="month" value="<?= htmlspecialchars($month) ?>" required>

<button type="submit"><?= t('show_report',$translations,$selected_lang) ?></button>
</form>
</div>

<!-- Payments Table -->
<?php if($payments): ?>
<div class="box">
<h2><?= htmlspecialchars($payments[0]['client_name']) ?> (<?= htmlspecialchars($month) ?>)</h2>

<table>
<tr>
<th><?= t('table_date',$translations,$selected_lang) ?></th>
<th><?= t('table_description',$translations,$selected_lang) ?></th>
<th><?= t('table_amount',$translations,$selected_lang) ?></th>
</tr>

<?php foreach($payments as $pay): ?>
<tr>
<td>
<?php 
$dt = new DateTime($pay['payment_date']); 
echo $dt->format('d-m-Y'); // اتومات تاریخ
?>
</td>
<td><?= htmlspecialchars($pay['note'] ?? '-') ?></td>
<td><?= number_format((float)$pay['amount'],2) ?></td>
</tr>
<?php endforeach; ?>

<tr class="total-row">
<td colspan="2"><?= t('table_total',$translations,$selected_lang) ?></td>
<td><?= number_format($total_payment,2) ?></td>
</tr>
</table>
</div>

<?php elseif($client_id): ?>
<div class="box">
<p style="text-align:center;font-weight:bold;"><?= t('no_payments_found',$translations,$selected_lang) ?></p>
</div>
<?php endif; ?>

</div>
</body>
</html>
<?php ob_end_flush(); ?>