<!-- table_delete/employee_del.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Удаление сотрудника</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Удаление сотрудника</h2>

        <form method="post" action="">
            <!-- Добавьте поле для ввода ID сотрудника, который нужно удалить -->
            <label for='emp_id'>Employee ID:</label>
            <input type='text' name='emp_id' required>
            <button type='submit'>Удалить сотрудника</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $emp_id = $_POST['emp_id'];

            // Подключение к базе данных
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

            // Выполнение SQL-запроса для удаления сотрудника
            $query = "DELETE FROM employee WHERE emp_id = :emp_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':emp_id', $emp_id, PDO::PARAM_INT);
            $stmt->execute();

            echo "<p>Сотрудник с ID $emp_id успешно удален.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
