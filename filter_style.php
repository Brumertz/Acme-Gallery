<?php 
include 'config.php';
include 'includes/header.php';

$query = "SELECT DISTINCT Style FROM painting";
$stmt = $pdo->prepare($query);
$stmt->execute();
$styles = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container mt-5">
    <h2>Filter Paintings by Style</h2>
    <div class="form-group">
        <label for="styleSelect">Select a Style:</label>
        <select id="styleSelect" class="form-control">
            <option value="">All</option>
            <?php foreach ($styles as $style): ?>
                <option value="<?= $style ?>"><?= $style ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div id="paintingsContainer" class="row mt-4">
        <!-- Painting cards will be populated here by JavaScript -->
    </div>
</div>

<script src="assets/js/scripts.js"></script>
<?php include 'includes/footer.php'; ?>