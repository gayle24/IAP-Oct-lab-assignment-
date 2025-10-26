<?php
session_start();
require 'database.php';
require '2fa.php';
require 'recovery.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$db = getDbConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code']);
    $secret = $_SESSION['temp_2fa_secret'];

    if ($tfa->verifyCode($secret, $code)) {
        $recovery = generateRecoveryCodes();   // 10 codes
        $hashes   = array_column($recovery, 'hash');
        $json     = json_encode($hashes);

        $stmt = $db->prepare("
            UPDATE users 
            SET twofa_secret = ?, recovery_codes = ? 
            WHERE id = ?
        ");
        $stmt->execute([$secret, $json, $userId]);

        // Store plain codes in session to show once
        $_SESSION['show_recovery_codes'] = array_column($recovery, 'plain');

        unset($_SESSION['TEMP_2FA_SECRET']);
        header('Location: enable_2fa.php?success=1');
        exit;
    } else {
        $error = "Invalid code. Try again.";
    }
}


if (isset($_GET['success']) && isset($_SESSION['show_recovery_codes'])) {
    $plainCodes = $_SESSION['show_recovery_codes'];
    unset($_SESSION['show_recovery_codes']);
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>2FA Enabled</title></head>
    <body>
        <h2>2FA Enabled Successfully!</h2>
        <p>Save these <strong>recovery codes</strong> in a safe place (password manager, printed copy). 
           Each can be used <strong>once</strong> if you lose your phone.</p>
        <ul>
            <?php foreach ($plainCodes as $c): ?>
                <li><code style="font-size:1.2em;"><?= $c ?></code></li>
            <?php endforeach; ?>
        </ul>
        <p><a href="index.php">Go to Dashboard</a></p>
    </body>
    </html>
    <?php
    exit;
}


$secret = $tfa->createSecret();
$_SESSION['temp_2fa_secret'] = $secret;

$stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$label = 'MyApp:' . $user['username'];
$qrDataUri = $tfa->getQRCodeImageAsDataUri($label, $secret);
?>
<!DOCTYPE html>
<html>
<head><title>Enable 2FA</title></head>
<body>
    <h2>Enable 2FA</h2>
    <p>Scan this QR code with your authenticator app:</p>
    <img src="<?= $qrDataUri ?>" style="width:200px; height:200px;">
    <p>Or enter this secret manually: <strong><?= $secret ?></strong></p>

    <?php if (isset($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Enter the code from your app:</label>
        <input type="text" name="code" required>
        <button type="submit">Verify</button>
    </form>
</body>
</html>