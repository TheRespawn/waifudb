<?php
$host = 'localhost';
$user = 'root';
$pass = '#Kotowaifu13';
$db = 'waifudb';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
