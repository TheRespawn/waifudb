<?php
include '../connect.php'; //Db einbinden

//default werte für tab, sort und search festlegen
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'waifu';
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_asc';

// Define queries and table headers per tab
$tabs = [
    'main' => [
        'query' => "SELECT 
                        w.WaifuID, 
                        w.Name, 
                        w.Origin, 
                        w.ImagePath,
                        w.Personality,
                        w.Appearance, 
                        w.Popularity,
                        GROUP_CONCAT(DISTINCT m.Title SEPARATOR ', ') AS MediaTitles, 
                        va.Name AS VoiceActorName
                    FROM Waifu w
                    LEFT JOIN Media m ON w.WaifuID = m.WaifuID
                    LEFT JOIN VoiceActor va ON w.WaifuID = va.WaifuID
                    %s
                    GROUP BY 
                        w.WaifuID, 
                        w.Name, 
                        w.Origin, 
                        w.ImagePath, 
                        w.Personality, 
                        w.Appearance, 
                        w.Popularity, 
                        va.Name
                    ORDER BY %s",
         //nur bei Suche        
        'where' => $search ? "WHERE w.Name LIKE '%$search%' OR w.Origin LIKE '%$search%'" : '',
        //sortiereoptionen
        'orderBy' => [
            'id_asc' => 'w.WaifuID ASC',
            'id_desc' => 'w.WaifuID DESC',
            'name_asc' => 'w.Name ASC',
            'name_desc' => 'w.Name DESC'
        ],
        'headers' => ['ID', 'Name', 'Origin', 'ImagePath', 'Personality', 'Appearance', 'Popularity', 'Media', 'Voice Actor']
    ],

    'waifu' => [
        'query' => "SELECT WaifuID, Name, Origin, ImagePath, Personality, Appearance, Popularity
                    FROM Waifu
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE Name LIKE '%$search%' OR Origin LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc'            => 'WaifuID ASC',
            'id_desc'           => 'WaifuID DESC',
            'popularity_desc'   => 'Popularity DESC',
            'popularity_asc'    => 'Popularity ASC',
            'name_asc'          => 'Name ASC',
            'name_desc'         => 'Name DESC'
        ],
        'headers' => ['ID', 'Name', 'Origin', 'Image', 'Personality', 'Appearance', 'Popularity']
    ],

    'media' => [
        'query' => "SELECT m.MediaID, w.Name AS WaifuName, s.Name AS StudioName, m.Title, m.Type, m.ReleaseYear
                    FROM Media m
                    JOIN Waifu w ON m.WaifuID = w.WaifuID
                    JOIN Studio s ON m.StudioID = s.StudioID
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE m.Title LIKE '%$search%' OR w.Name LIKE '%$search%' OR s.Name LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'm.MediaID ASC',
            'id_desc' => 'm.MediaID DESC',
            'release_desc' => 'm.ReleaseYear DESC',
            'release_asc' => 'm.ReleaseYear ASC'
        ],
        'headers' => ['ID', 'Waifu Name', 'Studio Name', 'Title', 'Type', 'Release Year']
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
            'founded_asc' => 'FoundedYear ASC',
            'name_asc' => 'Name ASC',
            'name_desc' => 'Name DESC'
        ],
        'headers' => ['ID', 'Name', 'Location', 'Founded Year']
    ],

    'voice' => [
        'query' => "SELECT va.VoiceActorID, w.Name AS WaifuName, va.Name, va.Nationality, va.Agency
                    FROM VoiceActor va
                    JOIN Waifu w ON va.WaifuID = w.WaifuID
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE va.Name LIKE '%$search%' OR w.Name LIKE '%$search%' OR va.Nationality LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'va.VoiceActorID ASC',
            'id_desc' => 'va.VoiceActorID DESC',
            'name_asc' => 'va.Name ASC',
            'name_desc' => 'va.Name DESC'
        ],
        'headers' => ['ID', 'Waifu Name', 'Voice Actor Name', 'Nationality', 'Agency']
    ],

    'fandom' => [
        'query' => "SELECT f.FandomID, w.Name AS WaifuName, f.FanClubName, f.MemberCount, f.Platform
                    FROM Fandom f
                    JOIN Waifu w ON f.WaifuID = w.WaifuID
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE f.FanClubName LIKE '%$search%' OR w.Name LIKE '%$search%' OR f.Platform LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'f.FandomID ASC',
            'id_desc' => 'f.FandomID DESC',
            'members_desc' => 'f.MemberCount DESC',
            'members_asc' => 'f.MemberCount ASC'
        ],
        'headers' => ['ID', 'Waifu Name', 'Fan Club Name', 'Member Count', 'Platform']
    ],

    'abilities' => [
        'query' => "SELECT a.AbilityID, w.Name AS WaifuName, a.Name, a.Description, a.Type
                    FROM Abilities a
                    JOIN Waifu w ON a.WaifuID = w.WaifuID
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE a.Name LIKE '%$search%' OR w.Name LIKE '%$search%' OR a.Description LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'a.AbilityID ASC',
            'id_desc' => 'a.AbilityID DESC',
            'type_asc' => 'a.Type ASC',
            'name_asc' => 'a.Name ASC'
        ],
        'headers' => ['ID', 'Waifu Name', 'Ability Name', 'Description', 'Type']
    ],

    'merchandise' => [
        'query' => "SELECT m.MerchID, w.Name AS WaifuName, m.Name, m.Type, m.Price, m.ReleaseDate
                    FROM Merchandise m
                    JOIN Waifu w ON m.WaifuID = w.WaifuID
                    %s
                    ORDER BY %s",
        'where' => $search ? "WHERE m.Name LIKE '%$search%' OR w.Name LIKE '%$search%' OR m.Type LIKE '%$search%'" : '',
        'orderBy' => [
            'id_asc' => 'm.MerchID ASC',
            'id_desc' => 'm.MerchID DESC',
            'price_desc' => 'm.Price DESC',
            'price_asc' => 'm.Price ASC',
            'release_desc' => 'm.ReleaseDate DESC',
            'release_asc' => 'm.ReleaseDate ASC'
        ],
        'headers' => ['Merch ID', 'Waifu Name', 'Merch Name', 'Type', 'Price', 'Release Date']
    ],
    //alle details, kein filder, random id 
    'waifu-of-the-day' => [
        'query' => "SELECT 
                        w.Name, w.Origin, w.ImagePath, w.Personality, w.Appearance, w.Popularity,
                        GROUP_CONCAT(DISTINCT m.Title SEPARATOR ', ') AS MediaTitles,
                        GROUP_CONCAT(DISTINCT s.Name SEPARATOR ', ') AS StudioNames,
                        GROUP_CONCAT(DISTINCT va.Name SEPARATOR ', ') AS VoiceActorNames,
                        GROUP_CONCAT(DISTINCT f.FanClubName SEPARATOR ', ') AS FanClubNames,
                        GROUP_CONCAT(DISTINCT f.MemberCount SEPARATOR ', ') AS FanClubMemberCounts,
                        GROUP_CONCAT(DISTINCT f.Platform SEPARATOR ', ') AS FanClubPlatforms,
                        GROUP_CONCAT(DISTINCT a.Name SEPARATOR ', ') AS AbilityNames,
                        GROUP_CONCAT(DISTINCT a.Description SEPARATOR '; ') AS AbilityDescriptions,
                        GROUP_CONCAT(DISTINCT a.Type SEPARATOR ', ') AS AbilityTypes,
                        GROUP_CONCAT(DISTINCT merch.Name SEPARATOR ', ') AS MerchNames,
                        GROUP_CONCAT(DISTINCT merch.Type SEPARATOR ', ') AS MerchTypes,
                        GROUP_CONCAT(DISTINCT merch.Price SEPARATOR ', ') AS MerchPrices,
                        GROUP_CONCAT(DISTINCT merch.ReleaseDate SEPARATOR ', ') AS MerchReleaseDates
                    FROM Waifu w
                    LEFT JOIN Media m ON w.WaifuID = m.WaifuID
                    LEFT JOIN Studio s ON m.StudioID = s.StudioID
                    LEFT JOIN VoiceActor va ON w.WaifuID = va.WaifuID
                    LEFT JOIN Fandom f ON w.WaifuID = f.WaifuID
                    LEFT JOIN Abilities a ON w.WaifuID = a.WaifuID
                    LEFT JOIN Merchandise merch ON w.WaifuID = merch.WaifuID
                    WHERE w.ImagePath IS NOT NULL AND w.ImagePath != ''
                    GROUP BY w.WaifuID, w.Name, w.Origin, w.ImagePath, w.Personality, w.Appearance, w.Popularity
                    ORDER BY RAND()
                    LIMIT 1",
        'where' => '',
        'orderBy' => ['' => '']
    ]
];

// error handling
if (!isset($tabs[$tab])) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'Invalid tab specified.']);
    exit;
}

// Get orderBy
$currentTabOrderByOptions = $tabs[$tab]['orderBy'] ?? [];
if (!empty($currentTabOrderByOptions)) {
    $orderBy = isset($currentTabOrderByOptions[$sort]) ? $currentTabOrderByOptions[$sort] : reset($currentTabOrderByOptions);
} else {
    $orderBy = '';
}

// Special handling for waifu-of-the-day
if ($tab === 'waifu-of-the-day') {
    //kein sql query
    $result = $conn->query($tabs[$tab]['query']);
    if (!$result) {
        http_response_code(500);
        echo "<p>Error fetching Waifu of the Day: " . $conn->error . "</p>";
        exit;
    }
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $waifuName = htmlspecialchars($row['Name']);
        $imagePath = (!empty($row['ImagePath'])) ? htmlspecialchars($row['ImagePath']) : '/cards/default-card.png';

        // Embedded CSS for styling
        echo "<style>
            .waifu-of-the-day-details-table {
                width: 100%;
                max-width: 800px;
                margin: 20px auto;
                border-collapse: collapse;
                background-color: #fff;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            .waifu-of-the-day-details-table th, .waifu-of-the-day-details-table td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            .waifu-of-the-day-details-table .section-header {
                background-color: #f0f0f0;
                font-weight: bold;
                font-size: 1.2em;
                border-top: 2px solid #ccc;
                border-bottom: 2px solid #ccc;
            }
            .waifu-of-the-day-details-table .field-row td:first-child {
                padding-left: 30px;
                font-weight: 600;
                color: #333;
            }
            .waifu-of-the-day-details-table .field-row td {
                background-color: #fafafa;
            }
            .waifu-of-the-day-details-table .field-row:nth-child(even) td {
                background-color: #f5f5f5;
            }
            .waifu-of-the-day-header {
                text-align: center;
                font-size: 1.5em;
                margin: 20px 0;
                color: #333;
            }
            .waifu-of-the-day-table-image {
                display: block;
                margin: 0 auto;
                max-width: 300px;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            }
        </style>";

        echo "<div class='waifu-of-the-day-header'>Waifu of the Day: " . $waifuName . "</div>";
        echo "<img src='" . $imagePath . "' alt='" . $waifuName . "' class='waifu-of-the-day-table-image' onerror=\"this.onerror=null;this.src='/cards/default-card.png';\">";
        //waifu of the day output aller Daten
        echo "<table class='waifu-of-the-day-details-table'>";
        
        // Waifu Section
        echo "<tr class='section-header'><td colspan='2'>Waifu</td></tr>";
        echo "<tr class='field-row'><td>Name</td><td>" . $waifuName . "</td></tr>";
        echo "<tr class='field-row'><td>Origin</td><td>" . htmlspecialchars($row['Origin']) . "</td></tr>";
        echo "<tr class='field-row'><td>Personality</td><td>" . htmlspecialchars($row['Personality']) . "</td></tr>";
        echo "<tr class='field-row'><td>Appearance</td><td>" . htmlspecialchars($row['Appearance']) . "</td></tr>";
        echo "<tr class='field-row'><td>Popularity</td><td>" . htmlspecialchars($row['Popularity']) . "</td></tr>";

        // Media Section
        echo "<tr class='section-header'><td colspan='2'>Media</td></tr>";
        echo "<tr class='field-row'><td>Titles</td><td>" . ($row['MediaTitles'] ? htmlspecialchars($row['MediaTitles']) : 'N/A') . "</td></tr>";
        echo "<tr class='field-row'><td>Studios</td><td>" . ($row['StudioNames'] ? htmlspecialchars($row['StudioNames']) : 'N/A') . "</td></tr>";

        // Voice Actor Section
        echo "<tr class='section-header'><td colspan='2'>Voice Actor</td></tr>";
        echo "<tr class='field-row'><td>Name</td><td>" . ($row['VoiceActorNames'] ? htmlspecialchars($row['VoiceActorNames']) : 'N/A') . "</td></tr>";

        // Fandom Section
        echo "<tr class='section-header'><td colspan='2'>Fandom</td></tr>";
        echo "<tr class='field-row'><td>Fan Clubs</td><td>" . ($row['FanClubNames'] ? htmlspecialchars($row['FanClubNames']) : 'N/A') . "</td></tr>";
        echo "<tr class='field-row'><td>Fan Club Members</td><td>" . ($row['FanClubMemberCounts'] ? htmlspecialchars($row['FanClubMemberCounts']) : 'N/A') . "</td></tr>";
        echo "<tr class='field-row'><td>Fan Club Platforms</td><td>" . ($row['FanClubPlatforms'] ? htmlspecialchars($row['FanClubPlatforms']) : 'N/A') . "</td></tr>";

        // Abilities Section
        echo "<tr class='section-header'><td colspan='2'>Abilities</td></tr>";
        echo "<tr class='field-row'><td>Names</td><td>" . ($row['AbilityNames'] ? htmlspecialchars($row['AbilityNames']) : 'N/A') . "</td></tr>";
        echo "<tr class='field-row'><td>Descriptions</td><td>" . ($row['AbilityDescriptions'] ? htmlspecialchars($row['AbilityDescriptions']) : 'N/A') . "</td></tr>";
        echo "<tr class='field-row'><td>Types</td><td>" . ($row['AbilityTypes'] ? htmlspecialchars($row['AbilityTypes']) : 'N/A') . "</td></tr>";

        // Merchandise Section
        echo "<tr class='section-header'><td colspan='2'>Merchandise</td></tr>";
        echo "<tr class='field-row'><td>Names</td><td>" . ($row['MerchNames'] ? htmlspecialchars($row['MerchNames']) : 'N/A') . "</td></tr>";
        echo "<tr class='field-row'><td>Types</td><td>" . ($row['MerchTypes'] ? htmlspecialchars($row['MerchTypes']) : 'N/A') . "</td></tr>";
        echo "<tr class='field-row'><td>Prices</td><td>" . ($row['MerchPrices'] ? htmlspecialchars($row['MerchPrices']) : 'N/A') . "</td></tr>";
        echo "<tr class='field-row'><td>Release Dates</td><td>" . ($row['MerchReleaseDates'] ? htmlspecialchars($row['MerchReleaseDates']) : 'N/A') . "</td></tr>";

        echo "</table>";
    } else {
        echo "<p>Could not select a Waifu of the Day (perhaps none have images assigned?).</p>";
    }
} else {
    if (!isset($tabs[$tab]['query']) || !isset($tabs[$tab]['where'])) {
        http_response_code(500);
        echo "<p>Internal server error: Tab configuration missing for '{$tab}'.</p>";
        exit;
    }

    $queryFormat = $tabs[$tab]['query'];
    $whereClause = $tabs[$tab]['where'];
    $finalQuery = sprintf($queryFormat, $whereClause, $orderBy);
    
    $result = $conn->query($finalQuery);

    if (!$result) {
        http_response_code(500);
        echo "<p>Error executing query for tab '{$tab}': " . $conn->error . ". Query: " . htmlspecialchars($finalQuery) . "</p>";
        exit;
    }

    echo "<table>";
    echo "<thead><tr>";
    if (isset($tabs[$tab]['headers']) && is_array($tabs[$tab]['headers'])) {
        foreach ($tabs[$tab]['headers'] as $header) {
            echo "<th>" . htmlspecialchars($header) . "</th>";
        }
    }
    echo "</tr></thead>";
    echo "<tbody>";

    if ($tab === 'main') {
        while ($row = $result->fetch_assoc()) {
            //fallback bild
            $imagePath = (!empty($row['ImagePath'])) ? htmlspecialchars($row['ImagePath']) : '/cards/default-card.png';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['WaifuID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Origin']) . "</td>";
            echo "<td>" . $imagePath . "</td>"; 
            echo "<td>" . ($row['Personality'] !== null ? htmlspecialchars($row['Personality']) : 'N/A') . "</td>";
            echo "<td>" . ($row['Appearance'] !== null ? htmlspecialchars($row['Appearance']) : 'N/A') . "</td>";
            echo "<td>" . ($row['Popularity'] !== null ? htmlspecialchars($row['Popularity']) : 'N/A') . "</td>";
            echo "<td>" . ($row['MediaTitles'] !== null ? htmlspecialchars($row['MediaTitles']) : 'N/A') . "</td>";
            echo "<td>" . ($row['VoiceActorName'] !== null ? htmlspecialchars($row['VoiceActorName']) : 'N/A') . "</td>";
            echo "</tr>";
        }
    } elseif ($tab === 'waifu') {
        while ($row = $result->fetch_assoc()) {
            $imagePath = (!empty($row['ImagePath'])) ? htmlspecialchars($row['ImagePath']) : '/cards/default-card.png';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['WaifuID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Origin']) . "</td>";
            echo "<td><img src='" . $imagePath . "' alt='" . htmlspecialchars($row['Name']) . "' class='waifu-table-image' onerror=\"this.onerror=null;this.src='/cards/default-card.png';\"></td>";
            echo "<td>" . ($row['Personality'] !== null ? htmlspecialchars($row['Personality']) : 'N/A') . "</td>";
            echo "<td>" . ($row['Appearance'] !== null ? htmlspecialchars($row['Appearance']) : 'N/A') . "</td>";
            echo "<td>" . ($row['Popularity'] !== null ? htmlspecialchars($row['Popularity']) : 'N/A') . "</td>";
            echo "</tr>";
        }
    } else { 
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . ($value !== null ? htmlspecialchars($value) : 'N/A') . "</td>";
            }
            echo "</tr>";
        }
    }
    echo "</tbody></table>";
}

$conn->close();
?>