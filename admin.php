<?php
include 'config.php';
include 'includes/header.php';

// Fetch unique services for the dropdown filter
$services = $pdo->query("SELECT DISTINCT PreferredService FROM Member")->fetchAll(PDO::FETCH_COLUMN);

// Fetch members based on filter if set
$serviceFilter = isset($_GET['serviceFilter']) ? $_GET['serviceFilter'] : '';
$query = "SELECT * FROM Member";
$params = [];

if ($serviceFilter) {
    $query .= " WHERE PreferredService = ?";
    $params[] = $serviceFilter;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['emailToDelete'])) {
    $emailToDelete = $_POST['emailToDelete'];
    $stmt = $pdo->prepare("DELETE FROM Member WHERE Email = ?");
    $stmt->execute([$emailToDelete]);
    header("Location: admin.php");
    exit;
}
?>

<div class="justify-content-center align-items-center" style="min-height: 60vh;">
    <div style="text-align: center;" class="container mt-5">    
        <h2>Admin Panel - Manage Members</h2>
    </div>    
    <br>
    <div class="container mt-5">
        <!-- Filter Form -->
        <form method="GET" class="mb-3">
            <div class="form-group">
                <label for="serviceFilter">Filter by Preferred Service:</label>
                <select name="serviceFilter" id="serviceFilter" class="form-control" onchange="this.form.submit()">
                    <option value="">All Services</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= htmlspecialchars($service) ?>" <?= $serviceFilter === $service ? 'selected' : '' ?>>
                            <?= htmlspecialchars($service) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>

        <!-- Members Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Preferred Service</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td><?= htmlspecialchars($member['FullName']) ?></td>
                        <td><?= htmlspecialchars($member['Email']) ?></td>
                        <td><?= htmlspecialchars($member['PreferredService']) ?></td>
                       
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="emailToDelete" value="<?= htmlspecialchars($member['Email']) ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div> 
</div>
<?php include 'includes/footer.php'; ?>
