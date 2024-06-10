<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Отчет по статусам заказа</title>
</head>
<body>

<header>
    <div class="logo">
        <img src="../images/logo.jpg" alt="UltraShop logo">
        <span>UltraShop</span>
    </div>
    <div class="navigation">
        <a href="../procedure/procedure.php">Процедура</a>
        <a href="delivery_history.php">Отчет по доставке</a>
        <a href="status_zakaz.php">Отчет по статусу заказа</a>
        <a href="../index.php">Home</a>
    </div>
</header>

<main>
    <div class="main-content">
        <h2>Отчет по статусам заказа</h2>

        <form method="post" action="">
            <label for="status">Выберите статус заказа:</label>
            <select name="status" required>
                <option value="3">Выполненные</option>
                <option value="1">В процессе</option>
                <option value="0">Сборка</option>
            </select>
            <button type="submit">Получить отчет</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $status = $_POST["status"];

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

            // Выполнение SQL-запроса с использованием параметров
            $query = "SELECT * FROM zakaz WHERE zak_status = :status";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll();

            // Вывод результатов
            echo "<table class='table'>";
            echo "<tr><th>ID заказа</th><th>ID контракта</th><th>Дата заказа</th><th>Дата подготовки</th><th>Статус</th></tr>";

            foreach ($result as $row) {
                echo "<tr><td>{$row['zak_id']}</td><td>{$row['con_id']}</td><td>{$row['zak_date']}</td><td>{$row['zak_prep_date']}</td><td>{$row['zak_status']}</td></tr>";
            }

            echo "</table>";
        }
        ?>
    </div>
</main>

</body>
</html>
