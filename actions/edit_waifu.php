<?php
// edit_waifu.php
header('Content-Type: application/json');

// Read and decode JSON payload
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON payload']);
    exit;
}

// Ensure we have an ID
if (empty($data['WaifuID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing WaifuID']);
    exit;
}
$id = (int) $data['WaifuID'];

// Allowed fields to update
$allowed = ['Name', 'Origin', 'Personality', 'Appearance', 'Popularity', 'ImagePath'];

// Connect to database
require_once '../connect.php'; // provides $conn (mysqli)

$updateCount = 0;

foreach ($allowed as $field) {
    if (array_key_exists($field, $data) && $field !== 'WaifuID') {
        $value = $data[$field];
        
        // Prepare dynamic UPDATE for this field
        $stmt = $conn->prepare("UPDATE waifu SET `$field` = ? WHERE `WaifuID` = ?");
        if (!$stmt) {
            continue; // skip if prepare fails
        }

        // Determine parameter types: double for Popularity, string otherwise, then integer for ID
        $paramType = ($field === 'Popularity') ? 'd' : 's';
        $paramType .= 'i';

        // Bind and execute
        $stmt->bind_param($paramType, $value, $id);
        if ($stmt->execute()) {
            $updateCount++;
        }
        $stmt->close();
    }
}

$conn->close();

// Respond success or error
if ($updateCount > 0) {
    echo json_encode([
        'status'  => 'success',
        'message' => "Updated {$updateCount} field(s)."
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'No fields were updated.'
    ]);
}
