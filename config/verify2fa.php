<?php
session_start();
require 'database.php';
require '2fa.php';
require 'recovery.php';

if (!isset($_SESSION['temp_user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['temp_user_id'];
$db = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['code']);

    // 1. Try normal TOTP
    $stmt = $db->prepare("SELECT twofa_secret, recovery_codes FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($tfa->verifyCode($user['twofa_secret'], $input)) {
        // Normal 2FA success
        finishLogin();
    }

    // 2. Try recovery code
    $hashes = json_decode($user['recovery_codes'] ?? '[]', true);
    if (verifyRecoveryCode($input, $hashes)) {
        // Remove used code
        removeUsedRecoveryCode($input, $hashes);
        $json = json_encode($hashes);

        $stmt = $db->prepare("UPDATE users SET recovery_codes = ? WHERE id = ?");
        $stmt->execute([$json, $userId]);

        // Log event (optional)
        error_log("User $userId used recovery code");

        finishLogin();
    } else {
        $error = "Invalid code or recovery code.";
    }
}

function finishLogin() {
    $_SESSION['user_id'] = $_SESSION['temp_user_id'];
    unset($_SESSION['temp_user_id'], $_SESSION['temp_2fa_secret']);
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>2FA Login</title></head>
<body>
    <h2>Two-Factor Authentication</h2>
    <?php if (isset($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Enter code from app <em>or</em> a recovery code:</label><br>
        <input type="text" name="code" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>