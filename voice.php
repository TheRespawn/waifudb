<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'connect.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "
    SELECT VoiceActorID, Name, Nationality, Agency
    FROM VoiceActor
";

if ($search !== '') {
    $sql .= " WHERE Name LIKE '%$search%' OR Nationality LIKE '%$search%' OR Agency LIKE '%$search%'";
}

$result = $conn->query($sql);

echo "<table>
<thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Nationality</th>
        <th>Agency</th>
    </tr>
</thead>
<tbody>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['VoiceActorID']}</td>
            <td>{$row['Name']}</td>
            <td>{$row['Nationality']}</td>
            <td>{$row['Agency']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No voice actors found.</td></tr>";
}

echo "</tbody></table>";
$conn->close();
?>
