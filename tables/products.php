<?php
// Подключение к базе данных (ваш код подключения)
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

// Обработка удаления продукта
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $prd_desc = $_POST['prd_desc'];
    // Выполнение SQL-запроса для удаления продукта
    $query_delete = "DELETE FROM products WHERE prd_desc = :prd_desc";
    $stmt_delete = $db->prepare($query_delete);
    $stmt_delete->bindParam(':prd_desc', $prd_desc, PDO::PARAM_STR);
    $stmt_delete->execute();
}

// Запрос к базе данных (ваш запрос)
$query = "SELECT prd_desc, prd_value, qua_kolvo FROM products";
$result = $db->query($query);

// Вывод данных
echo "<table class='table'>";
echo "<tr><th>Description</th><th>Value</th><th>Quantity</th><th>Actions</th></tr>";

foreach ($result as $row) {
    echo "<tr><td>{$row['prd_desc']}</td><td>{$row['prd_value']}</td><td>{$row['qua_kolvo']}</td>";

    // Добавлена ссылка для редактирования продукта
    echo "<td><a href='table_redactor/products_red.php?prd_desc={$row['prd_desc']}'>Edit</a></td>";

    // Добавить форму для добавления продукта в корзину
    echo "<td>";
    echo "<form action='tables/add_to_cart.php' method='post'>"; // Изменено здесь
    echo "<input type='hidden' name='product_desc' value='{$row['prd_desc']}'>";
    echo "<input type='number' name='quantity' value='1' min='1'>";
    echo "<button type='submit'>Добавить в корзину</button>";
    echo "</form>";
    echo "</td>";

    echo "</tr>";
}

echo "</table>";

// Форма для добавления продукта
echo "<h2>Добавление продукта</h2>";
echo "<form method='post' action='table_add/products_add.php'>";
echo "<label for='prd_desc'>Description:</label>";
echo "<input type='text' name='prd_desc' required>";

echo "<label for='prd_value'>Value:</label>";
echo "<input type='text' name='prd_value' required>";

echo "<label for='qua_kolvo'>Quantity:</label>";
echo "<input type='number' name='qua_kolvo' required>";

echo "<button type='submit'>Добавить продукт</button>";
echo "</form>";

// Форма для удаления продукта по описанию
echo "<h2>Удаление продукта</h2>";
echo "<form method='post'>";
echo "<label for='prd_desc'>Enter Product Description to delete:</label>";
echo "<input type='text' name='prd_desc' required>";
echo "<button type='submit' name='delete_product'>Удалить продукт</button>";
echo "</form>";
?>
