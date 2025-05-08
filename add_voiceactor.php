<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $name = $conn->real_escape_string($data['name']);
    $nationality = $conn->real_escape_string($data['nationality']);
    $agency = $conn->real_escape_string($data['agency']);

    $sql = "INSERT INTO voiceactor (Name, Nationality, Agency)
            VALUES ('$name', '$nationality', '$agency')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $conn->close();
}
?>