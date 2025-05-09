<?php
include 'connect.php';

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "
  SELECT m.MerchID, m.Name, m.Type, m.Price, m.ReleaseDate, w.Name AS WaifuName
  FROM Merchandise m
  LEFT JOIN Waifu w ON m.WaifuID = w.WaifuID
";

if (!empty($search)) {
  $search = $conn->real_escape_string($search);
  $sql .= " WHERE m.Name LIKE '%$search%' OR m.Type LIKE '%$search%' OR w.Name LIKE '%$search%'";
}

switch ($sort) {
    case 'id_asc':
        $sql .= " ORDER BY m.MerchID ASC";
        break;
    case 'id_desc':
        $sql .= " ORDER BY m.MerchID DESC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY m.Price DESC";
        break;
    case 'price_asc':
        $sql .= " ORDER BY m.Price ASC";
        break;
    case 'release_desc':
        $sql .= " ORDER BY m.ReleaseDate DESC";
        break;
    case 'release_asc':
        $sql .= " ORDER BY m.ReleaseDate ASC";
        break;
    default:
        $sql .= " ORDER BY m.MerchID ASC";
        break;
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  echo "<table><thead>
          <tr><th>ID</th><th>Name</th><th>Type</th><th>Price</th><th>Release Date</th><th>Waifu</th></tr>
        </thead><tbody>";
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['MerchID']}</td>
            <td>{$row['Name']}</td>
            <td>{$row['Type']}</td>
            <td>{$row['Price']}</td>
            <td>{$row['ReleaseDate']}</td>
            <td>{$row['WaifuName']}</td>
          </tr>";
  }
  echo "</tbody></table>";
} else {
  echo "<p>No results found.</p>";
}

$conn->close();
?>
