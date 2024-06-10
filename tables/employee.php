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

// Обработка удаления сотрудника
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_employee'])) {
    $emp_name = $_POST['emp_name'];
    // Выполнение SQL-запроса для удаления сотрудника
    $query_delete = "DELETE FROM employee WHERE emp_name = :emp_name";
    $stmt_delete = $db->prepare($query_delete);
    $stmt_delete->bindParam(':emp_name', $emp_name, PDO::PARAM_STR);
    $stmt_delete->execute();
}

// Запрос к базе данных (ваш запрос)
$query = "SELECT emp_name, emp_position FROM employee";
$result = $db->query($query);

// Вывод данных
echo "<table class='table'>";
echo "<tr><th>Name</th><th>Position</th><th>Actions</th></tr>";

foreach ($result as $row) {
    echo "<tr><td>{$row['emp_name']}</td><td>{$row['emp_position']}</td>";
    
    // Добавлена ссылка для редактирования сотрудника
    echo "<td><a href='table_redactor/employee_red.php?emp_name={$row['emp_name']}'>Edit</a></td></tr>";
}

echo "</table>";

// Форма для добавления сотрудника
echo "<h2>Добавление сотрудника</h2>";
echo "<form method='post' action='table_add/employee_add.php'>";
echo "<label for='emp_name'>Name:</label>";
echo "<input type='text' name='emp_name' required>";

echo "<label for='emp_position'>Position:</label>";
echo "<input type='text' name='emp_position' required>";

echo "<button type='submit'>Добавить сотрудника</button>";
echo "</form>";

// Форма для удаления сотрудника по имени
echo "<h2>Удаление сотрудника</h2>";
echo "<form method='post'>";
echo "<label for='emp_name'>Enter Employee Name to delete:</label>";
echo "<input type='text' name='emp_name' required>";
echo "<button type='submit' name='delete_employee'>Удалить сотрудника</button>";
echo "</form>";
?>
