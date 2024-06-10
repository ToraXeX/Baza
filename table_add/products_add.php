<!-- products_add.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Добавление продукта</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Добавление продукта</h2>

        <form method="post" action="">
            <!-- Добавьте необходимые поля для ввода данных для нового продукта -->
            <label for="prd_id">ID продукта (вручную):</label>
            <input type="text" name="prd_id" required>

            <label for="prd_desc">Описание:</label>
            <input type="text" name="prd_desc" required>

            <label for="prd_value">Значение:</label>
            <input type="text" name="prd_value" required>

            <label for="qua_kolvo">Количество:</label>
            <input type="number" name="qua_kolvo" required>

            <button type="submit">Добавить продукт</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $prd_id = $_POST["prd_id"];
            $prd_desc = $_POST["prd_desc"];
            $prd_value = $_POST["prd_value"];
            $qua_kolvo = $_POST["qua_kolvo"];

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

            // Выполнение SQL-запроса для добавления продукта
            $query = "INSERT INTO products (prd_id, prd_desc, prd_value, qua_kolvo) 
                      VALUES (:prd_id, :prd_desc, :prd_value, :qua_kolvo)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':prd_id', $prd_id, PDO::PARAM_INT);
            $stmt->bindParam(':prd_desc', $prd_desc, PDO::PARAM_STR);
            $stmt->bindParam(':prd_value', $prd_value, PDO::PARAM_STR);
            $stmt->bindParam(':qua_kolvo', $qua_kolvo, PDO::PARAM_INT);
            $stmt->execute();

            echo "<p>Продукт успешно добавлен.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
