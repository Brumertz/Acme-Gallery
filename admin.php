<?php
include 'config.php';
include 'includes/header.php';

// Fetch all members
$stmt = $pdo->query("SELECT * FROM Member");
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['emailToDelete'])) {
    $emailToDelete = $_POST['emailToDelete'];
    $stmt = $pdo->prepare("DELETE FROM Member WHERE Email = ?");
    $stmt->execute([$emailToDelete]);
    header("Location: admin.php");
    exit;
}
?>

<div class="container mt-5">
    <h2>Admin Panel - Manage Members</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Preferred Service</th>
                <th>Opt-Out</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td><?= htmlspecialchars($member['FullName']) ?></td>
                    <td><?= htmlspecialchars($member['Email']) ?></td>
                    <td><?= htmlspecialchars($member['PreferredService']) ?></td>
                    <td><?= $member['OptOut'] ? 'Yes' : 'No' ?></td>
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
<?php include 'includes/footer.php'; ?>
