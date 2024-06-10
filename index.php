<?php
session_start();
require_once('php/session_functions.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>UltraShop</title>
</head>
<body>

<header>
    <div class="logo">
        <img src="images/logo.jpg" alt="UltraShop logo">
        <span>UltraShop</span>
    </div>
    <div class="navigation">
        <a href="index.php">Home</a>
        <a href="procedure/procedure.php">Процедура</a>
        <a href="report/delivery_history.php">Отчет по истории доставки определенного курьера</a>
        <a href="report/status_zakaz.php">Отчет по статусам заказа</a>
        <a href="tables/cart.php">Корзина</a>
        <?php
        if (is_logged_in()) {
            echo '<a href="registration/logout.php">Выйти (' . htmlspecialchars($_SESSION['username']) . ')</a>';
        } else {
            echo '<a href="registration/login.php">Войти</a>';
            echo '<a href="registration/register.php">Регистрация</a>';
        }
        ?>
    </div>
</header>

<main>
    <div class="table-selection">
        <div class="menu-content">
            <a href="?table=contract">Контракты</a>
            <a href="?table=customer">Клиенты</a>
            <a href="?table=delivery">Доставка</a>
            <a href="?table=employee">Сотрудники</a>
            <a href="?table=products">Продукты</a>
            <a href="?table=zakaz">Заказы</a>
        </div>
    </div>
    <div class="main-content">
        <?php
        // Проверяем наличие параметра "table" в URL
        if (isset($_GET['table'])) {
            $tableName = $_GET['table'];
            // Используйте $tableName для загрузки соответствующей таблицы
            include("tables/$tableName.php");

            // Проверяем, является ли выбранная таблица "Заказы"
            if ($tableName == "zakaz") {
                // Отображаем форму для добавления заказа из папки table_add
                include("table_add/zakaz_add.php");
            }
        }
        ?>
    </div>
</main>

</body>
</html>
