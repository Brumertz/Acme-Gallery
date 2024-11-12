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
    $image = !empty($_FILES['image']['tmp_name']) ? file_get_contents($_FILES['image']['tmp_name']) : null;

    if (isset($_POST['action']) && $_POST['action'] === 'edit' && !empty($_POST['paintingID'])) {
        $paintingID = $_POST['paintingID'];
        $stmt = $pdo->prepare("UPDATE Painting SET Title = ?, Finished = ?, Media = ?, Style = ?, Image = ?, ArtistID = ? WHERE PaintingID = ?");
        $stmt->execute([$title, $finished, $media, $style, $image, $artistID, $paintingID]);
    } elseif (isset($_POST['action']) && $_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO Painting (Title, Finished, Media, Style, Image, ArtistID) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $finished, $media, $style, $image, $artistID]);
    }

    header("Location: manage_paintings.php?updated=" . time());
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
@media (max-width: 768px) {
    /* Hide "Media" and "Style" columns and action buttons on small screens */
    .hide-on-small {
        display: none;
    }

    .action-buttons {
        display: none;
    }
}

/* Toggle button styling for small screens */
.table-toggle-button {
    display: none;
    cursor: pointer;
}

@media (max-width: 768px) {
    .table-toggle-button {
        display: inline-block;
        margin-bottom: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 5px 10px;
        font-size: 16px;
        border-radius: 5px;
    }

    .table-toggle-button .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%280, 0, 0, 0.5%29' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        width: 20px;
        height: 20px;
        display: inline-block;
    }
}

/* Show hidden columns and action buttons when .show is added */
.show .hide-on-small,
.show .action-buttons {
    display: table-cell;
}
</style>

<div class="container mt-5">
    <h2>Manage Paintings</h2>
    <button class="btn btn-success mb-3" onclick="openAddModal()">Add Painting</button>

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
                            <button class="btn btn-success btn-sm" onclick="event.stopPropagation(); openEditModal(<?= htmlspecialchars(json_encode($painting)) ?>)">Edit</button>
                            <a href="manage_paintings.php?delete=<?= $painting['PaintingID'] ?>" class="btn btn-danger btn-sm" onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this painting?');">Delete</a>
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
                    <input type="hidden" name="action" id="action" value="add">
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


// Show action buttons only when clicking a row
function showActions(row) {
    const actionButtons = row.querySelector('.action-buttons');
    actionButtons.style.display = actionButtons.style.display === 'table-cell' ? 'none' : 'table-cell';
}

// Open Add Modal
function openAddModal() {
    document.getElementById('modalTitle').textContent = "Add Painting";
    document.getElementById('action').value = "add";
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
function openEditModal(painting) {
    document.getElementById('modalTitle').textContent = "Edit Painting";
    document.getElementById('action').value = "edit";
    document.getElementById('paintingID').value = painting.PaintingID;
    document.getElementById('title').value = painting.Title;
    document.getElementById('finished').value = painting.Finished;
    document.getElementById('media').value = painting.Media;
    document.getElementById('style').value = painting.Style;
    document.getElementById('artistID').value = painting.ArtistID;
    document.getElementById('image').value = ''; // Clear the file input
    $('#paintingModal').modal('show');
}
</script>

<?php include 'includes/footer.php'; ?>
