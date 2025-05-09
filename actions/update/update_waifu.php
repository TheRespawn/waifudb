<?php
include '../../connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$waifuID = intval($data['id']);
$name = $conn->real_escape_string($data['name']);
$origin = $conn->real_escape_string($data['origin']);
$personality = $conn->real_escape_string($data['personality']);
$appearance = $conn->real_escape_string($data['appearance']);
$popularity = floatval($data['popularity']);
$mediaTitle = $conn->real_escape_string($data['media'] ?? '');
$voiceActorName = $conn->real_escape_string($data['voiceActor'] ?? '');
$imageURL = '/images/' . basename($conn->real_escape_string($data['imageURL']));

$mediaID = null;
if ($mediaTitle) {
    $mediaResult = $conn->query("SELECT MediaID FROM media WHERE Title = '$mediaTitle'");
    if ($mediaResult && $mediaResult->num_rows > 0) {
        $mediaID = $mediaResult->fetch_assoc()['MediaID'];
    } else {
        $conn->query("INSERT INTO media (Title) VALUES ('$mediaTitle')");
        $mediaID = $conn->insert_id;
    }
}

$voiceActorID = null;
if ($voiceActorName) {
    $voiceResult = $conn->query("SELECT VoiceActorID FROM voiceactor WHERE Name = '$voiceActorName'");
    if ($voiceResult && $voiceResult->num_rows > 0) {
        $voiceActorID = $voiceResult->fetch_assoc()['VoiceActorID'];
    } else {
        $conn->query("INSERT INTO voiceactor (Name) VALUES ('$voiceActorName')");
        $voiceActorID = $conn->insert_id;
    }
}

$sql = "UPDATE waifu SET 
        Name = '$name',
        Origin = '$origin',
        Personality = '$personality',
        Appearance = '$appearance',
        Popularity = '$popularity',
        MediaID = " . ($mediaID ? $mediaID : 'NULL') . ",
        VoiceActorID = " . ($voiceActorID ? $voiceActorID : 'NULL') . ",
        ImageURL = '$imageURL'
        WHERE WaifuID = $waifuID";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success', 'message' => 'Waifu updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $conn->error]);
}

$conn->close();
?>