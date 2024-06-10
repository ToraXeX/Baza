<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Процедура изменения даты доставки</title>
</head>
<body>

<header>
    <div class="logo">
        <img src="../images/logo.jpg" alt="UltraShop logo">
        <span>UltraShop</span>
    </div>
    <div class="navigation">
        <a href="../procedure/procedure.php">Процедура</a>
        <a href="../report/delivery_history.php">Отчет по истории доставки определенного курьера</a>
        <a href="../report/status_zakaz.php">Отчет по статусам заказа</a>
        <a href="../index.php">Home</a>
    </div>
</header>

<main>
    <div class="main-content">
        <h2>Процедура изменения даты доставки</h2>

        <form method="post" action="">
            <label for="cus_phone">Введите номер телефона заказчика:</label>
            <input type="text" name="cus_phone" required>

            <label for="zak_id">Введите идентификатор заказа:</label>
            <input type="text" name="zak_id" required>

            <label for="new_delivery_date">Введите новую дату доставки:</label>
            <input type="date" name="new_delivery_date" required>

            <button type="submit">Изменить дату доставки</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Обработка формы после отправки
            $cus_phone = $_POST["cus_phone"];
            $zak_id = $_POST["zak_id"];
            $new_delivery_date = $_POST["new_delivery_date"];

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

            // Выполнение процедурной операции
            try {
                $db->beginTransaction();

                // Проверка наличия клиента и заказа
                $query_check = "SELECT * FROM customer WHERE cus_phone = '$cus_phone'";
                $result_check = $db->query($query_check);

                if ($result_check->rowCount() > 0) {
                    // Клиент существует, проверка заказа
                    $query_check_order = "SELECT * FROM zakaz WHERE zak_id = $zak_id";
                    $result_check_order = $db->query($query_check_order);

                    if ($result_check_order->rowCount() > 0) {
                        // Заказ существует, обновление даты доставки в таблице zakaz
                        $query_update_zakaz = "UPDATE zakaz SET zak_due_date = '$new_delivery_date' WHERE zak_id = $zak_id";
                        $db->exec($query_update_zakaz);

                        // Затем обновление даты доставки в таблице delivery
                        $query_update_delivery = "UPDATE delivery SET del_date = '$new_delivery_date' WHERE zak_id = $zak_id";
                        $db->exec($query_update_delivery);

                        echo "<p>Дата доставки успешно изменена.</p>";
                        $db->commit(); // Фиксация изменений
                    } else {
                        echo "<p>Заказ с указанным идентификатором не найден.</p>";
                    }
                } else {
                    echo "<p>Клиент с указанным номером телефона не найден.</p>";
                }
            } catch (PDOException $ex) {
                $db->rollBack(); // Откат в случае ошибки
                echo "Ошибка: " . $ex->getMessage();
            }
        }
        ?>
    </div>
</main>

</body>
</html>
