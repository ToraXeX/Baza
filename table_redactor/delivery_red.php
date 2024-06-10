<!-- table_redactor/delivery_red.php -->

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
    $del_id = $_POST['del_id'];
    $field = $_POST['field'];
    $new_value = $_POST['new_value'];

    $query_update = "UPDATE delivery SET $field = :new_value WHERE del_id = :del_id";
    $stmt_update = $db->prepare($query_update);
    $stmt_update->bindParam(':new_value', $new_value);
    $stmt_update->bindParam(':del_id', $del_id);
    $stmt_update->execute();
}

if (isset($_GET['id'])) {
    $del_id = $_GET['id'];
    $query_select = "SELECT * FROM delivery WHERE del_id = :del_id";
    $stmt_select = $db->prepare($query_select);
    $stmt_select->bindParam(':del_id', $del_id);
    $stmt_select->execute();
    $delivery = $stmt_select->fetch(PDO::FETCH_ASSOC);
} else {
    $del_id = '';
    $delivery = [];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Редактирование доставки</title>
</head>
<body>

<main>
    <div class="main-content">
        <h2>Редактирование доставки</h2>

        <form method="post" action="">
            <label for="del_id">Введите ID доставки:</label>
            <input type="text" name="del_id" value="<?php echo $del_id; ?>" required>
            <label for="field">Выберите поле для редактирования:</label>
            <select name="field">
                <option value="zak_id">Zakaz ID</option>
                <option value="del_cost">Cost</option>
                <option value="del_date">Date</option>
                <option value="del_status">Status</option>
            </select>
            <label for="new_value">Введите новое значение:</label>
            <input type="text" name="new_value" required>
            <button type="submit">Применить изменения</button>
        </form>

        <?php if (!empty($delivery)): ?>
            <p>Текущие значения:</p>
            <ul>
                <?php foreach ($delivery as $key => $value): ?>
                    <li><?php echo "$key: $value"; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
