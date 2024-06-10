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

require('fpdf/fpdf.php'); // Убедитесь, что путь к fpdf.php указан верно

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Shopping Cart', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

if (count($cart_items) > 0) {
    foreach ($cart_items as $item) {
        $pdf->Cell(0, 10, 'Product: ' . $item['prd_desc'], 0, 1);
        $pdf->Cell(0, 10, 'Price: ' . $item['prd_value'], 0, 1);
        $pdf->Cell(0, 10, 'Quantity: ' . $item['quantity'], 0, 1);
        $pdf->Ln(5);
    }
    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Total Price: ' . $total_price, 0, 1);
} else {
    $pdf->Cell(0, 10, 'Cart is empty.', 0, 1);
}

$pdf->Output('D', 'cart.pdf'); // Загрузка PDF в браузер

exit();
?>
