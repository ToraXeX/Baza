<!-- table_redactor/employee_red.php -->

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
    $emp_id = $_POST['emp_id'];
    $field = $_POST['field'];
    $new_value = $_POST['new_value'];

    // Выполнение SQL-запроса для обновления значения
    $query_update = "UPDATE employee SET $field = :new_value WHERE emp_id = :emp_id";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bindParam(':new_value', $new_value);
    $stmt_update->bindParam(':emp_id', $emp_id);
    $stmt_update->execute();
}

// Получение данных о сотруднике по ID
if (isset($_GET['id'])) {
    $emp_id = $_GET['id'];
    $query_select = "SELECT * FROM employee WHERE emp_id = :emp_id";
    $stmt_select = $db->prepare($query_select);
    $stmt_select->bindParam(':emp_id', $emp_id);
    $stmt_select->execute();
    $employee = $stmt_select->fetch(PDO::FETCH_ASSOC);
} else {
    $emp_id = '';
    $employee = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Редактирование сотрудника</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Редактирование сотрудника</h2>

        <form method="post" action="">
            <label for="emp_id">Введите ID сотрудника:</label>
            <input type="text" name="emp_id" value="<?php echo $emp_id; ?>" required>
            <label for="field">Выберите поле для редактирования:</label>
            <select name="field">
                <option value="emp_name">Name</option>
                <option value="emp_position">Position</option>
            </select>
            <label for="new_value">Введите новое значение:</label>
            <input type="text" name="new_value" required>
            <button type="submit">Применить изменения</button>
        </form>

        <?php if (!empty($employee)): ?>
            <p>Текущие значения:</p>
            <ul>
                <?php foreach ($employee as $key => $value): ?>
                    <li><?php echo "$key: $value"; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
