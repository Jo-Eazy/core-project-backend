<!--This is a test to see if errors will run-->
<?php
ini_set('display_errors',1); error_reporting(E_ALL);
$path = __DIR__ . '/data/test_rw.json';
file_put_contents($path, json_encode(['ok'=>true]), LOCK_EX);
echo "wrote\n";
echo file_get_contents($path);
?>