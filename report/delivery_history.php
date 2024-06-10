<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Отчет по истории доставки определенного курьера</title>
</head>
<body>

<header>
    <div class="logo">
        <img src="../images/logo.jpg" alt="UltraShop logo">
        <span>UltraShop</span>
    </div>
    <div class="navigation">
        <a href="../procedure/procedure.php">Процедура</a>
        <a href="delivery_history.php">Отчет по истории доставки определенного курьера</a>
        <a href="status_zakaz.php">Отчет по статусам заказа</a>
        <a href="../index.php">Home</a>
    </div>
</header>

<main>
    <div class="main-content">
        <h2>Отчет по истории доставки определенного курьера</h2>

        <form method="post" action="">
            <label for="emp_id">Введите ID работника:</label>
            <input type="text" name="emp_id" required>
            <button type="submit">Получить отчет</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $emp_id = $_POST["emp_id"];

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

            // Выполнение SQL-запроса с использованием JOIN
            $query = "SELECT doing.*, employee.emp_name FROM doing
                      JOIN employee ON doing.emp_id = employee.emp_id
                      WHERE doing.emp_id = $emp_id";
            $result = $db->query($query);

            // Вывод результатов
            echo "<table class='table'>";
            echo "<tr><th>ID доставки</th><th>ID работника</th><th>Имя работника</th></tr>";

            foreach ($result as $row) {
                echo "<tr><td>{$row['del_id']}</td><td>{$row['emp_id']}</td><td>{$row['emp_name']}</td></tr>";
            }

            echo "</table>";
        }
        ?>
    </div>
</main>

</body>
</html>
