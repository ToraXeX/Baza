<!-- table_redactor/customer_red.php -->

<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cus_id = $_POST['cus_id'];
    $field = $_POST['field'];
    $new_value = $_POST['new_value'];

    $query_update = "UPDATE customer SET $field = :new_value WHERE cus_id = :cus_id";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bindParam(':new_value', $new_value);
    $stmt_update->bindParam(':cus_id', $cus_id);
    $stmt_update->execute();
}

if (isset($_GET['id'])) {
    $cus_id = $_GET['id'];
    $query_select = "SELECT * FROM customer WHERE cus_id = :cus_id";
    $stmt_select = $db->prepare($query_select);
    $stmt_select->bindParam(':cus_id', $cus_id);
    $stmt_select->execute();
    $customer = $stmt_select->fetch(PDO::FETCH_ASSOC);
} else {
    $cus_id = '';
    $customer = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Редактирование клиента</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Редактирование клиента</h2>

        <form method="post" action="">
            <label for="cus_id">Введите ID клиента:</label>
            <input type="text" name="cus_id" value="<?php echo $cus_id; ?>" required>
            <label for="field">Выберите поле для редактирования:</label>
            <select name="field">
                <option value="cus_name">Name</option>
                <option value="cus_phone">Phone</option>
            </select>
            <label for="new_value">Введите новое значение:</label>
            <input type="text" name="new_value" required>
            <button type="submit">Применить изменения</button>
        </form>

        <?php if (!empty($customer)): ?>
            <p>Текущие значения:</p>
            <ul>
                <?php foreach ($customer as $key => $value): ?>
                    <li><?php echo "$key: $value"; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
