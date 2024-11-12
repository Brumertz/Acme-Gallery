<?php
include 'config.php';
include 'includes/header.php';

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$artistFilter = isset($_GET['artist']) ? $_GET['artist'] : '';

// Get all unique artists for the dropdown filter
$artists = $pdo->query("SELECT DISTINCT ArtistName, ArtistID FROM Artist")->fetchAll(PDO::FETCH_ASSOC);

// Build the query to get paintings filtered by artist
$sql = "SELECT Painting.Title, Painting.Finished, Painting.Media, Painting.Style, Painting.Image, Artist.ArtistName 
        FROM Painting 
        LEFT JOIN Artist ON Painting.ArtistID = Artist.ArtistID 
        WHERE Painting.Title LIKE :search";
$params = [':search' => "%$searchQuery%"];

if ($artistFilter) {
    $sql .= " AND Artist.ArtistID = :artist";
    $params[':artist'] = $artistFilter;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$paintings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Filter Paintings by Artist</h2>
    <form method="GET" class="form-inline mb-3">
        <label for="artistFilter" class="mr-2">Select Artist:</label>
        <select name="artist" id="artistFilter" class="form-control mr-2" onchange="this.form.submit()">
            <option value="">All</option>
            <?php foreach ($artists as $artist): ?>
                <option value="<?= $artist['ArtistID'] ?>" <?= $artist['ArtistID'] == $artistFilter ? 'selected' : '' ?>><?= htmlspecialchars($artist['ArtistName']) ?></option>
            <?php endforeach; ?>
        </select>
        
        
    </form>

    <div class="collection">
        <?php foreach ($paintings as $painting): ?>
            <div class="item d-flex mb-4">
                <div class="image">
                    <?php if (!empty($painting['Image'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($painting['Image']) ?>" alt="<?= htmlspecialchars($painting['Title']) ?>" class="img-fluid" style="max-width: 200px;">
                    <?php else: ?>
                        <img src="assets/images/default.jpg" alt="No Image" class="img-fluid" style="max-width: 200px;">
                    <?php endif; ?>
                </div>
                <div class="details ml-4">
                    <p><strong>Painting Title:</strong> <?= htmlspecialchars($painting['Title']) ?></p>
                    <p><strong>Finished:</strong> <?= htmlspecialchars($painting['Finished']) ?></p>
                    <p><strong>Paint Media:</strong> <?= htmlspecialchars($painting['Media']) ?></p>
                    <p><strong>Style:</strong> <?= htmlspecialchars($painting['Style']) ?></p>
                    <p><strong>Artist Name:</strong> <?= htmlspecialchars($painting['ArtistName']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .collection {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .item {
        display: flex;
        align-items: flex-start;
        border: 1px solid #ddd;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: 0.3s;
    }
    .item:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    .image img {
        border-radius: 5px;
    }
    .details {
        flex: 1;
    }
    .details p {
        margin: 0;
        font-size: 1em;
        color: #333;
    }
    .details p strong {
        font-weight: bold;
        color: #000;
    }
</style>

<?php include 'includes/footer.php'; ?>
