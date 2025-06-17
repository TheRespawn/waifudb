<?php
include '../connect.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => true, 'message' => 'Method not allowed']);
    exit;
}

$table = isset($_POST['update_table']) ? $_POST['update_table'] : '';
$id = isset($_POST['update_id']) ? intval($_POST['update_id']) : 0;

if (!$table || !$id) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Table and ID are required']);
    exit;
}

$tables = [
    'waifu' => [
        'id' => 'WaifuID',
        'fields' => [
            'Name' => ['type' => 'string', 'name' => 'waifu_name'],
            'Origin' => ['type' => 'string', 'name' => 'waifu_origin'],
            'Personality' => ['type' => 'string', 'name' => 'waifu_personality'],
            'Appearance' => ['type' => 'text', 'name' => 'waifu_appearance'],
            'Popularity' => ['type' => 'decimal', 'name' => 'waifu_popularity'],
            'ImagePath' => ['type' => 'string', 'name' => 'waifu_image_path']
        ]
    ],
    'studio' => [
        'id' => 'StudioID',
        'fields' => [
            'Name' => ['type' => 'string', 'name' => 'studio_name'],
            'Location' => ['type' => 'string', 'name' => 'studio_location'],
            'FoundedYear' => ['type' => 'int', 'name' => 'studio_founded_year']
        ]
    ],
    'media' => [
        'id' => 'MediaID',
        'fields' => [
            'WaifuID' => ['type' => 'int', 'name' => 'media_waifu_id'],
            'StudioID' => ['type' => 'int', 'name' => 'media_studio_id'],
            'Title' => ['type' => 'string', 'name' => 'media_title'],
            'Type' => ['type' => 'string', 'name' => 'media_type'],
            'ReleaseYear' => ['type' => 'int', 'name' => 'media_release_year']
        ]
    ],
    'voice_actor' => [
        'id' => 'VoiceActorID',
        'fields' => [
            'WaifuID' => ['type' => 'int', 'name' => 'voice_actor_waifu_id'],
            'Name' => ['type' => 'string', 'name' => 'voice_actor_name'],
            'Nationality' => ['type' => 'string', 'name' => 'voice_actor_nationality'],
            'Agency' => ['type' => 'string', 'name' => 'voice_actor_agency']
        ]
    ],
    'fandom' => [
        'id' => 'FandomID',
        'fields' => [
            'WaifuID' => ['type' => 'int', 'name' => 'fandom_waifu_id'],
            'FanClubName' => ['type' => 'string', 'name' => 'fandom_fan_club_name'],
            'MemberCount' => ['type' => 'int', 'name' => 'fandom_member_count'],
            'Platform' => ['type' => 'string', 'name' => 'fandom_platform']
        ]
    ],
    'abilities' => [
        'id' => 'AbilityID',
        'fields' => [
            'WaifuID' => ['type' => 'int', 'name' => 'ability_waifu_id'],
            'Name' => ['type' => 'string', 'name' => 'ability_name'],
            'Description' => ['type' => 'text', 'name' => 'ability_description'],
            'Type' => ['type' => 'string', 'name' => 'ability_type']
        ]
    ],
    'merchandise' => [
        'id' => 'MerchID',
        'fields' => [
            'WaifuID' => ['type' => 'int', 'name' => 'merchandise_waifu_id'],
            'Name' => ['type' => 'string', 'name' => 'merchandise_name'],
            'Type' => ['type' => 'string', 'name' => 'merchandise_type'],
            'Price' => ['type' => 'decimal', 'name' => 'merchandise_price'],
            'ReleaseDate' => ['type' => 'date', 'name' => 'merchandise_release_date']
        ]
    ]
];

if (!isset($tables[$table])) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Invalid table specified']);
    exit;
}

$updates = [];
$params = [];
$types = '';

foreach ($tables[$table]['fields'] as $field => $info) {
    if (isset($_POST[$info['name']]) && $_POST[$info['name']] !== '') {
        $value = $_POST[$info['name']];
        switch ($info['type']) {
            case 'string':
            case 'text':
                $updates[] = "`$field` = ?";
                $params[] = $conn->real_escape_string($value);
                $types .= 's';
                break;
            case 'int':
                $updates[] = "`$field` = ?";
                $params[] = intval($value);
                $types .= 'i';
                break;
            case 'decimal':
                $updates[] = "`$field` = ?";
                $params[] = floatval($value);
                $types .= 'd';
                break;
            case 'date':
                $updates[] = "`$field` = ?";
                $params[] = $value;
                $types .= 's';
                break;
        }
    }
}

if (empty($updates)) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'No fields to update']);
    exit;
}

$query = "UPDATE `$table` SET " . implode(', ', $updates) . " WHERE `" . $tables[$table]['id'] . "` = ?";
$params[] = $id;
$types .= 'i';

$stmt = $conn->prepare($query);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);
if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'Execute failed: ' . $stmt->error]);
    exit;
}

if ($stmt->affected_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => true, 'message' => 'No entry found with the specified ID']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Entry updated successfully']);
$stmt->close();
$conn->close();
?>