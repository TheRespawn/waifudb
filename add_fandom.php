<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $waifuID = $data['waifuID'] ? "'". $conn->real_escape_string($data['waifuID']) . "'" : 'NULL';
    $fanClubName = $conn->real_escape_string($data['fanClubName']);
    $memberCount = $data['memberCount'] ? (int)$data['memberCount'] : 'NULL';
    $platform = $conn->real_escape_string($data['platform']);

    $sql = "INSERT INTO fandom (WaifuID, FanClubName, MemberCount, Platform)
            VALUES ($waifuID, '$fanClubName', $memberCount, '$platform')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $conn->close();
}
?>