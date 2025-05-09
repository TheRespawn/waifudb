<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../connect.php';

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = $_GET['sort'] ?? '';

// Base query
$sql = "
    SELECT StudioID, Name, Location, FoundedYear
    FROM Studio
";

// Search filter
if ($search !== '') {
    $sql .= " WHERE Name LIKE '%$search%' OR Location LIKE '%$search%' OR FoundedYear LIKE '%$search%'";
}

// Sorting
switch ($sort) {
    case 'id_asc':
        $sql .= " ORDER BY StudioID ASC";
        break;
    case 'id_desc':
        $sql .= " ORDER BY StudioID DESC";
        break;
    case 'founded_asc':
        $sql .= " ORDER BY FoundedYear ASC";
        break;
    case 'founded_desc':
        $sql .= " ORDER BY FoundedYear DESC";
        break;
    default:
        $sql .= " ORDER BY StudioID ASC";
        break;
}

$result = $conn->query($sql);

// Output table
echo "<table>
<thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Location</th>
        <th>Founded Year</th>
    </tr>
</thead>
<tbody>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['StudioID']}</td>
            <td>{$row['Name']}</td>
            <td>{$row['Location']}</td>
            <td>{$row['FoundedYear']}</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='4'>No studios found.</td></tr>";
}

echo "</tbody></table>";
$conn->close();
?>
