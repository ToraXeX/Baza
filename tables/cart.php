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

// Временно назначаем ID пользователя для тестирования
$user_id = 1; // Используйте 1 для тестирования

// Обработка запроса на очистку корзины
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clear_cart'])) {
    $clear_query = "DELETE FROM cart WHERE customer_id = :customer_id";
    $stmt_clear = $db->prepare($clear_query);
    $stmt_clear->bindParam(':customer_id', $user_id, PDO::PARAM_INT);
    $stmt_clear->execute();
    header("Location: cart.php");
    exit();
}

// Запрос к базе данных для получения содержимого корзины пользователя вместе с названиями товаров
$query = "
    SELECT cart.quantity, products.prd_desc, products.prd_value 
    FROM cart 
    JOIN products ON cart.product_id = products.prd_id 
    WHERE cart.customer_id = :customer_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':customer_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Рассчитываем общую сумму корзины
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['prd_value'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Shopping Cart</h1>
        <div class="cart-container">
            <!-- Вывод содержимого корзины -->
            <?php if (count($cart_items) > 0): ?>
                <ul class="cart-items">
                    <?php foreach ($cart_items as $item): ?>
                        <li>
                            <div class="cart-item-info">
                                Product: <?php echo htmlspecialchars($item['prd_desc']); ?><br>
                                Price: <?php echo htmlspecialchars($item['prd_value']); ?><br>
                                Quantity: <?php echo htmlspecialchars($item['quantity']); ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="total-price">
                    Total Price: <?php echo $total_price; ?>
                </div>
                <!-- Форма для очистки корзины -->
                <form method="post" action="">
                    <button type="submit" name="clear_cart" class="clear-cart-button">Clear Cart</button>
                </form>
                <!-- Кнопка для сохранения корзины в PDF -->
                <form method="post" action="../generate_cart_pdf.php">
                    <button type="submit" class="pdf-cart-button" name="save_pdf">Save as PDF</button>
                </form>
                <a href="../index.php" class="home-button">Home</a>
            <?php else: ?>
                <p class="empty-cart">Cart is empty.</p>
            <?php endif; ?>
        </div>
        <div class="footer">
            <p>&copy; <?php echo date("Y"); ?> UltraShop</p>
        </div>
    </div>
</body>
</html>
