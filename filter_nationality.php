<?php
include 'config.php';
include 'includes/header.php';

$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$nationalityFilter = isset($_GET['nationality']) ? $_GET['nationality'] : '';
$periodFilter = isset($_GET['period']) ? $_GET['period'] : '';

$nationalities = $pdo->query("SELECT DISTINCT Nationality FROM Artist")->fetchAll(PDO::FETCH_COLUMN);
$periods = $pdo->query("SELECT DISTINCT Century FROM Artist")->fetchAll(PDO::FETCH_COLUMN);

$sql = "SELECT * FROM Artist WHERE ArtistName LIKE :search";
$params = [':search' => "%$searchQuery%"];

if ($nationalityFilter) {
    $sql .= " AND Nationality = :nationality";
    $params[':nationality'] = $nationalityFilter;
}
if ($periodFilter) {
    $sql .= " AND Century = :period";
    $params[':period'] = $periodFilter;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$artists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container mt-5">
    <h2>Filter Artists by Nationality</h2>
    <form method="GET" class="form-inline mb-3">
        <label for="nationalityFilter" class="mr-2">Select Nationality:</label>
        <select name="nationality" id="nationalityFilter" class="form-control mr-2" onchange="this.form.submit()">
            <option value="">All</option>
            <?php foreach ($nationalities as $nationality): ?>
                <option value="<?= $nationality ?>" <?= $nationality == $nationality ? 'selected' : '' ?>><?= $nationality ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="collection">
        <?php foreach ($artists as $artist): ?>
            <div class="item d-flex mb-4">
                <div class="image">
                    <?php if (!empty($artist['Thumbnail'])): ?>
                        <img src="data:image/jpeg;base64,<?= base64_encode($artist['Thumbnail']) ?>" alt="<?= htmlspecialchars($artist['ArtistName']) ?>" class="img-fluid" style="max-width: 200px;">
                    <?php else: ?>
                        <img src="assets/images/default.jpg" alt="No Image" class="img-fluid" style="max-width: 200px;">
                    <?php endif; ?>
                </div>
                <div class="details ml-4">
                    <p><strong>Artist Name:</strong> <?= htmlspecialchars($artist['ArtistName']) ?></p>
                    <p><strong>Life Span:</strong> <?= htmlspecialchars($artist['LifeSpan']) ?></p>
                    <p><strong>Nationality:</strong> <?= htmlspecialchars($artist['Nationality']) ?></p>
                    <p><strong>Century:</strong> <?= htmlspecialchars($artist['Century']) ?></p>
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