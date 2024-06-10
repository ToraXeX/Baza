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

// Запрос к базе данных (ваш запрос)
$query = "SELECT zak_date, zak_prep_date, zak_status, zak_due_date FROM zakaz";
$result = $db->query($query);

// Вывод данных
echo "<table class='table'>";
echo "<tr><th>Date</th><th>Preparation Date</th><th>Status</th><th>Due Date</th><th>Actions</th></tr>";

foreach ($result as $row) {
    echo "<tr><td>{$row['zak_date']}</td><td>{$row['zak_prep_date']}</td><td>{$row['zak_status']}</td><td>{$row['zak_due_date']}</td>";

    // Добавлена ссылка для редактирования заказа (если идентификатор нужен для редактирования, мы сохраняем его только в ссылке)
    echo "<td><a href='table_redactor/zakaz_red.php?zak_date={$row['zak_date']}&field=zak_id'>Edit</a></td></tr>";
}

echo "</table>";

// Форма для удаления заказа по ID
echo "<h2>Удаление заказа</h2>";
echo "<form method='post'>";
echo "<label for='zak_date'>Enter Order Date to delete:</label>";
echo "<input type='text' name='zak_date' required>";
echo "<button type='submit'>Удалить заказ</button>";
echo "</form>";

// Обработка удаления заказа
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Обработка формы после отправки
    $zak_date = $_POST['zak_date'];

    // Выполнение SQL-запроса для удаления заказа
    $query_delete = "DELETE FROM zakaz WHERE zak_date = :zak_date";
    $stmt_delete = $db->prepare($query_delete);
    $stmt_delete->bindParam(':zak_date', $zak_date, PDO::PARAM_STR);
    $stmt_delete->execute();
}
?>
