<?php
$host = "localhost";
$user = "postgres";
$pass = "3237";
$dbname = "ZakaZ_for_clubs";

try {
    $db = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; // Добавил вывод сообщения об успешном подключении
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>