
<?php 
include 'config.php';
include 'includes/header.php';

$query = "SELECT Painting.Title, Painting.Finished, Painting.Media, Painting.Style, Painting.Image, Artist.ArtistName 
          FROM Painting 
          LEFT JOIN Artist ON Painting.ArtistID = Artist.ArtistID";
$stmt = $pdo->prepare($query);
$stmt->execute();
$paintings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<div  style="text-align: center;" class="container mt-5">
    <h1>Welcome to Acme Arts Gallery</h1>
    <br>
    <p>Discover our curated selection of artwork and artists.</p>
    <p><a href="signup.php" class="btn btn-primary">Join Us</a></p>
</div>
<div class="container mt-5">
    <h2>Collection</h2>
    <br>
    <div class="row">
        <?php foreach ($paintings as $painting): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="row no-gutters">
                        <div class="col-md-4">
                            <?php if ($painting['Image']): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($painting['Image']) ?>" class="card-img" alt="<?= $painting['Title'] ?>">
                            <?php else: ?>
                                <img src="assets/images/default.jpg" class="card-img" alt="No Image">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title"><?= $painting['Title'] ?></h5>
                                <p class="card-text">Year: <?= $painting['Finished'] ?></p>
                                <p class="card-text">Media: <?= ucfirst($painting['Media']) ?></p>
                                <p class="card-text">Style: <?= $painting['Style'] ?></p>
                                <p class="card-text">Artist: <?= $painting['ArtistName'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<style>

h2{
        text-align: center;
        margin-bottom: 50px;

    }

</style>
<?php include 'includes/footer.php'; ?>