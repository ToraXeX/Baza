<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Обработка формы после отправки
    $cus_id = $_POST["cus_id"];

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

    // Проверка, существует ли клиент с указанным cus_id
    $query_check_customer = "SELECT * FROM customer WHERE cus_id = :cus_id";
    $stmt_check_customer = $db->prepare($query_check_customer);
    $stmt_check_customer->bindParam(':cus_id', $cus_id, PDO::PARAM_INT);
    $stmt_check_customer->execute();

    if ($stmt_check_customer->rowCount() > 0) {
        // Выполнение SQL-запроса для удаления клиента
        $query = "DELETE FROM customer WHERE cus_id = :cus_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':cus_id', $cus_id, PDO::PARAM_INT);
        $stmt->execute();

        echo "<p>Клиент успешно удален.</p>";
    } else {
        echo "<p>Клиент с указанным ID не найден.</p>";
    }
}
?>
