<?php
include '../connect.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get total number of waifus
$countQuery = "SELECT COUNT(*) as total FROM waifu";
$countResult = $conn->query($countQuery);
$totalWaifus = $countResult->fetch_assoc()['total'];

// Select a random waifu
$randomOffset = rand(0, $totalWaifus - 1);
$sql = "SELECT w.WaifuID, w.Name, w.Origin, w.Personality, w.Appearance, w.Popularity, w.ImageURL, 
               m.Title AS Media, m.Type, m.ReleaseYear, m.StudioID,
               s.Name AS Studio, v.Name AS VoiceActor, v.Nationality, v.Agency,
               GROUP_CONCAT(f.FanClubName) AS FanClubNames,
               GROUP_CONCAT(f.MemberCount) AS MemberCounts,
               GROUP_CONCAT(f.Platform) AS Platforms,
               GROUP_CONCAT(a.Name) AS AbilityNames,
               GROUP_CONCAT(a.Description) AS AbilityDescriptions,
               GROUP_CONCAT(a.Type) AS AbilityTypes
        FROM waifu w
        LEFT JOIN media m ON w.MediaID = m.MediaID
        LEFT JOIN studio s ON m.StudioID = s.StudioID
        LEFT JOIN voiceactor v ON w.VoiceActorID = v.VoiceActorID
        LEFT JOIN fandom f ON w.WaifuID = f.WaifuID
        LEFT JOIN abilities a ON w.WaifuID = a.WaifuID
        GROUP BY w.WaifuID
        LIMIT 1 OFFSET $randomOffset";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<div class='waifu-of-the-day-header' style='text-align: center; font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--h1-color, #ff66a3);'>Waifu of the Day: " . htmlspecialchars($row["Name"]) . "</div>";
    echo "<table>";
    echo "<tr><td colspan='2'><img src='" . htmlspecialchars($row["ImageURL"]) . "' alt='Waifu Image' class='waifu-of-the-day-table-image'></td></tr>";
    echo "<tr><th>Origin</th><td>" . htmlspecialchars($row["Origin"]) . "</td></tr>";
    echo "<tr><th>Personality</th><td>" . htmlspecialchars($row["Personality"]) . "</td></tr>";
    echo "<tr><th>Appearance</th><td>" . htmlspecialchars($row["Appearance"]) . "</td></tr>";
    echo "<tr><th>Popularity</th><td>" . htmlspecialchars($row["Popularity"]) . "</td></tr>";
    echo "<tr><th colspan='2' class='category-header'>Media</th></tr>";
    echo "<tr><td>Type</td><td>" . htmlspecialchars($row["Type"]) . "</td></tr>";
    echo "<tr><td>Release Year</td><td>" . htmlspecialchars($row["ReleaseYear"]) . "</td></tr>";
    echo "<tr><td>Studio ID</td><td>" . htmlspecialchars($row["StudioID"]) . "</td></tr>";
    echo "<tr><th colspan='2' class='category-header'>Studio</th></tr>";
    echo "<tr><td>Name</td><td>" . htmlspecialchars($row["Studio"]) . "</td></tr>";
    echo "<tr><th colspan='2' class='category-header'>Voice Actor</th></tr>";
    echo "<tr><td>Name</td><td>" . htmlspecialchars($row["VoiceActor"]) . "</td></tr>";
    echo "<tr><td>Nationality</td><td>" . htmlspecialchars($row["Nationality"]) . "</td></tr>";
    echo "<tr><td>Agency</td><td>" . htmlspecialchars($row["Agency"]) . "</td></tr>";
    echo "<tr><th colspan='2' class='category-header'>Fandom</th></tr>";
    echo "<tr><td>Fan Club Names</td><td>" . htmlspecialchars($row["FanClubNames"]) . "</td></tr>";
    echo "<tr><td>Member Counts</td><td>" . htmlspecialchars($row["MemberCounts"]) . "</td></tr>";
    echo "<tr><td>Platforms</td><td>" . htmlspecialchars($row["Platforms"]) . "</td></tr>";
    echo "<tr><th colspan='2' class='category-header'>Abilities</th></tr>";
    echo "<tr><td>Names</td><td>" . htmlspecialchars($row["AbilityNames"]) . "</td></tr>";
    echo "<tr><td>Descriptions</td><td>" . htmlspecialchars($row["AbilityDescriptions"]) . "</td></tr>";
    echo "<tr><td>Types</td><td>" . htmlspecialchars($row["AbilityTypes"]) . "</td></tr>";
    echo "</table>";
} else {
    echo "<p>No waifu found.</p>";
}

$conn->close();
?>