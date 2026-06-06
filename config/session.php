<?php
$folderSession = __DIR__ . '/../storage/sessions';

if (!is_dir($folderSession)) {
    mkdir($folderSession, 0777, true);
}

if (session_status() === PHP_SESSION_NONE) {
    session_save_path($folderSession);
}
?>
