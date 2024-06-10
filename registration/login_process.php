<?php
require_once('../php/db_connect.php');
require_once('../php/session_functions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Добавим вывод для отладки
    echo "Получены данные: Имя пользователя - $username, Пароль - $password<br>";

    if (authenticate_user($db, $username, $password)) {
        // Добавим вывод для отладки
        echo "Аутентификация успешна. Перенаправление на index.php...<br>";
        
        header("Location: ../index.php");
        exit();
    } else {
        echo "Неверное имя пользователя или пароль.";
    }
} else {
    echo "Метод запроса не является POST.";
}
?>
