<?php
// includes/functions.php - robust safe version
// Back up the original before replacing.

function dataFilePath(string $name): string {
    return __DIR__ . '/../data/' . $name . '.json';
}
 

function ensureDataDir(): void {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException("Failed to create data directory: $dir");
        }
    }
}

function readData(string $name): array {
    ensureDataDir();
    $path = dataFilePath($name);
    if (!file_exists($path)) {
        // create empty JSON file if missing
        @file_put_contents($path, json_encode([], JSON_PRETTY_PRINT), LOCK_EX);
        return [];
    }
    $json = @file_get_contents($path);
    if ($json === false) {
        error_log("readData: failed to read $path");
        return [];
    }
    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("readData: invalid JSON in $path: " . json_last_error_msg());
        // return empty array instead of erroring to keep app working
        return [];
    }
    return is_array($data) ? $data : [];
}

function writeData(string $name, array $data): void {
    ensureDataDir();
    $path = dataFilePath($name);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        throw new RuntimeException('writeData: json_encode error: ' . json_last_error_msg());
    }
    // use file_put_contents with LOCK_EX for atomic-ish write (sufficient for demo apps)
    $result = @file_put_contents($path, $json, LOCK_EX);
    if ($result === false) {
        throw new RuntimeException("writeData: failed to write file: $path");
    }
}

function h($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function nextId(array $items): int {
    $max = 0;
    foreach ($items as $it) {
        if (isset($it['id']) && is_numeric($it['id'])) {
            $max = max($max, (int)$it['id']);
        }
    }
    return $max + 1;
}
?>