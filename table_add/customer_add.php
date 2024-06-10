<!-- customer_add.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Добавление клиента</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Добавление клиента</h2>

        <form method="post" action="">
            <!-- Добавьте необходимые поля для ввода данных для нового клиента -->
            <label for="cus_id">Customer ID:</label>
            <input type="text" name="cus_id" required>

            <label for="cus_name">Name:</label>
            <input type="text" name="cus_name" required>

            <label for="cus_phone">Phone:</label>
            <input type="text" name="cus_phone" required>

            <button type="submit">Добавить клиента</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $cus_id = $_POST["cus_id"];
            $cus_name = $_POST["cus_name"];
            $cus_phone = $_POST["cus_phone"];

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

            // Выполнение SQL-запроса для добавления клиента
            $query = "INSERT INTO customer (cus_id, cus_name, cus_phone) 
                      VALUES (:cus_id, :cus_name, :cus_phone)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':cus_id', $cus_id, PDO::PARAM_INT);
            $stmt->bindParam(':cus_name', $cus_name, PDO::PARAM_STR);
            $stmt->bindParam(':cus_phone', $cus_phone, PDO::PARAM_STR);
            $stmt->execute();

            echo "<p>Клиент успешно добавлен.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
