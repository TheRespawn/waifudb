<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../connect.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = $_GET['sort'] ?? 'id_asc';

$sql = "
    SELECT w.WaifuID, w.Name, w.Origin, w.Personality, w.Appearance, w.Popularity, 
           m.Title AS MediaTitle, v.Name AS VoiceActorName, w.ImageURL
    FROM waifu w
    LEFT JOIN media m ON w.MediaID = m.MediaID
    LEFT JOIN voiceactor v ON w.VoiceActorID = v.VoiceActorID
";

if ($search !== '') {
    if (is_numeric($search)) {
        $sql .= " WHERE w.WaifuID = $search";
    } else {
        $sql .= " WHERE w.Name LIKE '%$search%' OR w.Origin LIKE '%$search%' OR w.Personality LIKE '%$search%' OR w.Appearance LIKE '%$search%'";
    }
}

switch ($sort) {
    case 'id_asc':
        $sql .= " ORDER BY w.WaifuID ASC";
        break;
    case 'id_desc':
        $sql .= " ORDER BY w.WaifuID DESC";
        break;
    case 'popularity_asc':
        $sql .= " ORDER BY w.Popularity ASC";
        break;
    case 'popularity_desc':
        $sql .= " ORDER BY w.Popularity DESC";
        break;
    default:
        $sql .= " ORDER BY w.WaifuID ASC";
        break;
}

$result = $conn->query($sql);

echo "<table>
<thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Origin</th>
        <th>Personality</th>
        <th>Appearance</th>
        <th>Popularity</th>
        <th>Media</th>
        <th>Voice Actor</th>
        <th>Image</th>
    </tr>
</thead>
<tbody>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $image = $row['ImageURL'] ? "<img src='{$row['ImageURL']}' alt='{$row['Name']}' />" : 'No Image';
        echo "<tr>
            <td>{$row['WaifuID']}</td>
            <td>" . htmlspecialchars($row['Name']) . "</td>
            <td>" . htmlspecialchars($row['Origin']) . "</td>
            <td>" . htmlspecialchars($row['Personality']) . "</td>
            <td>" . htmlspecialchars($row['Appearance']) . "</td>
            <td>" . htmlspecialchars($row['Popularity']) . "</td>
            <td>" . htmlspecialchars($row['MediaTitle'] ?? 'N/A') . "</td>
            <td>" . htmlspecialchars($row['VoiceActorName'] ?? 'N/A') . "</td>
            <td>$image</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='9'>No waifus found.</td></tr>";
}

echo "</tbody></table>";
$conn->close();
?>