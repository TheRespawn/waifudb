<?php
// Include the database connection file
require_once 'connect.php';

// Fetch a random waifu ID between 1 and 4
$result = $conn->query("SELECT WaifuID FROM waifu WHERE WaifuID BETWEEN 1 AND 4 ORDER BY RAND() LIMIT 1");
$waifuRow = $result->fetch_assoc();
$waifuId = $waifuRow['WaifuID'];

// Fetch waifu details
$waifuStmt = $conn->prepare("SELECT * FROM waifu WHERE WaifuID = ?");
$waifuStmt->bind_param("i", $waifuId);
$waifuStmt->execute();
$waifuResult = $waifuStmt->get_result();
$waifu = $waifuResult->fetch_assoc();

// Fetch related media
$mediaStmt = $conn->prepare("SELECT * FROM media WHERE MediaID = ?");
$mediaStmt->bind_param("i", $waifu['MediaID']);
$mediaStmt->execute();
$mediaResult = $mediaStmt->get_result();
$media = $mediaResult->fetch_assoc();

// Fetch related studio
$studioStmt = $conn->prepare("SELECT * FROM studio WHERE StudioID = ?");
$studioStmt->bind_param("i", $media['StudioID']);
$studioStmt->execute();
$studioResult = $studioStmt->get_result();
$studio = $studioResult->fetch_assoc();

// Fetch related voice actor
$voiceStmt = $conn->prepare("SELECT * FROM voiceactor WHERE VoiceActorID = ?");
$voiceStmt->bind_param("i", $waifu['VoiceActorID']);
$voiceStmt->execute();
$voiceResult = $voiceStmt->get_result();
$voice = $voiceResult->fetch_assoc();

// Fetch related fandoms
$fandomStmt = $conn->prepare("SELECT * FROM fandom WHERE WaifuID = ?");
$fandomStmt->bind_param("i", $waifuId);
$fandomStmt->execute();
$fandomResult = $fandomStmt->get_result();
$fandoms = $fandomResult->fetch_all(MYSQLI_ASSOC);

// Fetch related abilities
$abilityStmt = $conn->prepare("SELECT * FROM abilities WHERE WaifuID = ?");
$abilityStmt->bind_param("i", $waifuId);
$abilityStmt->execute();
$abilityResult = $abilityStmt->get_result();
$abilities = $abilityResult->fetch_all(MYSQLI_ASSOC);

// Fetch related events
$eventStmt = $conn->prepare("SELECT e.* FROM events e JOIN mediaevents me ON e.EventID = me.EventID WHERE me.MediaID = ?");
$eventStmt->bind_param("i", $waifu['MediaID']);
$eventStmt->execute();
$eventResult = $eventStmt->get_result();
$events = $eventResult->fetch_all(MYSQLI_ASSOC);

// Fetch related merchandise
$merchStmt = $conn->prepare("SELECT * FROM merchandise WHERE WaifuID = ?");
$merchStmt->bind_param("i", $waifuId);
$merchStmt->execute();
$merchResult = $merchStmt->get_result();
$merchandise = $merchResult->fetch_all(MYSQLI_ASSOC);
?>

<div style="background: white; padding: 1rem; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
    <h2 style="color: #ff66a3; text-align: center;">Waifu of the Day: <?php echo htmlspecialchars($waifu['Name']); ?></h2>
    <div>
        <img src="<?php echo htmlspecialchars($waifu['ImageURL']); ?>" alt="<?php echo htmlspecialchars($waifu['Name']); ?>" style="max-width: 100px; border-radius: 6px;">
        <p><strong>Origin:</strong> <?php echo htmlspecialchars($waifu['Origin']); ?></p>
        <p><strong>Personality:</strong> <?php echo htmlspecialchars($waifu['Personality']); ?></p>
        <p><strong>Appearance:</strong> <?php echo htmlspecialchars($waifu['Appearance']); ?></p>
        <p><strong>Popularity:</strong> <?php echo htmlspecialchars($waifu['Popularity']); ?></p>
    </div>

    <?php if ($media): ?>
    <div style="margin-top: 1rem;">
        <h3 style="color: #ff66a3;">Media</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Title</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Type</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Release Year</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Studio ID</th>
            </tr>
            <tr>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($media['Title']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($media['Type']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($media['ReleaseYear']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($media['StudioID']); ?></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>

    <?php if ($studio): ?>
    <div style="margin-top: 1rem;">
        <h3 style="color: #ff66a3;">Studio</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Name</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Location</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Founded Year</th>
            </tr>
            <tr>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($studio['Name']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($studio['Location']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($studio['FoundedYear']); ?></td>
            </tr>
        </table>
    </div>
    <?php endif; ?>

    <?php if ($voice): ?>
    <div style="margin-top: 1rem;">
        <h3 style="color: #ff66a3;">Voice Actor</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($voice['Name']); ?></p>
        <p><strong>Nationality:</strong> <?php echo htmlspecialchars($voice['Nationality']); ?></p>
        <p><strong>Agency:</strong> <?php echo htmlspecialchars($voice['Agency']); ?></p>
    </div>
    <?php endif; ?>

    <?php if ($fandoms): ?>
    <div style="margin-top: 1rem;">
        <h3 style="color: #ff66a3;">Fandoms</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Fan Club Name</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Member Count</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Platform</th>
            </tr>
            <?php foreach ($fandoms as $f): ?>
            <tr>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($f['FanClubName']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($f['MemberCount']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($f['Platform']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

    <?php if ($abilities): ?>
    <div style="margin-top: 1rem;">
        <h3 style="color: #ff66a3;">Abilities</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Name</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Description</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Type</th>
            </tr>
            <?php foreach ($abilities as $a): ?>
            <tr>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($a['Name']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($a['Description']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($a['Type']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

    <?php if ($events): ?>
    <div style="margin-top: 1rem;">
        <h3 style="color: #ff66a3;">Events</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Name</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Location</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Date</th>
            </tr>
            <?php foreach ($events as $e): ?>
            <tr>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($e['Name']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($e['Location']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($e['Date']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>

    <?php if ($merchandise): ?>
    <div style="margin-top: 1rem;">
        <h3 style="color: #ff66a3;">Merchandise</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Name</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Type</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Price</th>
                <th style="background: #ffccdd; padding: 0.5rem; border-bottom: 1px solid #eee;">Release Date</th>
            </tr>
            <?php foreach ($merchandise as $m): ?>
            <tr>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($m['Name']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($m['Type']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($m['Price']); ?></td>
                <td style="padding: 0.5rem; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($m['ReleaseDate']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>
</div>