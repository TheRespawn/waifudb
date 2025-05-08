<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $name = $conn->real_escape_string($data['name']);
    $location = $conn->real_escape_string($data['location']);
    $foundedYear = $data['foundedYear'] ? (int)$data['foundedYear'] : 'NULL';

    $sql = "INSERT INTO studio (Name, Location, FoundedYear)
            VALUES ('$name', '$location', $foundedYear)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $conn->close();
}
?>