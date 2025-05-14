<?php
include '../connect.php'; // Adjust path if needed

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'waifu';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_asc';

// Define queries and table headers per tab
$tabs = [
    'main' => [
        'query' => "SELECT w.WaifuID, w.Name, w.Origin, w.Personality, w.Appearance, w.Popularity,
                           GROUP_CONCAT(m.Title) AS MediaTitles, v.Name AS VoiceActorName
                    FROM Waifu w
                    LEFT JOIN Media m ON w.WaifuID = m.WaifuID
                    LEFT JOIN VoiceActor v ON w.WaifuID = v.WaifuID
                    %s
                    GROUP BY w.WaifuID
                    ORDER BY %s",
        'where' => $search ? "WHERE w.Name LIKE '%$search%' OR w.Origin LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'w.WaifuID ASC',
            'id_desc' => 'w.WaifuID DESC',
            'name_asc' => 'w.Name ASC',
            'name_desc' => 'w.Name DESC'
        ],
        'headers' => ['ID', 'Name', 'Origin', 'Personality', 'Appearance', 'Popularity', 'Media', 'Voice Actor']
    ],
    'waifu' => [
        'query' => "SELECT WaifuID, Name, Origin, Personality, Appearance, Popularity
                    FROM Waifu
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE Name LIKE '%$search%' OR Origin LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'WaifuID ASC',
            'id_desc' => 'WaifuID DESC',
            'popularity_desc' => 'Popularity DESC',
            'popularity_asc' => 'Popularity ASC'
        ],
        'headers' => ['ID', 'Name', 'Origin', 'Personality', 'Appearance', 'Popularity']
    ],
    'media' => [
        'query' => "SELECT MediaID, WaifuID, StudioID, Title, Type, ReleaseYear
                    FROM Media
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE Title LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'MediaID ASC',
            'id_desc' => 'MediaID DESC',
            'release_desc' => 'ReleaseYear DESC',
            'release_asc' => 'ReleaseYear ASC'
        ],
        'headers' => ['ID', 'Waifu ID', 'Studio ID', 'Title', 'Type', 'Release Year']
    ],
    'studio' => [
        'query' => "SELECT StudioID, Name, Location, FoundedYear
                    FROM Studio
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE Name LIKE '%$search%' OR Location LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'StudioID ASC',
            'id_desc' => 'StudioID DESC',
            'founded_desc' => 'FoundedYear DESC',
            'founded_asc' => 'FoundedYear ASC'
        ],
        'headers' => ['ID', 'Name', 'Location', 'Founded Year']
    ],
    'voice' => [
        'query' => "SELECT VoiceActorID, WaifuID, Name, Nationality, Agency
                    FROM VoiceActor
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE Name LIKE '%$search%' OR Nationality LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'VoiceActorID ASC',
            'id_desc' => 'VoiceActorID DESC'
        ],
        'headers' => ['ID', 'Waifu ID', 'Name', 'Nationality', 'Agency']
    ],
    'fandom' => [
        'query' => "SELECT FandomID, WaifuID, FanClubName, MemberCount, Platform
                    FROM Fandom
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE FanClubName LIKE '%$search%' OR Platform LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'FandomID ASC',
            'id_desc' => 'FandomID DESC',
            'members_desc' => 'MemberCount DESC',
            'members_asc' => 'MemberCount ASC'
        ],
        'headers' => ['ID', 'Waifu ID', 'Fan Club Name', 'Member Count', 'Platform']
    ],
    'abilities' => [
        'query' => "SELECT AbilityID, WaifuID, Name, Description, Type
                    FROM Abilities
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE Name LIKE '%$search%' OR Description LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'AbilityID ASC',
            'id_desc' => 'AbilityID DESC',
            'type' => 'Type ASC',
            'name' => 'Name ASC'
        ],
        'headers' => ['ID', 'Waifu ID', 'Name', 'Description', 'Type']
    ],
    'merchandise' => [
        'query' => "SELECT MerchandiseID, WaifuID, Name, Type, Price, ReleaseDate
                    FROM Merchandise
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE Name LIKE '%$search%' OR Type LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'MerchandiseID ASC',
            'id_desc' => 'MerchandiseID DESC',
            'price_desc' => 'Price DESC',
            'price_asc' => 'Price ASC',
            'release_desc' => 'ReleaseDate DESC',
            'release_asc' => 'ReleaseDate ASC'
        ],
        'headers' => ['ID', 'Waifu ID', 'Name', 'Type', 'Price', 'Release Date']
    ],
    'waifu-of-the-day' => [
        'query' => "SELECT w.WaifuID, w.Name
                    FROM Waifu w
                    ORDER BY RAND()
                    LIMIT 1",
        'where' => '',
        'orderBy' => ['' => '']
    ]
];

// Validate tab
if (!isset($tabs[$tab])) {
    http_response_code(400);
    echo "<p>Invalid tab</p>";
    exit;
}

// Get orderBy
$orderBy = isset($tabs[$tab]['orderBy'][$sort]) ? $tabs[$tab]['orderBy'][$sort] : reset($tabs[$tab]['orderBy']);

// Special handling for waifu-of-the-day
if ($tab === 'waifu-of-the-day') {
    $result = $conn->query($tabs[$tab]['query']);
    if (!$result) {
        http_response_code(500);
        echo "<p>Error: " . $conn->error . "</p>";
        exit;
    }
    $row = $result->fetch_assoc();
    $waifuID = $row['WaifuID'];
    $waifuName = htmlspecialchars($row['Name']);
    // Map WaifuID to image (simplified, adjust as needed)
    $imageMap = [
        1 => '/images/waifu1.jpg',
        2 => '/images/waifu2.png',
        3 => '/images/waifu3.png',
        4 => '/images/waifu4.jpg',
        5 => '/images/waifu5.webp'
        // Add more mappings as needed
    ];
    $image = isset($imageMap[$waifuID]) ? $imageMap[$waifuID] : '/images/default.jpg';
    echo "<div class='waifu-of-the-day-header'>Waifu of the Day: $waifuName</div>";
    echo "<img src='$image' alt='$waifuName' class='waifu-of-the-day-table-image'>";
} else {
    // Build query
    $query = sprintf($tabs[$tab]['query'], $tabs[$tab]['where'], $orderBy);
    $result = $conn->query($query);

    if (!$result) {
        http_response_code(500);
        echo "<p>Error: " . $conn->error . "</p>";
        exit;
    }

    // Output table
    echo "<table>";
    echo "<thead><tr>";
    foreach ($tabs[$tab]['headers'] as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }
    echo "</tr></thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . ($value !== null ? htmlspecialchars($value) : 'N/A') . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
}

$conn->close();
?>