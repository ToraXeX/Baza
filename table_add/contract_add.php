<!-- contract_add.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Добавление контракта</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Добавление контракта</h2>

        <form method="post" action="">
            <!-- Добавьте необходимые поля для ввода данных для нового контракта -->
            <label for='con_id'>Contract ID:</label>
            <input type='text' name='con_id' required>

            <label for='emp_id'>Employee ID:</label>
            <input type='text' name='emp_id' required>

            <label for='con_date'>Date:</label>
            <input type='date' name='con_date' required>

            <label for='con_number'>Number:</label>
            <input type='text' name='con_number' required>

            <label for='cus_id'>Customer ID:</label>
            <input type='text' name='cus_id' required>

            <button type='submit'>Добавить контракт</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $con_id = $_POST['con_id'];
            $emp_id = $_POST['emp_id'];
            $con_date = $_POST['con_date'];
            $con_number = $_POST['con_number'];
            $cus_id = $_POST['cus_id'];

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

            // Выполнение SQL-запроса для добавления контракта
            $query = "INSERT INTO contract (con_id, emp_id, con_date, con_number, cus_id) 
                      VALUES (:con_id, :emp_id, :con_date, :con_number, :cus_id)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':con_id', $con_id, PDO::PARAM_INT);
            $stmt->bindParam(':emp_id', $emp_id, PDO::PARAM_INT);
            $stmt->bindParam(':con_date', $con_date, PDO::PARAM_STR);
            $stmt->bindParam(':con_number', $con_number, PDO::PARAM_STR);
            $stmt->bindParam(':cus_id', $cus_id, PDO::PARAM_INT);
            $stmt->execute();

            echo "<p>Контракт успешно добавлен.</p>";
        }
        ?>
    </div>
</main>

</body>
</html>
