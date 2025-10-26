<?php
function generateRecoveryCodes(int $count = 10, int $length = 8): array
{
    $codes = [];
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    for ($i = 0; $i < $count; $i++) {
        $plain = '';
        for ($j = 0; $j < $length; $j++) {
            $plain .= $chars[random_int(0, strlen($chars) - 1)];
        }
        $codes[] = [
            'plain' => $plain,
            'hash'  => password_hash($plain, PASSWORD_BCRYPT)
        ];
    }
    return $codes;
}

function verifyRecoveryCode(string $plain, array $hashedCodes): bool
{
    foreach ($hashedCodes as $hash) {
        if (password_verify($plain, $hash)) {
            return true;
        }
    }
    return false;
}

function removeUsedRecoveryCode(string $plain, array &$hashedCodes): void
{
    foreach ($hashedCodes as $i => $hash) {
        if (password_verify($plain, $hash)) {
            unset($hashedCodes[$i]);
        }
    }
    $hashedCodes = array_values($hashedCodes);
}
?>