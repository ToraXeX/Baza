<!-- table_delete/delivery_del.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Удаление доставки</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Удаление доставки</h2>

        <form method="post" action="">
            <!-- Добавьте поле для ввода ID доставки, которую нужно удалить -->
            <label for='del_id'>Delivery ID:</label>
            <input type='text' name='del_id' required>
            <button type='submit'>Удалить доставку</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $del_id = $_POST['del_id'];

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

            // Выполнение SQL-запроса для удаления доставки
            $query = "DELETE FROM delivery WHERE del_id = :del_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':del_id', $del_id, PDO::PARAM_INT);
            $stmt->execute();

            echo "<p>Доставка с ID $del_id успешно удалена.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
