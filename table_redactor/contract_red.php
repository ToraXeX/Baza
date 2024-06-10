<!-- table_redactor/contract_red.php -->

<?php
// Подключение к базе данных (ваш код подключения)
$servername = "localhost";
$username = "postgres";
$password = "3237";
$database = "ZakaZ_for_clubs";

try {
    $db = new PDO("pgsql:host=$servername;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

// Обработка формы после отправки
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $con_id = $_POST['con_id'];
    $field = $_POST['field'];
    $new_value = $_POST['new_value'];

    // Выполнение SQL-запроса для обновления значения
    $query_update = "UPDATE contract SET $field = :new_value WHERE con_id = :con_id";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bindParam(':new_value', $new_value);
    $stmt_update->bindParam(':con_id', $con_id);
    $stmt_update->execute();
}

// Получение данных о контракте по ID
if (isset($_GET['id'])) {
    $con_id = $_GET['id'];
    $query_select = "SELECT * FROM contract WHERE con_id = :con_id";
    $stmt_select = $db->prepare($query_select);
    $stmt_select->bindParam(':con_id', $con_id);
    $stmt_select->execute();
    $contract = $stmt_select->fetch(PDO::FETCH_ASSOC);
} else {
    $con_id = '';
    $contract = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Редактирование контракта</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Редактирование контракта</h2>

        <form method="post" action="">
            <label for="con_id">Введите ID контракта:</label>
            <input type="text" name="con_id" value="<?php echo $con_id; ?>" required>
            <label for="field">Выберите поле для редактирования:</label>
            <select name="field">
                <option value="emp_id">Employee ID</option>
                <option value="con_date">Date</option>
                <option value="con_number">Number</option>
                <option value="cus_id">Customer ID</option>
            </select>
            <label for="new_value">Введите новое значение:</label>
            <input type="text" name="new_value" required>
            <button type="submit">Применить изменения</button>
        </form>

        <?php if (!empty($contract)): ?>
            <p>Текущие значения:</p>
            <ul>
                <?php foreach ($contract as $key => $value): ?>
                    <li><?php echo "$key: $value"; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
