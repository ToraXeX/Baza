<!-- delivery_add.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Добавление доставки</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Добавление доставки</h2>

        <form method="post" action="">
            <!-- Добавьте необходимые поля для ввода данных для новой доставки -->
            <label for="del_id">Delivery ID:</label>
            <input type="text" name="del_id" required>

            <label for="zak_id">Zakaz ID:</label>
            <input type="text" name="zak_id" required>

            <label for="del_cost">Стоимость:</label>
            <input type="text" name="del_cost" required>

            <label for="del_date">Дата доставки:</label>
            <input type="date" name="del_date" required>

            <label for="del_status">Статус:</label>
            <input type="text" name="del_status" required>

            <button type="submit">Добавить доставку</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $del_id = $_POST["del_id"];
            $zak_id = $_POST["zak_id"];
            $del_cost = $_POST["del_cost"];
            $del_date = $_POST["del_date"];
            $del_status = $_POST["del_status"];

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

            // Выполнение SQL-запроса для добавления доставки с указанным del_id
            $query = "INSERT INTO delivery (del_id, zak_id, del_cost, del_date, del_status) 
                      VALUES (:del_id, :zak_id, :del_cost, :del_date, :del_status)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':del_id', $del_id, PDO::PARAM_INT);
            $stmt->bindParam(':zak_id', $zak_id, PDO::PARAM_INT);
            $stmt->bindParam(':del_cost', $del_cost, PDO::PARAM_STR);
            $stmt->bindParam(':del_date', $del_date, PDO::PARAM_STR);
            $stmt->bindParam(':del_status', $del_status, PDO::PARAM_STR);
            $stmt->execute();

            echo "<p>Доставка успешно добавлена.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
