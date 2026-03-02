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
    header("Location: ../auth/login.php"); exit;
}

$selected_lang = $_SESSION['custom_lang'] ?? $translations['lang'] ?? 'en';
$dir = in_array($selected_lang,['ps','fa','ar'])?'rtl':'ltr';

$clients_stmt = $pdo->prepare("SELECT id,name FROM clients WHERE user_id=? ORDER BY name ASC");
$clients_stmt->execute([$_SESSION['user_id']]);
$clients = $clients_stmt->fetchAll(PDO::FETCH_ASSOC);

$message = '';

// ADD Payment
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_payment'])){
    $client_id = $_POST['client_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $payment_date = $_POST['payment_date'] ?? '';
    $note = $_POST['note'] ?? '';
    if($client_id && $amount && $payment_date){
        $stmt = $pdo->prepare("INSERT INTO client_payments (client_id,amount,payment_date,note) VALUES (?,?,?,?)");
        $stmt->execute([$client_id,$amount,$payment_date,$note]);
        $message = $selected_lang=='ps'?'رسید ثبت شو!':'Payment added successfully!';
    } else {
        $message = $selected_lang=='ps'?'ټولې برخې ډکې کړئ!':'Please fill all required fields!';
    }
}

// DELETE
if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM client_payments WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: client_payments.php"); exit;
}

// FETCH Payments
$payments_stmt = $pdo->prepare("
SELECT cp.*, c.name AS client_name
FROM client_payments cp
JOIN clients c ON cp.client_id=c.id
WHERE c.user_id=? ORDER BY cp.payment_date DESC
");
$payments_stmt->execute([$_SESSION['user_id']]);
$payments = $payments_stmt->fetchAll(PDO::FETCH_ASSOC);

function t(string $key,array $translations,string $lang):string{
    $keyWithLang = $lang==='ps'?$key.'_ps':$key;
    return $translations[$keyWithLang]??$translations[$key]??$key;
}
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($selected_lang) ?>" dir="<?= $dir ?>">
<head>
<meta charset="UTF-8">
<title><?= t('receipt_title',$translations,$selected_lang) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../../style.css">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f7f8;
    padding: 20px;
    margin: 0;
}

.container {
    max-width: 90%; /* کمپیوټر لپاره مناسب عرض */
    margin: auto;
}

.box {
    background: #fff;
    padding: 25px 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    margin-bottom: 25px;
    overflow-x: auto; /* جدول د موبایل لپاره افقي سکروول */
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 1.5rem;
}

/* فارم تنظیمات: کمپیوټر لپاره 60٪ عرض */
form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: space-between;
    width: 60%;
    margin: auto;
}

form label {
    flex: 1 1 100%;
    margin-bottom: 5px;
    font-weight: bold;
}

form select, form input, form textarea, form button {
    flex: 1 1 calc(48% - 10px);
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 1rem;
}

form textarea {
    min-height: 70px;
}

form button {
    background: #007bff;
    color: #fff;
    border: none;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s;
}

form button:hover {
    background: #0056b3;
}

.message {
    text-align: center;
    color: green;
    font-weight: bold;
    margin-bottom: 10px;
}

/* جدول تنظیمات */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    min-width: 300px; /* موبایل لپاره حد */
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: center;
    font-size: 0.95rem;
}

th {
    background: #007bff;
    color: #fff;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

.actions a {
    padding: 5px 10px;
    border-radius: 5px;
    color: #fff;
    text-decoration: none;
}

.edit {
    background: #28a745;
}

.delete {
    background: #dc3545;
}

.edit:hover {
    background: #218838;
}

.delete:hover {
    background: #c82333;
}

/* موبایل لپاره Responsive تنظیمات */
@media (max-width: 768px) {
    form {
        width: 100%; /* فارم 100٪ عرض */
    }

    form select, form input, form textarea, form button {
        flex: 1 1 100%; /* هر input په بشپړ سکرین کې */
    }

    h2 {
        font-size: 1.2rem;
    }

    th, td {
        font-size: 0.85rem;
        padding: 8px;
    }
}
</style>
</head>
<body>
<div class="container">

<div class="box">
<h2><?= t('receipt_title',$translations,$selected_lang) ?></h2>
<?php if($message): ?><div class="message"><?= htmlspecialchars($message) ?></div><?php endif; ?>

<form method="post">
<input type="hidden" name="add_payment">
<label><?= t('receipt_client',$translations,$selected_lang) ?></label>
<select name="client_id" required>
<option value=""><?= t('receipt_client',$translations,$selected_lang) ?></option>
<?php foreach($clients as $c): ?>
<option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
<?php endforeach; ?>
</select>
<label><?= t('receipt_amount',$translations,$selected_lang) ?> (AFN)</label>
<input type="number" name="amount" step="0.01" required>
<label><?= t('receipt_date',$translations,$selected_lang) ?></label>
<input type="date" name="payment_date" required>
<label><?= t('receipt_note',$translations,$selected_lang) ?></label>
<textarea name="note"></textarea>
<button type="submit"><?= t('receipt_submit',$translations,$selected_lang) ?></button>
</form>
</div>

<div class="box">
<h2><?= t('receipt_list',$translations,$selected_lang) ?></h2>
<table>
<tr>
<th>#</th><th><?= t('table_client',$translations,$selected_lang) ?></th>
<th><?= t('table_amount',$translations,$selected_lang) ?></th>
<th><?= t('table_date',$translations,$selected_lang) ?></th>
<th><?= t('table_note',$translations,$selected_lang) ?></th>
<th><?= t('table_actions',$translations,$selected_lang) ?></th>
</tr>
<?php if($payments): foreach($payments as $i=>$p): ?>
<tr>
<td><?= $i+1 ?></td>
<td><?= htmlspecialchars($p['client_name']) ?></td>
<td><?= number_format($p['amount'],2) ?></td>
<td><?= htmlspecialchars($p['payment_date']) ?></td>
<td><?= htmlspecialchars($p['note']) ?></td>
<td class="actions">
<a href="client_payment_edit.php?id=<?= $p['id'] ?>" class="edit"><?= t('edit',$translations,$selected_lang) ?></a>
<a href="?delete=<?= $p['id'] ?>" class="delete" onclick="return confirm('<?= t('confirm_delete',$translations,$selected_lang) ?>');"><?= t('delete',$translations,$selected_lang) ?></a>
</td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="6"><?= $selected_lang=='ps'?'هیڅ رسید ونه موندل شو':'No payments found' ?></td></tr>
<?php endif; ?>
</table>
</div>

</div>
</body>
</html>
<?php ob_end_flush(); ?>