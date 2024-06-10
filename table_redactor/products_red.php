<!-- table_redactor/products_red.php -->

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

// Обработка формы после отправки
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prd_id = $_POST['prd_id'];
    $field = $_POST['field'];
    $new_value = $_POST['new_value'];

    // Выполнение SQL-запроса для обновления значения
    $query_update = "UPDATE products SET $field = :new_value WHERE prd_id = :prd_id";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bindParam(':new_value', $new_value);
    $stmt_update->bindParam(':prd_id', $prd_id);
    $stmt_update->execute();
}

// Получение данных о продукте по ID
if (isset($_GET['id'])) {
    $prd_id = $_GET['id'];
    $query_select = "SELECT * FROM products WHERE prd_id = :prd_id";
    $stmt_select = $db->prepare($query_select);
    $stmt_select->bindParam(':prd_id', $prd_id);
    $stmt_select->execute();
    $product = $stmt_select->fetch(PDO::FETCH_ASSOC);
} else {
    $prd_id = '';
    $product = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Редактирование продукта</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Редактирование продукта</h2>

        <form method="post" action="">
            <label for="prd_id">Введите ID продукта:</label>
            <input type="text" name="prd_id" value="<?php echo $prd_id; ?>" required>
            <label for="field">Выберите поле для редактирования:</label>
            <select name="field">
                <option value="prd_desc">Description</option>
                <option value="prd_value">Value</option>
                <option value="qua_kolvo">Quantity</option>
            </select>
            <label for="new_value">Введите новое значение:</label>
            <input type="text" name="new_value" required>
            <button type="submit">Применить изменения</button>
        </form>

        <?php if (!empty($product)): ?>
            <p>Текущие значения:</p>
            <ul>
                <?php foreach ($product as $key => $value): ?>
                    <li><?php echo "$key: $value"; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
