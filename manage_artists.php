<?php 
include 'config.php';
include 'includes/header.php';

// Handle form submission for adding/updating an artist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $artistName = $_POST['artistName'];
    $lifeSpan = $_POST['lifeSpan'];
    $nationality = $_POST['nationality'];
    $century = $_POST['century'];
    $thumbnail = !empty($_FILES['thumbnail']['tmp_name']) ? file_get_contents($_FILES['thumbnail']['tmp_name']) : null;

    if (isset($_POST['artistID']) && !empty($_POST['artistID'])) {
        // Update artist
        $artistID = $_POST['artistID'];
        $stmt = $pdo->prepare("UPDATE Artist SET ArtistName = ?, LifeSpan = ?, Nationality = ?, Century = ?, Thumbnail = ? WHERE ArtistID = ?");
        $stmt->execute([$artistName, $lifeSpan, $nationality, $century, $thumbnail, $artistID]);
    } else {
        // Add new artist
        $stmt = $pdo->prepare("INSERT INTO Artist (ArtistName, LifeSpan, Nationality, Century, Thumbnail) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$artistName, $lifeSpan, $nationality, $century, $thumbnail]);
    }

    header("Location: manage_artists.php");
    exit;
}

// Handle deletion
if (isset($_GET['delete'])) {
    $artistID = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM Artist WHERE ArtistID = ?");
    $stmt->execute([$artistID]);
    header("Location: manage_artists.php");
    exit;
}

// Fetch all artists
$stmt = $pdo->query("SELECT * FROM Artist");
$artists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- CSS for Responsive Table and Toggle Button -->
<style>
/* Hide "Life Span", "Nationality", "Century" columns, and action buttons on small screens */
@media (max-width: 768px) {
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
    <h2>Manage Artists</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#artistModal">Add Artist</button>

    <!-- Toggle Button for Small Screens -->
    

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Thumbnail</th>
                    <th>Name</th>
                    <th class="hide-on-small">Life Span</th>
                    <th class="hide-on-small">Nationality</th>
                    <th class="hide-on-small">Century</th>
                    <th class="action-buttons">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($artists as $artist): ?>
                    <tr onclick="showActions(this)">
                        <td>
                            <?php if (!empty($artist['Thumbnail'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($artist['Thumbnail']) ?>" alt="<?= htmlspecialchars($artist['ArtistName']) ?>" class="img-fluid" style="max-width: 100px;">
                            <?php else: ?>
                                <span>No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($artist['ArtistName']) ?></td>
                        <td class="hide-on-small"><?= htmlspecialchars($artist['LifeSpan']) ?></td>
                        <td class="hide-on-small"><?= htmlspecialchars($artist['Nationality']) ?></td>
                        <td class="hide-on-small"><?= htmlspecialchars($artist['Century']) ?></td>
                        <td class="action-buttons">
                            <button class="btn btn-success btn-sm edit-btn" data-id="<?= $artist['ArtistID'] ?>"
                                    data-name="<?= htmlspecialchars($artist['ArtistName']) ?>"
                                    data-lifespan="<?= htmlspecialchars($artist['LifeSpan']) ?>"
                                    data-nationality="<?= htmlspecialchars($artist['Nationality']) ?>"
                                    data-century="<?= htmlspecialchars($artist['Century']) ?>">
                                Edit
                            </button>
                            <a href="manage_artists.php?delete=<?= $artist['ArtistID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this artist?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Add/Edit Artist -->
<div class="modal fade" id="artistModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="manage_artists.php" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Artist</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="artistID" id="artistID">
                    <div class="form-group">
                        <label for="artistName">Artist Name</label>
                        <input type="text" name="artistName" id="artistName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="lifeSpan">Life Span</label>
                        <input type="text" name="lifeSpan" id="lifeSpan" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="nationality">Nationality</label>
                        <input type="text" name="nationality" id="nationality" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="century">Century</label>
                        <input type="text" name="century" id="century" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="thumbnail">Thumbnail</label>
                        <input type="file" name="thumbnail" id="thumbnail" class="form-control-file">
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
// Function to toggle columns for small screens


// Show action buttons only when clicking a row
function showActions(row) {
    const actionButtons = row.querySelector('.action-buttons');
    actionButtons.style.display = actionButtons.style.display === 'table-cell' ? 'none' : 'table-cell';
}

document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
        const artistID = button.getAttribute('data-id');
        const artistName = button.getAttribute('data-name');
        const lifeSpan = button.getAttribute('data-lifespan');
        const nationality = button.getAttribute('data-nationality');
        const century = button.getAttribute('data-century');

        document.getElementById('artistID').value = artistID;
        document.getElementById('artistName').value = artistName;
        document.getElementById('lifeSpan').value = lifeSpan;
        document.getElementById('nationality').value = nationality;
        document.getElementById('century').value = century;
        
        document.getElementById('modalTitle').textContent = "Edit Artist";
        $('#artistModal').modal('show');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
