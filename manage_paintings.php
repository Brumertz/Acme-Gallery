<?php 
include 'config.php';
include 'includes/header.php';

// Handle form submission for adding/updating a painting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $finished = $_POST['finished'];
    $media = $_POST['media'];
    $style = $_POST['style'];
    $artistID = $_POST['artistID'];
    
    // Check if a new image was uploaded
    $image = !empty($_FILES['image']['tmp_name']) ? file_get_contents($_FILES['image']['tmp_name']) : null;

    if (!empty($_POST['paintingID'])) {
        // Update painting
        $paintingID = $_POST['paintingID'];
        if ($image === null) {
            $stmt = $pdo->prepare("UPDATE Painting SET Title = ?, Finished = ?, Media = ?, Style = ?, ArtistID = ? WHERE PaintingID = ?");
            $stmt->execute([$title, $finished, $media, $style, $artistID, $paintingID]);
        } else {
            $stmt = $pdo->prepare("UPDATE Painting SET Title = ?, Finished = ?, Media = ?, Style = ?, Image = ?, ArtistID = ? WHERE PaintingID = ?");
            $stmt->execute([$title, $finished, $media, $style, $image, $artistID, $paintingID]);
        }
    } else {
        // Add new painting
        $stmt = $pdo->prepare("INSERT INTO Painting (Title, Finished, Media, Style, Image, ArtistID) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $finished, $media, $style, $image, $artistID]);
    }

    header("Location: manage_paintings.php");
    exit;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $paintingID = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM Painting WHERE PaintingID = ?");
    $stmt->execute([$paintingID]);
    header("Location: manage_paintings.php");
    exit;
}

// Fetch all paintings with artist names
$stmt = $pdo->query("SELECT Painting.*, Artist.ArtistName FROM Painting LEFT JOIN Artist ON Painting.ArtistID = Artist.ArtistID");
$paintings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all artists for dropdown
$artistStmt = $pdo->query("SELECT ArtistID, ArtistName FROM Artist");
$artists = $artistStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- CSS for Responsive Table and Toggle Button -->
<style>
/* Hide "Media" and "Style" columns and action buttons on small screens */
@media (max-width: 768px) {
    .hide-on-small {
        display: none;
    }
    .action-buttons {
        display: none;
    }
}
</style>

<div class="container mt-5">
    <h2>Manage Paintings</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#paintingModal" onclick="openAddModal()">Add Painting</button>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th class="hide-on-small">Finished Year</th>
                    <th class="hide-on-small">Media</th>
                    <th class="hide-on-small">Style</th>
                    <th class="hide-on-small">Artist</th>
                    <th class="action-buttons">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paintings as $painting): ?>
                    <tr onclick="showActions(this)">
                        <td>
                            <?php if (!empty($painting['Image'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($painting['Image']) ?>" alt="<?= htmlspecialchars($painting['Title']) ?>" class="img-fluid" style="max-width: 100px;">
                            <?php else: ?>
                                <span>No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($painting['Title']) ?></td>
                        <td class="hide-on-small"><?= htmlspecialchars($painting['Finished']) ?></td>
                        <td class="hide-on-small"><?= htmlspecialchars($painting['Media']) ?></td>
                        <td class="hide-on-small"><?= htmlspecialchars($painting['Style']) ?></td>
                        <td class="hide-on-small"><?= htmlspecialchars($painting['ArtistName']) ?></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm edit-btn" 
                                    data-id="<?= $painting['PaintingID'] ?>" 
                                    data-title="<?= htmlspecialchars($painting['Title']) ?>" 
                                    data-finished="<?= htmlspecialchars($painting['Finished']) ?>" 
                                    data-media="<?= htmlspecialchars($painting['Media']) ?>" 
                                    data-style="<?= htmlspecialchars($painting['Style']) ?>" 
                                    data-artist-id="<?= $painting['ArtistID'] ?>">
                                Edit
                            </button>
                            <a href="manage_paintings.php?delete=<?= $painting['PaintingID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this painting?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Add/Edit Painting -->
<div class="modal fade" id="paintingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="manage_paintings.php" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Painting</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="paintingID" id="paintingID">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="finished">Finished Year</label>
                        <input type="number" name="finished" id="finished" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="media">Media</label>
                        <input type="text" name="media" id="media" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="style">Style</label>
                        <input type="text" name="style" id="style" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="artistID">Artist</label>
                        <select name="artistID" id="artistID" class="form-control">
                            <?php foreach ($artists as $artist): ?>
                                <option value="<?= $artist['ArtistID'] ?>"><?= $artist['ArtistName'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" name="image" id="image" class="form-control-file">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Show action buttons only when clicking a row and in responsive mode
function showActions(row) {
    // Check if the screen width is 768px or less (responsive mode)
    if (window.innerWidth <= 768) {
        const actionButtons = row.querySelector('.action-buttons');
        actionButtons.style.display = actionButtons.style.display === 'table-cell' ? 'none' : 'table-cell';
    }
}

// Open Add Modal
function openAddModal() {
    document.getElementById('modalTitle').textContent = "Add Painting";
    document.getElementById('paintingID').value = '';
    document.getElementById('title').value = '';
    document.getElementById('finished').value = '';
    document.getElementById('media').value = '';
    document.getElementById('style').value = '';
    document.getElementById('artistID').value = '';
    document.getElementById('image').value = ''; // Clear the file input
    $('#paintingModal').modal('show');
}

// Open Edit Modal and populate fields with existing data
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        const paintingID = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');
        const finished = button.getAttribute('data-finished');
        const media = button.getAttribute('data-media');
        const style = button.getAttribute('data-style');
        const artistID = button.getAttribute('data-artist-id');

        document.getElementById('paintingID').value = paintingID;
        document.getElementById('title').value = title;
        document.getElementById('finished').value = finished;
        document.getElementById('media').value = media;
        document.getElementById('style').value = style;
        document.getElementById('artistID').value = artistID;
        
        document.getElementById('modalTitle').textContent = "Edit Painting";
        $('#paintingModal').modal('show');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
