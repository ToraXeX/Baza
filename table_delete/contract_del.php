<!-- table_delete/contract_del.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Удаление контракта</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Удаление контракта</h2>

        <form method="post" action="">
            <!-- Добавьте поле для ввода ID контракта, который нужно удалить -->
            <label for='con_id'>Contract ID:</label>
            <input type='text' name='con_id' required>
            <button type='submit'>Удалить контракт</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $con_id = $_POST['con_id'];

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

            // Выполнение SQL-запроса для удаления контракта
            $query = "DELETE FROM contract WHERE con_id = :con_id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':con_id', $con_id, PDO::PARAM_INT);
            $stmt->execute();

            echo "<p>Контракт с ID $con_id успешно удален.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
