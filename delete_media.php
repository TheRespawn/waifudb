<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $identifier = $conn->real_escape_string($data['identifier']);

    if (is_numeric($identifier)) {
        $sql = "DELETE FROM media WHERE MediaID = '$identifier'";
    } else {
        $sql = "DELETE FROM media WHERE Title = '$identifier' LIMIT 1";
    }

    if ($conn->query($sql) === TRUE && $conn->affected_rows > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Media not found or error occurred']);
    }

    $conn->close();
}
?>