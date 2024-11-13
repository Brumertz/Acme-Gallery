<?php 
include 'config.php';

$query = "SELECT Painting.PaintingID, Painting.Title, Painting.Finished, Painting.Media, Painting.Style, 
                 Painting.Image, Artist.ArtistID, Artist.ArtistName 
          FROM Painting 
          LEFT JOIN Artist ON Painting.ArtistID = Artist.ArtistID";
$stmt = $pdo->prepare($query);
$stmt->execute();
$paintings = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($paintings as &$painting) {
    $painting['Image'] = base64_encode($painting['Image']);
}

header('Content-Type: application/json');
echo json_encode($paintings);