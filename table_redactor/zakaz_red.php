<!-- table_redactor/zakaz_red.php -->

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
    $zak_id = $_POST['zak_id'];
    $field = $_POST['field'];
    $new_value = $_POST['new_value'];

    // Выполнение SQL-запроса для обновления значения
    $query_update = "UPDATE zakaz SET $field = :new_value WHERE zak_id = :zak_id";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bindParam(':new_value', $new_value);
    $stmt_update->bindParam(':zak_id', $zak_id);
    $stmt_update->execute();
}

// Получение данных о заказе по ID
if (isset($_GET['id'])) {
    $zak_id = $_GET['id'];
    $query_select = "SELECT * FROM zakaz WHERE zak_id = :zak_id";
    $stmt_select = $db->prepare($query_select);
    $stmt_select->bindParam(':zak_id', $zak_id);
    $stmt_select->execute();
    $zakaz = $stmt_select->fetch(PDO::FETCH_ASSOC);
} else {
    $zak_id = '';
    $zakaz = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Редактирование заказа</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Редактирование заказа</h2>

        <form method="post" action="">
            <label for="zak_id">Введите ID заказа:</label>
            <input type="text" name="zak_id" value="<?php echo $zak_id; ?>" required>
            <label for="field">Выберите поле для редактирования:</label>
            <select name="field">
                <option value="con_id">Contract ID</option>
                <option value="zak_date">Date</option>
                <option value="zak_prep_date">Preparation Date</option>
                <option value="zak_status">Status</option>
                <option value="zak_due_date">Due Date</option>
            </select>
            <label for="new_value">Введите новое значение:</label>
            <input type="text" name="new_value" required>
            <button type="submit">Применить изменения</button>
        </form>

        <?php if (!empty($zakaz)): ?>
            <p>Текущие значения:</p>
            <ul>
                <?php foreach ($zakaz as $key => $value): ?>
                    <li><?php echo "$key: $value"; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
