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

// Обработка удаления доставки
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_delivery'])) {
    $del_date = $_POST['del_date'];
    // Выполнение SQL-запроса для удаления доставки
    $query_delete = "DELETE FROM delivery WHERE del_date = :del_date";
    $stmt_delete = $db->prepare($query_delete);
    $stmt_delete->bindParam(':del_date', $del_date, PDO::PARAM_STR);
    $stmt_delete->execute();
}

// Запрос к базе данных (ваш запрос)
$query = "SELECT del_cost, del_date, del_status FROM delivery";
$result = $db->query($query);

// Вывод данных
echo "<table class='table'>";
echo "<tr><th>Cost</th><th>Date</th><th>Status</th><th>Actions</th></tr>";

foreach ($result as $row) {
    echo "<tr><td>{$row['del_cost']}</td><td>{$row['del_date']}</td><td>{$row['del_status']}</td>";
    
    // Добавлена ссылка для редактирования доставки
    echo "<td><a href='table_redactor/delivery_red.php?del_date={$row['del_date']}'>Edit</a></td></tr>";
}

echo "</table>";

// Форма для добавления доставки
echo "<h2>Добавление доставки</h2>";
echo "<form method='post' action='table_add/delivery_add.php'>"; // Обновленный путь
echo "<label for='del_cost'>Cost:</label>";
echo "<input type='text' name='del_cost' required>";

echo "<label for='del_date'>Date:</label>";
echo "<input type='date' name='del_date' required>";

echo "<label for='del_status'>Status:</label>";
echo "<input type='text' name='del_status' required>";

echo "<button type='submit'>Добавить доставку</button>";
echo "</form>";

// Форма для удаления доставки по дате
echo "<h2>Удаление доставки</h2>";
echo "<form method='post'>";
echo "<label for='del_date'>Enter Delivery Date to delete:</label>";
echo "<input type='text' name='del_date' required>";
echo "<button type='submit' name='delete_delivery'>Удалить доставку</button>";
echo "</form>";
?>
