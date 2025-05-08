<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $title = $conn->real_escape_string($data['title']);
    $type = !empty($data['type']) ? "'". $conn->real_escape_string($data['type']) . "'" : 'NULL';
    $releaseYear = !empty($data['releaseYear']) ? (int)$data['releaseYear'] : 'NULL';
    $studioID = !empty($data['studioID']) ? "'". $conn->real_escape_string($data['studioID']) . "'" : 'NULL';

    if (empty($title)) {
        echo json_encode(['status' => 'error', 'message' => 'Title is required']);
        $conn->close();
        exit;
    }

    $sql = "INSERT INTO media (Title, Type, ReleaseYear, StudioID)
            VALUES ('$title', $type, $releaseYear, $studioID)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $conn->close();
}
?>