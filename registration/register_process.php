<?php
require_once('../php/db_connect.php'); // Исправленный путь
require_once('registration_functions.php'); // Путь к файлу с функциями регистрации

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получаем данные из формы
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Хешируем пароль
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Добавляем пользователя в базу данных
    if (register_user($username, $hashed_password)) {
        // Пользователь успешно зарегистрирован, перенаправляем на страницу входа
        header("Location: login.php");
        exit();
    } else {
        // Ошибка при регистрации
        echo "Ошибка при регистрации пользователя.";
    }
}
?>
