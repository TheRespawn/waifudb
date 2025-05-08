<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $name = $conn->real_escape_string($data['name']);
    $origin = $conn->real_escape_string($data['origin']);
    $personality = $conn->real_escape_string($data['personality']);
    $appearance = $conn->real_escape_string($data['appearance']);
    $popularity = (float)$data['popularity'];
    $media = $conn->real_escape_string($data['media']);
    $voiceActor = $conn->real_escape_string($data['voiceActor']);
    $imageURL = $conn->real_escape_string($data['imageURL']);

    $sql = "INSERT INTO waifu (Name, Origin, Personality, Appearance, Popularity, MediaID, VoiceActorID, ImageURL)
            VALUES ('$name', '$origin', '$personality', '$appearance', $popularity, NULL, NULL, '$imageURL')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }

    $conn->close();
}
?>