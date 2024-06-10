<!-- zakaz_add.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Добавление заказа</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Добавление заказа</h2>

        <form method="post" action="">
            <!-- Добавьте необходимые поля для ввода данных для нового заказа -->
            <label for="zak_id">ID заказа:</label>
            <input type="text" name="zak_id" required>

            <label for="con_id">ID контракта:</label>
            <input type="text" name="con_id" required>

            <label for="zak_date">Дата заказа:</label>
            <input type="date" name="zak_date" required>

            <label for="zak_prep_date">Дата подготовки:</label>
            <input type="date" name="zak_prep_date" required>

            <label for="zak_status">Статус:</label>
            <input type="text" name="zak_status" required>

            <label for="zak_due_date">Дата доставки:</label>
            <input type="date" name="zak_due_date" required>

            <button type="submit">Добавить заказ</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $zak_id = $_POST["zak_id"];
            $con_id = $_POST["con_id"];
            $zak_date = $_POST["zak_date"];
            $zak_prep_date = $_POST["zak_prep_date"];
            $zak_status = $_POST["zak_status"];
            $zak_due_date = $_POST["zak_due_date"];

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

            // Проверка, существует ли заказ с указанным zak_id
            $query_check_order = "SELECT * FROM zakaz WHERE zak_id = :zak_id";
            $stmt_check_order = $db->prepare($query_check_order);
            $stmt_check_order->bindParam(':zak_id', $zak_id, PDO::PARAM_INT);
            $stmt_check_order->execute();

            if ($stmt_check_order->rowCount() > 0) {
                // Заказ с указанным zak_id уже существует
                echo "<p>Заказ с указанным ID уже существует.</p>";
            } else {
                // Выполнение SQL-запроса для добавления заказа
                $query = "INSERT INTO zakaz (zak_id, con_id, zak_date, zak_prep_date, zak_status, zak_due_date) 
                          VALUES (:zak_id, :con_id, :zak_date, :zak_prep_date, :zak_status, :zak_due_date)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':zak_id', $zak_id, PDO::PARAM_INT);
                $stmt->bindParam(':con_id', $con_id, PDO::PARAM_INT);
                $stmt->bindParam(':zak_date', $zak_date, PDO::PARAM_STR);
                $stmt->bindParam(':zak_prep_date', $zak_prep_date, PDO::PARAM_STR);
                $stmt->bindParam(':zak_status', $zak_status, PDO::PARAM_STR);
                $stmt->bindParam(':zak_due_date', $zak_due_date, PDO::PARAM_STR);
                $stmt->execute();

                echo "<p>Заказ успешно добавлен.</p>";
            }
        }
        ?>
    </div>
</main>

</body>
</html>
