<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connect.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = $_GET['sort'] ?? '';

$sql = "
    SELECT m.MediaID, m.Title, m.Type, m.ReleaseYear, s.Name AS StudioName
    FROM Media m
    LEFT JOIN Studio s ON m.StudioID = s.StudioID
";

if ($search !== '') {
    $sql .= " WHERE m.Title LIKE '%$search%' OR s.Name LIKE '%$search%' OR m.Type LIKE '%$search%'";
}

switch ($sort) {
    case 'release_asc':
        $sql .= " ORDER BY m.ReleaseYear ASC";
        break;
    case 'release_desc':
        $sql .= " ORDER BY m.ReleaseYear DESC";
        break;
    default:
        $sql .= " ORDER BY m.Title ASC";
        break;
}

$result = $conn->query($sql);

if (isset($_GET['json'])) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

echo "<table>
<thead>
    <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Type</th>
        <th>Release Year</th>
        <th>Studio</th>
    </tr>
</thead>
<tbody>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['MediaID']}</td>
            <td>{$row['Title']}</td>
            <td>{$row['Type']}</td>
            <td>{$row['ReleaseYear']}</td>
            <td>{$row['StudioName']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No media found.</td></tr>";
}

echo "</tbody></table>";
$conn->close();
?>