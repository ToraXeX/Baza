<?php
require_once('../php/db_connect.php');

function register_user($username, $hashed_password) {
    global $db;
    
    // Генерируем уникальный идентификатор пользователя на уровне базы данных
    $query = "INSERT INTO user_passwords (cus_id, cus_name, password_hash) VALUES (uuid_generate_v4(), :username, :password)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashed_password);
    
    return $stmt->execute();
}
?>
