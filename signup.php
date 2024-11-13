<?php
include 'config.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $preferredService = htmlspecialchars($_POST['preferredService']);
    $optOut = isset($_POST['optOut']) ? 1 : 0;

    // Begin transaction to ensure both inserts succeed
    $pdo->beginTransaction();

    try {
        // Insert new member into Member table
        $stmt = $pdo->prepare("INSERT INTO Member (FullName, Email, PreferredService, OptOut) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $preferredService, $optOut]);

        // Get the last inserted member ID
        $memberID = $pdo->lastInsertId();

        // Log the new member sign-up in EmailLog table
        $logMessage = "New member sign-up: " . $fullName;
        $pdo->prepare("INSERT INTO EmailLog (MemberID, Message) VALUES (?, ?)")->execute([$memberID, $logMessage]);

        // Commit transaction if both inserts succeed
        $pdo->commit();

        // Redirect to thank you page
        header('Location: thankyou.php');
        exit;

    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

// Handle Unsubscribe request
if (isset($_POST['unsubscribe'])) {
    $unsubscribeEmail = htmlspecialchars($_POST['unsubscribeEmail']);
    
    // Fetch the member ID by email
    $stmt = $pdo->prepare("SELECT MemberID, FullName FROM Member WHERE Email = ?");
    $stmt->execute([$unsubscribeEmail]);
    $member = $stmt->fetch();

    if ($member) {
        $memberID = $member['MemberID'];
        $fullName = $member['FullName'];

        // Log the unsubscribe request in the EmailLog table
        $logMessage2 = "Unsubscribe request " . $fullName;
        $pdo->prepare("INSERT INTO EmailLog (MemberID, Message) VALUES (?, ?)")->execute([$memberID, $logMessage2]);

        echo "<div class='alert alert-info text-center'>Unsubscribe request received. Admin will review and remove you from our records shortly.</div>";
    } else {
        echo "<div class='alert alert-warning text-center'>Email not found in our records. Please check and try again.</div>";
    }// Redirect to thank you page
    header('Location: thankyouUns.php');
    exit;

}
     
?>

<!-- Sign-Up Form -->
<div style="text-align: center;" class="container mt-5">
    <h2>Sign Up for Acme Gallery Membership</h2>
</div>
<br>
<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 50vh;">
    <form method="POST">
        <div class="form-group">
            <label for="fullName">Full Name</label>
            <input type="text" name="fullName" id="fullName" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="preferredService">Preferred Service</label>
            <select name="preferredService" id="preferredService" class="form-control">
                <option value="Newsletter">Newsletter</option>
                <option value="Exhibition Invites">Exhibition Invites</option>
                <option value="Workshops">Workshops</option>
            </select>
        </div>
       
        <div style="text-align: center;" class="container mt-5">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>

<!-- Unsubscribe Form -->
<div style="text-align: center;" class="container mt-5">
    <h2>Unsubscribe from Acme Gallery Communications</h2>
    <p>If you no longer wish to receive communications, please enter your email to request removal.</p>
</div>
<br>
<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: 50vh;">
    <form method="POST">
        <div class="form-group">
            <label for="unsubscribeEmail">Email</label>
            <input type="email" name="unsubscribeEmail" id="unsubscribeEmail" class="form-control" required>
        </div>
        <div style="text-align: center;" class="container mt-5">
            <button type="submit" name="unsubscribe" class="btn btn-danger">Unsubscribe</button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
