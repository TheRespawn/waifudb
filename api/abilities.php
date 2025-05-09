<?php
include '../connect.php';

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "
  SELECT a.AbilityID, a.Name, a.Type, a.Description, w.Name AS WaifuName
  FROM Abilities a
  LEFT JOIN Waifu w ON a.WaifuID = w.WaifuID
";

if (!empty($search)) {
  $search = $conn->real_escape_string($search);
  $sql .= " WHERE a.Name LIKE '%$search%' OR a.Type LIKE '%$search%' OR w.Name LIKE '%$search%'";
}

switch ($sort) {
    case 'id_asc':
        $sql .= " ORDER BY a.AbilityID ASC";
        break;
    case 'id_desc':
        $sql .= " ORDER BY a.AbilityID DESC";
        break;
    case 'name':
        $sql .= " ORDER BY a.Name ASC";
        break;
    case 'type':
        $sql .= " ORDER BY a.Type ASC";
        break;
    default:
        $sql .= " ORDER BY a.AbilityID ASC";
        break;
}
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  echo "<table><thead>
          <tr><th>ID</th><th>Name</th><th>Type</th><th>Description</th><th>Waifu</th></tr>
        </thead><tbody>";
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['AbilityID']}</td>
            <td>{$row['Name']}</td>
            <td>{$row['Type']}</td>
            <td>{$row['Description']}</td>
            <td>{$row['WaifuName']}</td>
          </tr>";
  }
  echo "</tbody></table>";
} else {
  echo "<p>No results found.</p>";
}

$conn->close();
?>
