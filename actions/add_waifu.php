<?php
header('Content-Type: application/json');
include '../connect.php'; // Adjust path based on directory structure

// Helper function to check if section has data
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
    $waifuFields = ['waifuName', 'waifuOrigin', 'waifuPersonality', 'waifuAppearance', 'waifuPopularity'];
    if (!hasData($waifuFields, $_POST)) {
        throw new Exception('Waifu details are required.');
    }

    $waifuName = $conn->real_escape_string($_POST['waifuName']);
    $waifuOrigin = $conn->real_escape_string($_POST['waifuOrigin']);
    $waifuPersonality = $conn->real_escape_string($_POST['waifuPersonality']);
    $waifuAppearance = $conn->real_escape_string($_POST['waifuAppearance']);
    $waifuPopularity = floatval($_POST['waifuPopularity']);

    if ($waifuPopularity < 0 || $waifuPopularity > 99.9) {
        throw new Exception('Popularity must be between 0.0 and 99.9.');
    }

    $waifuQuery = "INSERT INTO Waifu (Name, Origin, Personality, Appearance, Popularity)
                   VALUES ('$waifuName', '$waifuOrigin', '$waifuPersonality', '$waifuAppearance', $waifuPopularity)";
    if (!$conn->query($waifuQuery)) {
        throw new Exception('Error adding waifu: ' . $conn->error);
    }
    $waifuID = $conn->insert_id;

    // Studio (optional)
    $studioFields = ['studioName', 'studioLocation', 'studioFoundedYear'];
    if (hasData($studioFields, $_POST)) {
        $studioName = $conn->real_escape_string($_POST['studioName']);
        $studioLocation = !empty($_POST['studioLocation']) ? "'" . $conn->real_escape_string($_POST['studioLocation']) . "'" : 'NULL';
        $studioFoundedYear = !empty($_POST['studioFoundedYear']) ? intval($_POST['studioFoundedYear']) : 'NULL';

        $studioQuery = "INSERT INTO Studio (Name, Location, FoundedYear)
                        VALUES ('$studioName', $studioLocation, $studioFoundedYear)";
        if (!$conn->query($studioQuery)) {
            throw new Exception('Error adding studio: ' . $conn->error);
        }
        $studioID = $conn->insert_id;
    } else {
        $studioID = null;
    }

    // Media (optional)
    $mediaFields = ['mediaTitle', 'mediaType', 'mediaReleaseYear'];
    if (hasData($mediaFields, $_POST)) {
        if (!$studioID) {
            throw new Exception('Studio is required for media.');
        }
        $mediaTitle = $conn->real_escape_string($_POST['mediaTitle']);
        $mediaType = !empty($_POST['mediaType']) ? "'" . $conn->real_escape_string($_POST['mediaType']) . "'" : 'NULL';
        $mediaReleaseYear = !empty($_POST['mediaReleaseYear']) ? intval($_POST['mediaReleaseYear']) : 'NULL';

        $mediaQuery = "INSERT INTO Media (WaifuID, StudioID, Title, Type, ReleaseYear)
                       VALUES ($waifuID, $studioID, '$mediaTitle', $mediaType, $mediaReleaseYear)";
        if (!$conn->query($mediaQuery)) {
            throw new Exception('Error adding media: ' . $conn->error);
        }
    }

    // Voice Actor (optional)
    $voiceFields = ['voiceName', 'voiceNationality', 'voiceAgency'];
    if (hasData($voiceFields, $_POST)) {
        $voiceName = $conn->real_escape_string($_POST['voiceName']);
        $voiceNationality = !empty($_POST['voiceNationality']) ? "'" . $conn->real_escape_string($_POST['voiceNationality']) . "'" : 'NULL';
        $voiceAgency = !empty($_POST['voiceAgency']) ? "'" . $conn->real_escape_string($_POST['voiceAgency']) . "'" : 'NULL';

        $voiceQuery = "INSERT INTO VoiceActor (WaifuID, Name, Nationality, Agency)
                       VALUES ($waifuID, '$voiceName', $voiceNationality, $voiceAgency)";
        if (!$conn->query($voiceQuery)) {
            throw new Exception('Error adding voice actor: ' . $conn->error);
        }
    }

    // Fandom (optional)
    $fandomFields = ['fandomFanClubName', 'fandomMemberCount', 'fandomPlatform'];
    if (hasData($fandomFields, $_POST)) {
        $fandomFanClubName = $conn->real_escape_string($_POST['fandomFanClubName']);
        $fandomMemberCount = !empty($_POST['fandomMemberCount']) ? intval($_POST['fandomMemberCount']) : 'NULL';
        $fandomPlatform = !empty($_POST['fandomPlatform']) ? "'" . $conn->real_escape_string($_POST['fandomPlatform']) . "'" : 'NULL';

        $fandomQuery = "INSERT INTO Fandom (WaifuID, FanClubName, MemberCount, Platform)
                        VALUES ($waifuID, '$fandomFanClubName', $fandomMemberCount, $fandomPlatform)";
        if (!$conn->query($fandomQuery)) {
            throw new Exception('Error adding fandom: ' . $conn->error);
        }
    }

    // Ability (optional)
    $abilityFields = ['abilityName', 'abilityDescription', 'abilityType'];
    if (hasData($abilityFields, $_POST)) {
        $abilityName = $conn->real_escape_string($_POST['abilityName']);
        $abilityDescription = !empty($_POST['abilityDescription']) ? "'" . $conn->real_escape_string($_POST['abilityDescription']) . "'" : 'NULL';
        $abilityType = !empty($_POST['abilityType']) ? "'" . $conn->real_escape_string($_POST['abilityType']) . "'" : 'NULL';

        $abilityQuery = "INSERT INTO Abilities (WaifuID, Name, Description, Type)
                         VALUES ($waifuID, '$abilityName', $abilityDescription, $abilityType)";
        if (!$conn->query($abilityQuery)) {
            throw new Exception('Error adding ability: ' . $conn->error);
        }
    }

    // Merchandise (optional)
    $merchFields = ['merchName', 'merchType', 'merchPrice', 'merchReleaseDate'];
    if (hasData($merchFields, $_POST)) {
        $merchName = $conn->real_escape_string($_POST['merchName']);
        $merchType = !empty($_POST['merchType']) ? "'" . $conn->real_escape_string($_POST['merchType']) . "'" : 'NULL';
        $merchPrice = !empty($_POST['merchPrice']) ? floatval($_POST['merchPrice']) : 'NULL';
        $merchReleaseDate = !empty($_POST['merchReleaseDate']) ? "'" . $conn->real_escape_string($_POST['merchReleaseDate']) . "'" : 'NULL';

        $merchQuery = "INSERT INTO Merchandise (WaifuID, Name, Type, Price, ReleaseDate)
                       VALUES ($waifuID, '$merchName', $merchType, $merchPrice, $merchReleaseDate)";
        if (!$conn->query($merchQuery)) {
            throw new Exception('Error adding merchandise: ' . $conn->error);
        }
    }

    // Commit transaction
    $conn->commit();
    echo json_encode(['status' => 'success', 'message' => 'Entry added successfully']);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
?>