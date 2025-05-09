<?php
include '../../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $waifuID = $data['waifuID'] ? "'". $conn->real_escape_string($data['waifuID']) . "'" : 'NULL';
    $name = $conn->real_escape_string($data['name']);
    $type = $conn->real_escape_string($data['type']);
    $price = $data['price'] ? (float)$data['price'] : 'NULL';
    $releaseDate = $data['releaseDate'] ? "'". $conn->real_escape_string($data['releaseDate']) . "'" : 'NULL';

    $sql = "INSERT INTO merchandise (WaifuID, Name, Type, Price, ReleaseDate)
            VALUES ($waifuID, '$name', '$type', $price, $releaseDate)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $conn->close();
}
?>