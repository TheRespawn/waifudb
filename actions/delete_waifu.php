<?php

header('Content-Type: application/json');
include '../connect.php'; 

// Get the posted data
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array

$identifier = null;

if (isset($input['identifier'])) {
    $identifier = trim($input['identifier']);
}

if (empty($identifier)) {
    echo json_encode(['status' => 'error', 'message' => 'Identifier (ID or Name) is required.']);
    $conn->close();
    exit;
}

$identifier = $conn->real_escape_string($identifier);
$sql = "";
$stmt = null;

// schaut ob waifu id oder name
if (is_numeric($identifier)) {
    $sql = "DELETE FROM Waifu WHERE WaifuID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare statement failed (ID): ' . $conn->error]);
        $conn->close();
        exit;
    }
    $stmt->bind_param("i", $identifier);
} else {
    $sql = "DELETE FROM Waifu WHERE Name = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare statement failed (Name): ' . $conn->error]);
        $conn->close();
        exit;
    }
    $stmt->bind_param("s", $identifier);
}
//error handling
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Waifu deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Waifu not found with the given identifier or no changes made.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
