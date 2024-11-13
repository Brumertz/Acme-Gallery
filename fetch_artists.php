<?php
include 'config.php';

$query = "SELECT Artist.ArtistID, Artist.ArtistName, Artist.LifeSpan, Artist.Nationality, Artist.Century, Thumbnail 
FROM Artist";
$stmt = $pdo->prepare($query);
$stmt->execute();
$artists = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($artists as &$artist) {
    $artist['Image'] = base64_encode($artist['Image']);
}
// Return the artist data as JSON
header('Content-Type: application/json');
echo json_encode($artists);
?>