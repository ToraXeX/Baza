<?php
session_start();

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

// Проверяем, был ли отправлен запрос POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Проверяем, были ли переданы обязательные параметры
    if (isset($_POST['quantity']) && isset($_POST['product_desc'])) {
        $quantity = $_POST['quantity'];
        $product_desc = $_POST['product_desc']; // Описание продукта, полученное из формы

        // Получаем product_id по product_desc из базы данных
        $get_product_query = "SELECT prd_id FROM products WHERE prd_desc = :prd_desc";
        $stmt_get_product = $db->prepare($get_product_query);
        $stmt_get_product->bindParam(':prd_desc', $product_desc, PDO::PARAM_STR);
        $stmt_get_product->execute();
        $product = $stmt_get_product->fetch(PDO::FETCH_ASSOC);
        $product_id = $product['prd_id'];

        // Временно назначаем ID пользователя
        $customer_id = 1; // Используйте 1 для тестирования

        // Проверяем, есть ли уже такой товар в корзине
        $check_query = "SELECT * FROM cart WHERE customer_id = :customer_id AND product_id = :product_id";
        $stmt_check = $db->prepare($check_query);
        $stmt_check->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt_check->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt_check->execute();
        $existing_item = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($existing_item) {
            // Если товар уже есть в корзине, увеличиваем его количество на указанное значение
            $new_quantity = $existing_item['quantity'] + $quantity;
            $update_query = "UPDATE cart SET quantity = :quantity WHERE customer_id = :customer_id AND product_id = :product_id";
            $stmt_update = $db->prepare($update_query);
            $stmt_update->bindParam(':quantity', $new_quantity, PDO::PARAM_INT);
            $stmt_update->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt_update->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt_update->execute();
        } else {
            // Иначе добавляем новый товар в корзину
            $query = "INSERT INTO cart (customer_id, product_id, quantity) VALUES (:customer_id, :product_id, :quantity)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Перенаправляем обратно на страницу с продуктами
        header("Location: ../index.php?table=products");
        exit();
    } else {
        // Если не все параметры были переданы, отобразить ошибку
        echo "Missing parameters.";
    }
}
?>
