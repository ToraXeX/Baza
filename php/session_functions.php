<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function authenticate_user($conn, $username, $password) {
    $query = "SELECT * FROM user_passwords WHERE cus_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        // Устанавливаем переменные сессии
        $_SESSION['user_id'] = $user['cus_id'];
        $_SESSION['username'] = $user['cus_name'];
        return true;
    }
    return false;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function logout() {
    session_unset();
    session_destroy();
}
