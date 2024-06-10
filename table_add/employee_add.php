<!-- employee_add.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Добавление сотрудника</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Добавление сотрудника</h2>

        <form method="post" action="">
            <!-- Добавьте необходимые поля для ввода данных для нового сотрудника -->
            <label for="emp_id">Employee ID:</label>
            <input type="text" name="emp_id" required>

            <label for="emp_name">Name:</label>
            <input type="text" name="emp_name" required>

            <label for="emp_position">Position:</label>
            <input type="text" name="emp_position" required>

            <button type="submit">Добавить сотрудника</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $emp_id = $_POST["emp_id"];
            $emp_name = $_POST["emp_name"];
            $emp_position = $_POST["emp_position"];

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

            // Выполнение SQL-запроса для добавления сотрудника
            $query = "INSERT INTO employee (emp_id, emp_name, emp_position) 
                      VALUES (:emp_id, :emp_name, :emp_position)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':emp_id', $emp_id, PDO::PARAM_INT);
            $stmt->bindParam(':emp_name', $emp_name, PDO::PARAM_STR);
            $stmt->bindParam(':emp_position', $emp_position, PDO::PARAM_STR);
            $stmt->execute();

            echo "<p>Сотрудник успешно добавлен.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
