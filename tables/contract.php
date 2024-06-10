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

// Обработка удаления контракта
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_contract'])) {
    $con_number = $_POST['con_number'];
    // Выполнение SQL-запроса для удаления контракта
    $query_delete = "DELETE FROM contract WHERE con_number = :con_number";
    $stmt_delete = $db->prepare($query_delete);
    $stmt_delete->bindParam(':con_number', $con_number, PDO::PARAM_STR);
    $stmt_delete->execute();
}

// Запрос к базе данных для получения контрактов с именами сотрудников и клиентов
$query = "
    SELECT 
        c.con_number, 
        c.con_date, 
        e.emp_name, 
        cus.cus_name 
    FROM 
        contract c
    JOIN 
        employee e ON c.emp_id = e.emp_id
    JOIN 
        customer cus ON c.cus_id = cus.cus_id
";
$result = $db->query($query);

// Вывод данных
echo "<table class='table'>";
echo "<tr><th>Employee Name</th><th>Date</th><th>Number</th><th>Customer Name</th><th>Actions</th></tr>";

foreach ($result as $row) {
    echo "<tr><td>{$row['emp_name']}</td><td>{$row['con_date']}</td><td>{$row['con_number']}</td><td>{$row['cus_name']}</td>";
    
    // Добавлена ссылка для редактирования контракта
    echo "<td><a href='table_redactor/contract_red.php?con_number={$row['con_number']}'>Edit</a></td></tr>";
}

echo "</table>";

// Форма для добавления контракта
echo "<h2>Добавление контракта</h2>";
echo "<form method='post' action='table_add/contract_add.php'>";
echo "<label for='emp_id'>Employee ID:</label>";
echo "<input type='text' name='emp_id' required>";

echo "<label for='con_date'>Date:</label>";
echo "<input type='date' name='con_date' required>";

echo "<label for='con_number'>Number:</label>";
echo "<input type='text' name='con_number' required>";

echo "<label for='cus_id'>Customer ID:</label>";
echo "<input type='text' name='cus_id' required>";

echo "<button type='submit'>Добавить контракт</button>";
echo "</form>";

// Форма для удаления контракта по номеру
echo "<h2>Удаление контракта</h2>";
echo "<form method='post'>";
echo "<label for='con_number'>Enter Contract Number to delete:</label>";
echo "<input type='text' name='con_number' required>";
echo "<button type='submit' name='delete_contract'>Удалить контракт</button>";
echo "</form>";
?>
