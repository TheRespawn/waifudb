<?php
include 'connect.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = $_GET['sort'] ?? '';

$sql = "
  SELECT w.WaifuID, w.Name, w.Origin, w.Personality, w.Popularity, m.Title, v.Name AS VoiceActorName, s.Name AS StudioName, w.ImageURL
  FROM Waifu w
  LEFT JOIN Media m ON w.MediaID = m.MediaID
  LEFT JOIN VoiceActor v ON w.VoiceActorID = v.VoiceActorID
  LEFT JOIN Studio s ON m.StudioID = s.StudioID
";

if (!empty($search)) {
    $sql .= " WHERE w.Name LIKE '%$search%' OR w.Origin LIKE '%$search%' OR w.Personality LIKE '%$search%'";
}

switch ($sort) {
    case 'popularity_desc':
        $sql .= " ORDER BY w.Popularity DESC";
        break;
    case 'popularity_asc':
        $sql .= " ORDER BY w.Popularity ASC";
        break;
    default:
        $sql .= " ORDER BY w.Name ASC";
        break;
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
echo "<table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Origin</th>
            <th>Personality</th>
            <th>Popularity</th>
            <th>Media</th>
            <th>Voice Actor</th>
            <th>Studio</th>
            <th>Image</th>
          </tr>
        </thead>
        <tbody>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['WaifuID']}</td>
            <td>{$row['Name']}</td>
            <td>{$row['Origin']}</td>
            <td>{$row['Personality']}</td>
            <td>{$row['Popularity']}</td>
            <td>" . ($row['Title'] ?? 'N/A') . "</td>
            <td>" . ($row['VoiceActorName'] ?? 'N/A') . "</td>
            <td>" . ($row['StudioName'] ?? 'N/A') . "</td>
            <td><img src='{$row['ImageURL']}' alt='{$row['Name']}' style='height:80px;'></td>
          </tr>";
}

    echo "</tbody></table>";
} else {
    echo "<p>No results found.</p>";
}

$conn->close();
?>
