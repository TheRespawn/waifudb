<?php
include 'connect.php';

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? '';

$sql = "
  SELECT f.FandomID, f.FanClubName, f.MemberCount, f.Platform, w.Name AS WaifuName
  FROM Fandom f
  LEFT JOIN Waifu w ON f.WaifuID = w.WaifuID
";

if (!empty($search)) {
  $search = $conn->real_escape_string($search);
  $sql .= " WHERE f.FanClubName LIKE '%$search%' OR f.Platform LIKE '%$search%' OR w.Name LIKE '%$search%'";
}

switch ($sort) {
    case 'id_asc':
        $sql .= " ORDER BY f.FandomID ASC";
        break;
    case 'id_desc':
        $sql .= " ORDER BY f.FandomID DESC";
        break;
    case 'members_desc':
        $sql .= " ORDER BY f.MemberCount DESC";
        break;
    case 'members_asc':
        $sql .= " ORDER BY f.MemberCount ASC";
        break;
    default:
        $sql .= " ORDER BY f.FandomID ASC";
        break;
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
  echo "<table><thead>
          <tr><th>ID</th><th>Fan Club</th><th>Members</th><th>Platform</th><th>Waifu</th></tr>
        </thead><tbody>";
  while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['FandomID']}</td>
            <td>{$row['FanClubName']}</td>
            <td>{$row['MemberCount']}</td>
            <td>{$row['Platform']}</td>
            <td>{$row['WaifuName']}</td>
          </tr>";
  }
  echo "</tbody></table>";
} else {
  echo "<p>No results found.</p>";
}

$conn->close();
?>
