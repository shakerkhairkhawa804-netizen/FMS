<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../auth_check.php'; //
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
require_once __DIR__ . '/../includes/mainbar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Include translations
$localization = include __DIR__ . '/../function/localization.php';
$translations = $localization['translations'];
$selected_lang = $_SESSION['custom_lang'] ?? $localization['lang'] ?? 'en';
$dir = $localization['dir'] ?? 'ltr';

// Translation helper
function t($key) {
    global $translations, $selected_lang;
    $keyWithLang = ($selected_lang === 'ps') ? $key . '_ps' : $key;
    return $translations[$keyWithLang] ?? $translations[$key] ?? $key;
}

// Fetch categories
$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Edit income
$edit_income = null;
if (isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM incomes WHERE id=? AND user_id=?");
    $stmt->execute([$_GET['edit_id'], $_SESSION['user_id']]);
    $edit_income = $stmt->fetch(PDO::FETCH_ASSOC);
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $income_date = $_POST['income_date'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($income_date && $category_id && $amount) {
        if ($edit_income) {
            $stmt = $pdo->prepare("UPDATE incomes SET income_date=?, category_id=?, amount=?, description=? WHERE id=? AND user_id=?");
            $stmt->execute([$income_date, $category_id, $amount, $description, $edit_income['id'], $_SESSION['user_id']]);
            $message = t('income_update_success');
        } else {
            $stmt = $pdo->prepare("INSERT INTO incomes (user_id, category_id, amount, description, income_date) VALUES (?,?,?,?,?)");
            $stmt->execute([$_SESSION['user_id'], $category_id, $amount, $description, $income_date]);
            $message = t('income_add_success');
        }

        // Refresh edit_income for edit form
        if ($edit_income) {
            $stmt = $pdo->prepare("SELECT * FROM incomes WHERE id=? AND user_id=?");
            $stmt->execute([$edit_income['id'], $_SESSION['user_id']]);
            $edit_income = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } else {
        $message = t('fill_all_fields');
    }
}

// Fetch all incomes for table
$stmt_incomes = $pdo->prepare("
    SELECT i.*, c.name AS category_name
    FROM incomes i
    JOIN categories c ON i.category_id = c.id
    WHERE i.user_id = ?
    ORDER BY i.income_date DESC
");
$stmt_incomes->execute([$_SESSION['user_id']]);
$incomes = $stmt_incomes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($selected_lang) ?>" dir="<?= htmlspecialchars($dir) ?>">
<head>
<meta charset="UTF-8">
<title><?= t('income_title') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

<style>
/* ===== Body & Fonts ===== */
body { 
    font-family: Arial, sans-serif; 
    background: #f4f7f8; 
    padding: 20px; 
    margin: 0; 
    box-sizing: border-box;
}

h2 { 
    margin-bottom: 15px; 
    text-align: center;
    color: #333;
}

/* ===== Form & List Containers ===== */
.form-container, .list-container { 
    background: #fff; 
    padding: 25px; 
    border-radius: 12px; 
    box-shadow: 0 8px 20px rgba(0,0,0,0.1); 
    max-width: 800px; 
    margin: 20px auto; 
    box-sizing: border-box;
    transition: transform 0.2s ease;
}

.form-container:hover, .list-container:hover {
    transform: translateY(-2px);
}

/* ===== Inputs, Selects, Textarea & Buttons ===== */
input, select, textarea, button {
    width: 100%;
    padding: 12px 15px;
    margin: 8px 0 15px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 15px;
    box-sizing: border-box;
}

textarea {
    resize: vertical; 
    min-height: 80px;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.5);
}

/* ===== Buttons ===== */
button {
    background: #007bff;
    color: #fff;
    border: none;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s ease;
}

button:hover {
    background: #0056b3;
    transform: translateY(-2px);
}

/* ===== Messages ===== */
.message {
    text-align: center;
    font-weight: bold;
    color: green;
    margin-bottom: 15px;
}

/* ===== Table ===== */
.table-container {
    width: 100%;
    overflow-x: auto;   /* horizontal scroll for mobile */
    margin-top: 15px;
    border-radius: 8px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.05);
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 600px; /* scroll for smaller screens */
    background: #fff;
}

th, td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #ddd;
}

th {
    background: #007bff;
    color: #fff;
    position: sticky;
    top: 0;
    z-index: 5;
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
    font-size: 13px;
    color: #fff;
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
    .form-container, .list-container { padding: 20px; }
    th, td { font-size: 14px; padding: 10px; }
    input, select, textarea, button { font-size: 14px; padding: 10px; }
    table { min-width: 500px; }
}

@media (max-width: 768px) {
    .form-container, .list-container { padding: 18px; }
    th, td { font-size: 13px; padding: 8px; }
    input, select, textarea, button { font-size: 13px; padding: 8px; }
    table { min-width: 400px; }
}

@media (max-width: 480px) {
    body { padding: 10px; }
    .form-container, .list-container { padding: 15px; width: 100%; }
    th, td { font-size: 12px; padding: 6px; }
    input, select, textarea, button { font-size: 13px; padding: 6px; }
    .actions a { padding: 4px 6px; font-size: 11px; margin: 2px; }
    h2 { font-size: 18px; }
}
</style>
</head>
<body>

<div class="form-container">
    <h2><?= $edit_income ? t('edit') : t('income_title') ?></h2>

    <?php if($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="post">
        <label><?= t('income_date') ?></label>
        <input type="date" name="income_date" value="<?= htmlspecialchars($edit_income['income_date'] ?? '') ?>" required>

        <label><?= t('income_category') ?></label>
        <select name="category_id" required>
            <option value=""><?= t('income_category') ?></option>
            <?php foreach($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= isset($edit_income['category_id']) && $edit_income['category_id']==$cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label><?= t('income_amount') ?> (AFN)</label>
        <input type="number" name="amount" step="0.01" value="<?= htmlspecialchars($edit_income['amount'] ?? '') ?>" required>

        <label><?= t('income_description') ?></label>
        <textarea name="description"><?= htmlspecialchars($edit_income['description'] ?? '') ?></textarea>

        <button type="submit"><?= $edit_income ? t('edit') : t('income_submit') ?></button>
        <?php if($edit_income): ?>
            <a href="income_add.php"><?= t('cancel_edit') ?></a>
        <?php endif; ?>
    </form>
</div>

<div class="list-container">
    <h2><?= t('income_list') ?></h2>
    <table>
        <tr>
            <th><?= t('income_date') ?></th>
            <th><?= t('income_category') ?></th>
            <th><?= t('income_amount') ?></th>
            <th><?= t('income_description') ?></th>
            <th><?= t('actions') ?></th>
        </tr>
        <?php if($incomes): ?>
            <?php foreach($incomes as $inc): ?>
            <tr>
                <td><?= htmlspecialchars($inc['income_date']) ?></td>
                <td><?= htmlspecialchars($inc['category_name']) ?></td>
                <td><?= number_format($inc['amount'],2) ?></td>
                <td><?= htmlspecialchars($inc['description']) ?></td>
                <td>
                    <a href="?edit_id=<?= $inc['id'] ?>"><?= t('edit') ?></a> |
                    <a href="income_delete.php?id=<?= $inc['id'] ?>" onclick="return confirm('<?= $selected_lang=='ps' ? 'ډاډه یی؟' : 'Are you sure?' ?>');"><?= t('delete') ?></a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5"><?= t('no_incomes') ?></td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>