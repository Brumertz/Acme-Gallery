<?php 
include 'config.php';
include 'includes/header.php';

// Fetch unique artist names from the database
$query = "SELECT ArtistID, ArtistName FROM artist";
$stmt = $pdo->prepare($query);
$stmt->execute();
$artists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Filter Paintings by Artist</h2>
    <div class="form-group">
        <label for="artistSelect">Select an Artist:</label>
        <select id="artistSelect" class="form-control">
            <option value="">All Artists</option>
            <?php foreach ($artists as $artist): ?>
                <option value="<?= $artist['ArtistID'] ?>"><?= htmlspecialchars($artist['ArtistName']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div id="paintingsContainer" class="row mt-4">
        <!-- Painting cards will be populated here by JavaScript -->
    </div>
</div>

<script src="assets/js/scripts.js"></script>
<?php include 'includes/footer.php'; ?>