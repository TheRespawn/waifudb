<?php
include 'connect.php';

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "SELECT EventID, Name, Location, Date FROM Events";

if (!empty($search)) {
  $search = $conn->real_escape_string($search);
  $sql .= " WHERE Name LIKE '%$search%' OR Location LIKE '%$search%'";
}

switch ($sort) {
  case 'date_desc':
    $sql .= " ORDER BY Date DESC";
    break;
  case 'date_asc':
    $sql .= " ORDER BY Date ASC";
    break;
  default:
    $sql .= " ORDER BY Name ASC";
    break;
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  echo "<table><thead>
          <tr><th>ID</th><th>Name</th><th>Location</th><th>Date</th></tr>
        </thead><tbody>";
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['EventID']}</td>
            <td>{$row['Name']}</td>
            <td>{$row['Location']}</td>
            <td>{$row['Date']}</td>
          </tr>";
  }
  echo "</tbody></table>";
} else {
  echo "<p>No results found.</p>";
}

$conn->close();
?>
