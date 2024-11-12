<?php 
include 'config.php';
include 'includes/header.php';

// Handle form submission for adding/updating a painting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: View submitted form data
    var_dump($_POST);
    
    // Collect form data
    $title = $_POST['title'];
    $finished = $_POST['finished'];
    $media = $_POST['media'];
    $style = $_POST['style'];
    $artistID = $_POST['artistID'];
    $image = !empty($_FILES['image']['tmp_name']) ? file_get_contents($_FILES['image']['tmp_name']) : null;

    // Check the action field to determine add or edit
    if (isset($_POST['action']) && $_POST['action'] === 'edit' && !empty($_POST['paintingID'])) {
        // Update painting
        $paintingID = $_POST['paintingID'];
        $stmt = $pdo->prepare("UPDATE Painting SET Title = ?, Finished = ?, Media = ?, Style = ?, Image = ?, ArtistID = ? WHERE PaintingID = ?");
        $stmt->execute([$title, $finished, $media, $style, $image, $artistID, $paintingID]);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'add') {
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

<div class="container mt-5">
    <h2>Manage Paintings</h2>
    <button class="btn btn-success mb-3" onclick="openAddModal()">Add Painting</button>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Finished Year</th>
                <th>Media</th>
                <th>Style</th>
                <th>Artist</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paintings as $painting): ?>
                <tr>
                    <td>
                        <?php if (!empty($painting['Image'])): ?>
                            <img src="data:image/jpeg;base64,<?= base64_encode($painting['Image']) ?>" alt="<?= htmlspecialchars($painting['Title']) ?>" width="100">
                        <?php else: ?>
                            <span>No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($painting['Title']) ?></td>
                    <td><?= htmlspecialchars($painting['Finished']) ?></td>
                    <td><?= htmlspecialchars($painting['Media']) ?></td>
                    <td><?= htmlspecialchars($painting['Style']) ?></td>
                    <td><?= htmlspecialchars($painting['ArtistName']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($painting)) ?>)">Edit</button>
                        <a href="manage_paintings.php?delete=<?= $painting['PaintingID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this painting?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
                    <input type="hidden" name="action" id="action" value="add"> <!-- Action identifier -->
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
// Open Add Modal and set the action to "add"
function openAddModal() {
    document.getElementById('modalTitle').textContent = "Add Painting";
    document.getElementById('action').value = "add";
    document.getElementById('paintingID').value = '';
    document.getElementById('title').value = '';
    document.getElementById('finished').value = '';
    document.getElementById('media').value = '';
    document.getElementById('style').value = '';
    document.getElementById('artistID').value = '';
    $('#paintingModal').modal('show');
}

// Open Edit Modal and populate fields with existing data
function openEditModal(painting) {
    document.getElementById('modalTitle').textContent = "Edit Painting";
    document.getElementById('action').value = "edit";
    document.getElementById('paintingID').value = painting.PaintingID;
    document.getElementById('title').value = painting.Title;
    document.getElementById('finished').value = painting.Finished;
    document.getElementById('media').value = painting.Media;
    document.getElementById('style').value = painting.Style;
    document.getElementById('artistID').value = painting.ArtistID;
    $('#paintingModal').modal('show');
}
</script>

<?php include 'includes/footer.php'; ?>
