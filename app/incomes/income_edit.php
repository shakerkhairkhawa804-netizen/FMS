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

if (!isset($_GET['id'])) {
    header("Location: income_add.php");
    exit();
}

$income_id = $_GET['id'];

// Fetch income for edit
$stmt = $pdo->prepare("SELECT * FROM incomes WHERE id = ? AND user_id = ?");
$stmt->execute([$income_id, $_SESSION['user_id']]);
$income = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$income) {
    header("Location: income_add.php");
    exit();
}

// Fetch categories
$stmt_cat = $pdo->prepare("SELECT * FROM categories WHERE type='income'");
$stmt_cat->execute();
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $income_date = $_POST['income_date'] ?? '';
    $category_id = $_POST['category_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($income_date && $category_id && $amount) {
        $stmt = $pdo->prepare("UPDATE incomes SET income_date=?, category_id=?, amount=?, description=? WHERE id=? AND user_id=?");
        $stmt->execute([$income_date, $category_id, $amount, $description, $income_id, $_SESSION['user_id']]);
        $message = "Income updated successfully!";

        // Refresh income data
        $stmt = $pdo->prepare("SELECT * FROM incomes WHERE id = ? AND user_id = ?");
        $stmt->execute([$income_id, $_SESSION['user_id']]);
        $income = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $message = "Please fill all required fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $dir ?>">
<head>
<meta charset="UTF-8">
<title>Edit Income</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ===== Body & Container ===== */
body {
    font-family: 'Arial', sans-serif;
    background: linear-gradient(135deg, #c3ecf0, #fefbd8);
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
.form-container {
    background: #ffffff;
    padding: 35px 30px;
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    width: 100%;
    max-width: 520px;
    transition: transform 0.3s;
}
.form-container:hover {
    transform: translateY(-5px);
}

/* ===== Heading ===== */
h2 {
    margin-bottom: 30px;
    font-weight: 700;
    text-align: center;
    color: #007bff;
}
h2 i { margin-right: 10px; }

/* ===== Input Fields ===== */
label {
    display: flex;
    align-items: center;
    font-weight: 500;
    margin-bottom: 6px;
    color: #555;
}
label i { margin-right: 8px; color: #007bff; }
input, select, textarea {
    width: 100%;
    padding: 14px 16px;
    margin-bottom: 18px;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-size: 15px;
    box-sizing: border-box;
    transition: 0.3s;
}
input:focus, select:focus, textarea:focus {
    border-color: #007bff;
    outline: none;
}

/* ===== Button ===== */
button {
    background: linear-gradient(90deg, #007bff, #00c6ff);
    color: #fff;
    border: none;
    cursor: pointer;
    font-weight: bold;
    padding: 14px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.4s;
    font-size: 16px;
}
button i { margin-right: 10px; font-size: 16px; }
button:hover { background: linear-gradient(90deg, #0056b3, #0096c7); transform: scale(1.05); }

/* ===== Message ===== */
.message {
    text-align: center;
    font-weight: bold;
    color: green;
    margin-bottom: 20px;
}

/* ===== Responsive ===== */
@media (max-width: 480px) {
    .form-container {
        padding: 25px;
    }
    h2 { font-size: 1.5rem; }
    input, select, textarea, button { font-size: 14px; }
}
</style>
</head>
<body>
<div class="form-container">
    <h2><i class="fa-solid fa-wallet"></i> Edit Income</h2>
    <?php if($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <form method="post">
        <label><i class="fa-solid fa-calendar-days"></i> Date</label>
        <input type="date" name="income_date" value="<?= htmlspecialchars($income['income_date'] ?? '') ?>" required>

        <label><i class="fa-solid fa-list"></i> Category</label>
        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php foreach($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($cat['id']==$income['category_id'] ? 'selected':'') ?>>
                <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>

        <label><i class="fa-solid fa-coins"></i> Amount (AFN)</label>
        <input type="number" name="amount" step="0.01" value="<?= htmlspecialchars($income['amount'] ?? '') ?>" required>

        <label><i class="fa-solid fa-pen"></i> Description</label>
        <textarea name="description"><?= htmlspecialchars($income['description'] ?? '') ?></textarea>

        <button type="submit"><i class="fa-solid fa-floppy-disk"></i> Update Income</button>
    </form>
</div>
</body>
</html>