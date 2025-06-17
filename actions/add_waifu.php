<?php
header('Content-Type: application/json');
include '../connect.php'; // Adjust path based on directory structure

// Helper function to check if section has data (remains the same)
function hasData($fields, $post) {
    foreach ($fields as $field) {
        if (!empty($post[$field])) {
            return true;
        }
    }
    return false;
}

// Start transaction to ensure data consistency
$conn->begin_transaction();

try {
    // Waifu (required)
    // Added 'waifuImagePath' to the fields to check
    $waifuFields = ['waifuName', 'waifuOrigin', 'waifuPersonality', 'waifuAppearance', 'waifuPopularity', 'waifuImagePath'];
    // Note: 'waifuImagePath' can be optional or required based on your needs.
    // If it's optional, you might not want it in hasData check or handle its absence.
    // For now, assuming it can be NULL if not provided, but the form field will be there.

    if (empty($_POST['waifuName']) || empty($_POST['waifuOrigin'])) { // Basic check for core fields
        throw new Exception('Waifu Name and Origin are required.');
    }

    $waifuName = $conn->real_escape_string($_POST['waifuName']);
    $waifuOrigin = $conn->real_escape_string($_POST['waifuOrigin']);
    $waifuPersonality = $conn->real_escape_string($_POST['waifuPersonality']);
    $waifuAppearance = $conn->real_escape_string($_POST['waifuAppearance']);
    $waifuPopularity = floatval($_POST['waifuPopularity']);
    // Get the image path, ensure it starts with /cards/ or is NULL/empty
    $waifuImagePath = isset($_POST['waifuImagePath']) ? $conn->real_escape_string(trim($_POST['waifuImagePath'])) : NULL;

    if ($waifuImagePath === '') {
        $waifuImagePath = NULL; // Store as NULL if empty string submitted
    } elseif ($waifuImagePath !== NULL && strpos($waifuImagePath, '/cards/') !== 0) {
        // Optional: Basic validation or prefixing if only filename is given
        // For now, we assume user provides the correct path like /cards/image.png
        // You could add more robust validation here if needed.
        // Example: if (strpos($waifuImagePath, '/') !== 0) $waifuImagePath = '/cards/' . $waifuImagePath;
    }


    if ($waifuPopularity < 0 || $waifuPopularity > 99.9) {
        throw new Exception('Popularity must be between 0.0 and 99.9.');
    }

    // Updated Waifu query to include ImagePath
    $waifuQuery = "INSERT INTO Waifu (Name, Origin, Personality, Appearance, Popularity, ImagePath)
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmtWaifu = $conn->prepare($waifuQuery);
    if ($stmtWaifu === false) {
        throw new Exception('Prepare statement for Waifu failed: ' . $conn->error);
    }
    // Bind parameters: s (string), s, s, s, d (double), s (string for ImagePath)
    $stmtWaifu->bind_param("ssssds", $waifuName, $waifuOrigin, $waifuPersonality, $waifuAppearance, $waifuPopularity, $waifuImagePath);

    if (!$stmtWaifu->execute()) {
        throw new Exception('Error adding waifu: ' . $stmtWaifu->error);
    }
    $waifuID = $conn->insert_id;
    $stmtWaifu->close();

    // Studio (optional) - Logic remains largely the same
    $studioFields = ['studioName', 'studioLocation', 'studioFoundedYear'];
    if (hasData($studioFields, $_POST) && !empty($_POST['studioName'])) { // Ensure studioName is present if adding studio
        $studioName = $conn->real_escape_string($_POST['studioName']);
        $studioLocation = !empty($_POST['studioLocation']) ? $conn->real_escape_string($_POST['studioLocation']) : NULL;
        $studioFoundedYear = !empty($_POST['studioFoundedYear']) ? intval($_POST['studioFoundedYear']) : NULL;

        $studioQuery = "INSERT INTO Studio (Name, Location, FoundedYear) VALUES (?, ?, ?)";
        $stmtStudio = $conn->prepare($studioQuery);
        if ($stmtStudio === false) throw new Exception('Prepare statement for Studio failed: ' . $conn->error);
        $stmtStudio->bind_param("ssi", $studioName, $studioLocation, $studioFoundedYear);
        if (!$stmtStudio->execute()) throw new Exception('Error adding studio: ' . $stmtStudio->error);
        $studioID = $conn->insert_id;
        $stmtStudio->close();
    } else {
        $studioID = null;
    }

    // Media (optional) - Logic remains largely the same
    $mediaFields = ['mediaTitle', 'mediaType', 'mediaReleaseYear'];
    if (hasData($mediaFields, $_POST) && !empty($_POST['mediaTitle'])) { // Ensure mediaTitle is present
        if (!$studioID) {
            // If no studio was added in this transaction, try to find an existing one or handle error
            // This part depends on your business logic: either require studio for media,
            // or allow media without studio (would require schema change for StudioID in Media to be NULLable)
            // For now, we'll assume StudioID is required if media is added.
            // A better approach might be to select an existing studio if its name is provided.
            // However, the original script implies a new studio is created if its details are given.
             throw new Exception('Studio is required to add media. Please add studio details or ensure StudioID is available.');
        }
        $mediaTitle = $conn->real_escape_string($_POST['mediaTitle']);
        $mediaType = !empty($_POST['mediaType']) ? $conn->real_escape_string($_POST['mediaType']) : NULL;
        $mediaReleaseYear = !empty($_POST['mediaReleaseYear']) ? intval($_POST['mediaReleaseYear']) : NULL;

        $mediaQuery = "INSERT INTO Media (WaifuID, StudioID, Title, Type, ReleaseYear) VALUES (?, ?, ?, ?, ?)";
        $stmtMedia = $conn->prepare($mediaQuery);
        if ($stmtMedia === false) throw new Exception('Prepare statement for Media failed: ' . $conn->error);
        $stmtMedia->bind_param("iissi", $waifuID, $studioID, $mediaTitle, $mediaType, $mediaReleaseYear);
        if (!$stmtMedia->execute()) throw new Exception('Error adding media: ' . $stmtMedia->error);
        $stmtMedia->close();
    }

    // Voice Actor (optional) - Logic remains largely the same
    $voiceFields = ['voiceName', 'voiceNationality', 'voiceAgency'];
    if (hasData($voiceFields, $_POST) && !empty($_POST['voiceName'])) {
        $voiceName = $conn->real_escape_string($_POST['voiceName']);
        $voiceNationality = !empty($_POST['voiceNationality']) ? $conn->real_escape_string($_POST['voiceNationality']) : NULL;
        $voiceAgency = !empty($_POST['voiceAgency']) ? $conn->real_escape_string($_POST['voiceAgency']) : NULL;

        $voiceQuery = "INSERT INTO VoiceActor (WaifuID, Name, Nationality, Agency) VALUES (?, ?, ?, ?)";
        $stmtVoice = $conn->prepare($voiceQuery);
        if ($stmtVoice === false) throw new Exception('Prepare statement for VoiceActor failed: ' . $conn->error);
        $stmtVoice->bind_param("isss", $waifuID, $voiceName, $voiceNationality, $voiceAgency);
        if (!$stmtVoice->execute()) throw new Exception('Error adding voice actor: ' . $stmtVoice->error);
        $stmtVoice->close();
    }

    // Fandom (optional) - Logic remains largely the same
    $fandomFields = ['fandomFanClubName', 'fandomMemberCount', 'fandomPlatform'];
    if (hasData($fandomFields, $_POST) && !empty($_POST['fandomFanClubName'])) {
        $fandomFanClubName = $conn->real_escape_string($_POST['fandomFanClubName']);
        $fandomMemberCount = !empty($_POST['fandomMemberCount']) ? intval($_POST['fandomMemberCount']) : NULL;
        $fandomPlatform = !empty($_POST['fandomPlatform']) ? $conn->real_escape_string($_POST['fandomPlatform']) : NULL;

        $fandomQuery = "INSERT INTO Fandom (WaifuID, FanClubName, MemberCount, Platform) VALUES (?, ?, ?, ?)";
        $stmtFandom = $conn->prepare($fandomQuery);
        if ($stmtFandom === false) throw new Exception('Prepare statement for Fandom failed: ' . $conn->error);
        $stmtFandom->bind_param("isis", $waifuID, $fandomFanClubName, $fandomMemberCount, $fandomPlatform);
        if (!$stmtFandom->execute()) throw new Exception('Error adding fandom: ' . $stmtFandom->error);
        $stmtFandom->close();
    }

    // Ability (optional) - Logic remains largely the same
    $abilityFields = ['abilityName', 'abilityDescription', 'abilityType'];
    if (hasData($abilityFields, $_POST) && !empty($_POST['abilityName'])) {
        $abilityName = $conn->real_escape_string($_POST['abilityName']);
        $abilityDescription = !empty($_POST['abilityDescription']) ? $conn->real_escape_string($_POST['abilityDescription']) : NULL;
        $abilityType = !empty($_POST['abilityType']) ? $conn->real_escape_string($_POST['abilityType']) : NULL;

        $abilityQuery = "INSERT INTO Abilities (WaifuID, Name, Description, Type) VALUES (?, ?, ?, ?)";
        $stmtAbility = $conn->prepare($abilityQuery);
        if ($stmtAbility === false) throw new Exception('Prepare statement for Abilities failed: ' . $conn->error);
        $stmtAbility->bind_param("isss", $waifuID, $abilityName, $abilityDescription, $abilityType);
        if (!$stmtAbility->execute()) throw new Exception('Error adding ability: ' . $stmtAbility->error);
        $stmtAbility->close();
    }

    // Merchandise (optional) - Logic remains largely the same
    $merchFields = ['merchName', 'merchType', 'merchPrice', 'merchReleaseDate'];
    if (hasData($merchFields, $_POST) && !empty($_POST['merchName'])) {
        $merchName = $conn->real_escape_string($_POST['merchName']);
        $merchType = !empty($_POST['merchType']) ? $conn->real_escape_string($_POST['merchType']) : NULL;
        $merchPrice = !empty($_POST['merchPrice']) ? floatval($_POST['merchPrice']) : NULL;
        $merchReleaseDate = !empty($_POST['merchReleaseDate']) ? $conn->real_escape_string($_POST['merchReleaseDate']) : NULL;

        $merchQuery = "INSERT INTO Merchandise (WaifuID, Name, Type, Price, ReleaseDate) VALUES (?, ?, ?, ?, ?)";
        $stmtMerch = $conn->prepare($merchQuery);
        if ($stmtMerch === false) throw new Exception('Prepare statement for Merchandise failed: ' . $conn->error);
        $stmtMerch->bind_param("issds", $waifuID, $merchName, $merchType, $merchPrice, $merchReleaseDate);
        if (!$stmtMerch->execute()) throw new Exception('Error adding merchandise: ' . $stmtMerch->error);
        $stmtMerch->close();
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Entry added successfully', 'waifuID' => $waifuID]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    http_response_code(400); // Bad request or server error
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>
