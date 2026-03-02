<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// دلته خپل localization یا $_SESSION['user_id'] وغیره وکاروئ

require_once __DIR__ . '/../config/config.php';
ob_start(); // optional for redirect safety

require_once __DIR__ . '/../auth_check.php'; //
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/mainbar.php';
$localization = include __DIR__ . '/../function/localization.php';






$message = '';

// Handle POST (Add or Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $category_id  = $_POST['category_id'] ?? '';
    $amount       = $_POST['amount'] ?? '';
    $description  = trim($_POST['description'] ?? '');
    $expense_date = $_POST['expense_date'] ?? '';

    if ($category_id && $amount && $expense_date) {
        if ($id) {
            $stmt = $pdo->prepare("
                UPDATE expenses
                SET category_id=?, amount=?, description=?, expense_date=?
                WHERE id=? AND user_id=?
            ");
            $stmt->execute([$category_id, $amount, $description, $expense_date, $id, $_SESSION['user_id']]);
            $message = "مصرف په بریالیتوب سره تازه شو ✅";
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO expenses (user_id, category_id, amount, description, expense_date)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$_SESSION['user_id'], $category_id, $amount, $description, $expense_date]);
            $message = "مصرف په بریالیتوب ثبت شو ✅";
        }
    } else {
        $message = "مهرباني وکړئ ټول ضروري فیلډونه ډک کړئ ❌";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $del_id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM expenses WHERE id=? AND user_id=?");
    $stmt->execute([$del_id, $_SESSION['user_id']]);
    $message = "مصرف په بریالیتوب سره حذف شو ✅";
}

// categories for select
$categories = $pdo->query("SELECT id, name FROM categories")->fetchAll();

// fetch all expenses
$stmt = $pdo->prepare("
    SELECT e.id, e.amount, e.description, e.expense_date, c.name AS category_name
    FROM expenses e
    JOIN categories c ON e.category_id = c.id
    WHERE e.user_id=?
    ORDER BY e.expense_date DESC
");
$stmt->execute([$_SESSION['user_id']]);
$expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If editing, fetch single expense
$edit_expense = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE id=? AND user_id=?");
    $stmt->execute([$edit_id, $_SESSION['user_id']]);
    $edit_expense = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
<meta charset="UTF-8">
<title>Expenses Management</title>
<link rel="stylesheet" href="../../style.css">  
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
<style>
/* ===== Body & Fonts ===== */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f2f5;
    padding: 20px;
    margin: 0;
    box-sizing: border-box;
}

/* ===== Headings ===== */
h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

/* ===== Card Form ===== */
.card {
    background: #fff;
    width: 100%;
    max-width: 500px;
    margin: auto;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}

/* ===== Form Inputs ===== */
input, select, textarea {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
    box-sizing: border-box;
    background: #fff;
    color: #1f2937;
}

select {
    background: #f9fafb;
    appearance: none;
}

textarea {
    min-height: 80px;
    resize: vertical;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.5);
}

/* ===== Buttons ===== */
button {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 6px;
    border: none;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    background: #007bff;
    color: #fff;
    transition: all 0.2s ease;
}

button:hover {
    background: #0056b3;
    transform: translateY(-2px);
}

/* ===== Messages ===== */
.msg, .alert {
    text-align: center;
    color: green;
    margin-bottom: 15px;
    font-weight: bold;
}

/* ===== Table Container ===== */
.table-container {
    width: 70%;
    max-width: 100%;
    max-height: 400px;
    overflow-x: auto;
    overflow-y: auto;
    margin: 30px auto;
    border-radius: 8px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    background: #fff;
}

table {
    width: 100%;
    min-width: 600px;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

th {
    background: #007bff;
    color: #fff;
    position: sticky;
    top: 0;
    z-index: 10;
}

tr:nth-child(even) {
    background: #f9f9f9;
}

/* ===== Table Actions ===== */
.actions {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 5px;
}

.actions a {
    text-decoration: none;
    font-weight: bold;
    padding: 6px 12px;
    border-radius: 4px;
    color: #fff;
    font-size: 13px;
    transition: background 0.2s ease;
    text-align: center;
}

.actions a.edit {
    background-color: #28a745;
}

.actions a.edit:hover {
    background-color: #218838;
}

.actions a.delete {
    background-color: #dc3545;
}

.actions a.delete:hover {
    background-color: #c82333;
}

/* ===== Responsive Adjustments ===== */
@media (max-width: 1024px) {
    .table-container {
        width: 90%;
    }
    table {
        min-width: 500px;
    }
    th, td {
        font-size: 14px;
        padding: 10px;
    }
    .card {
        width: 90%;
        padding: 20px;
    }
    input, select, textarea, button {
        font-size: 14px;
        padding: 10px;
    }
}

@media (max-width: 768px) {
    .table-container {
        width: 95%;
    }
    table {
        min-width: 400px;
    }
    th, td {
        font-size: 13px;
        padding: 8px;
    }
    .card {
        width: 95%;
        padding: 18px;
    }
    input, select, textarea, button {
        font-size: 13px;
        padding: 8px;
    }
}

@media (max-width: 480px) {
    body {
        padding: 10px;
    }
    .card {
        width: 100%;
        padding: 15px;
    }
    th, td {
        font-size: 12px;
        padding: 6px;
    }
    input, select, textarea, button {
        font-size: 13px;
        padding: 6px;
    }
    .actions a {
        padding: 4px 6px;
        font-size: 11px;
        margin: 2px;
    }
    h2 {
        font-size: 18px;
    }
}
</style>
</head>
<body>

<h2><?= $edit_expense ? t('expenses_title', $translations, $selected_lang) : t('expenses_title', $translations, $selected_lang) ?></h2>

<?php if ($message): ?>
<p class="msg"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<div class="card">
<form method="post">
    <input type="hidden" name="id" value="<?= $edit_expense['id'] ?? '' ?>">

    <select name="category_id" required>
        <option value=""><?= t('expense_category', $translations, $selected_lang) ?></option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($edit_expense && $edit_expense['category_id']==$cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="number" name="amount" step="0.01" placeholder="<?= t('expense_amount', $translations, $selected_lang) ?>" value="<?= $edit_expense['amount'] ?? '' ?>" required>
    <input type="date" name="expense_date" placeholder="<?= t('expense_date', $translations, $selected_lang) ?>" value="<?= $edit_expense['expense_date'] ?? '' ?>" required>
    <input type="text" name="description" placeholder="<?= t('expense_description', $translations, $selected_lang) ?>" value="<?= $edit_expense['description'] ?? '' ?>">
    <button type="submit"><?= $edit_expense ? t('edit', $translations, $selected_lang) : t('expense_submit', $translations, $selected_lang) ?></button>
</form>
</div>

<h2 style="margin-top:50px;"><?= t('expense_list', $translations, $selected_lang) ?></h2>

<div class="table-container">
<table>
    <tr>
        <th>#</th>
        <th><?= t('expense_category', $translations, $selected_lang) ?></th>
        <th><?= t('expense_amount', $translations, $selected_lang) ?></th>
        <th><?= t('expense_description', $translations, $selected_lang) ?></th>
        <th><?= t('expense_date', $translations, $selected_lang) ?></th>
        <th><?= t('expense_actions', $translations, $selected_lang) ?></th>
    </tr>

<?php if ($expenses): ?>
    <?php foreach ($expenses as $i => $ex): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($ex['category_name']) ?></td>
            <td><?= number_format($ex['amount'], 2) ?></td>
            <td><?= htmlspecialchars($ex['description']) ?></td>
            <td><?= $ex['expense_date'] ?></td>
            <td class="actions">
                <a href="?edit=<?= $ex['id'] ?>" class="edit"><?= t('edit', $translations, $selected_lang) ?></a>
                <a href="?delete=<?= $ex['id'] ?>" class="delete" onclick="return confirm('<?= $selected_lang=='ps' ? 'ډاډه یی؟' : 'Are you sure?' ?>')"><?= t('delete', $translations, $selected_lang) ?></a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="6"><?= $selected_lang=='ps' ? 'مصارف ونه موندل شول' : 'No expenses found' ?></td>
    </tr>
<?php endif; ?>
</table>
</div>

<script src="/home/assets/js/main.js"></script>


</body>
</html>
