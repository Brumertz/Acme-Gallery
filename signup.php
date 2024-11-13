<?php
include 'config.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $preferredService = htmlspecialchars($_POST['preferredService']);
    $optOut = isset($_POST['optOut']) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO Member (FullName, Email, PreferredService, OptOut) VALUES (?, ?, ?, ?)");
    $stmt->execute([$fullName, $email, $preferredService, $optOut]);

    $logMessage = "New member sign-up: " . $fullName;
    $pdo->prepare("INSERT INTO EmailLog (MemberID, Message) VALUES (LAST_INSERT_ID(), ?)")->execute([$logMessage]);

    header('Location: thankyou.php');
    exit;
}
?>

    <div  style="text-align: center;" class="container mt-5">
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
            <div class="form-group form-check">
                <input type="checkbox" name="optOut" id="optOut" class="form-check-input">
                <label for="optOut" class="form-check-label">I would like to opt-out of communications</label>
            </div>
            <div    style="text-align: center;" class="container mt-5">
            <button href="thankyou.php" type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>


<?php include 'includes/footer.php'; ?>
