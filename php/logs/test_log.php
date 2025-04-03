<?php
$log_file = __DIR__ . '/logs/upload_lyrics.log';

function test_log() {
    global $log_file;
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - TEST LOG WORKS\n", FILE_APPEND);
}

test_log();
echo "Log test completed. Check the file: upload_lyrics.log";
?>
