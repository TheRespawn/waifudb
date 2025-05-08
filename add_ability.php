<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $waifuID = $data['waifuID'] ? "'". $conn->real_escape_string($data['waifuID']) . "'" : 'NULL';
    $name = $conn->real_escape_string($data['name']);
    $description = $conn->real_escape_string($data['description']);
    $type = $conn->real_escape_string($data['type']);

    $sql = "INSERT INTO abilities (WaifuID, Name, Description, Type)
            VALUES ($waifuID, '$name', '$description', '$type')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $conn->close();
}
?>