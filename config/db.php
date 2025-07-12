<?php
$host = 'localhost';
$db = 'myblog';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . $conn->connect_error);
}
?>
