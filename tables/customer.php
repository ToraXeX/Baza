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

// Обработка удаления клиента
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_customer'])) {
    $cus_name = $_POST['cus_name'];
    // Выполнение SQL-запроса для удаления клиента
    $query_delete = "DELETE FROM customer WHERE cus_name = :cus_name";
    $stmt_delete = $db->prepare($query_delete);
    $stmt_delete->bindParam(':cus_name', $cus_name, PDO::PARAM_STR);
    $stmt_delete->execute();
}

// Запрос к базе данных (ваш запрос)
$query = "SELECT cus_name, cus_phone FROM customer";
$result = $db->query($query);

// Вывод данных
echo "<table class='table'>";
echo "<tr><th>Name</th><th>Phone</th><th>Actions</th></tr>";

foreach ($result as $row) {
    echo "<tr><td>{$row['cus_name']}</td><td>{$row['cus_phone']}</td>";

    // Добавлена ссылка для редактирования клиента
    echo "<td><a href='table_redactor/customer_red.php?cus_name={$row['cus_name']}'>Edit</a></td></tr>";
}

echo "</table>";

// Форма для добавления клиента
echo "<h2>Добавление клиента</h2>";
echo "<form method='post' action='table_add/customer_add.php'>";
echo "<label for='cus_name'>Name:</label>";
echo "<input type='text' name='cus_name' required>";

echo "<label for='cus_phone'>Phone:</label>";
echo "<input type='text' name='cus_phone' required>";

echo "<button type='submit'>Добавить клиента</button>";
echo "</form>";

// Форма для удаления клиента по имени
echo "<h2>Удаление клиента</h2>";
echo "<form method='post'>";
echo "<label for='cus_name'>Enter Customer Name to delete:</label>";
echo "<input type='text' name='cus_name' required>";
echo "<button type='submit' name='delete_customer'>Удалить клиента</button>";
echo "</form>";
?>
