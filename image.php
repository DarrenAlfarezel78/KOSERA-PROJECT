<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id']) || !isset($_GET['type'])) {
    http_response_code(404);
    exit('Not Found');
}

$id = (int) $_GET['id'];
$type = (string) $_GET['type'];

if (!in_array($type, ['image', 'certificate'], true)) {
    http_response_code(400);
    exit('Invalid Type');
}

$conn = getConnection();
$column = $type === 'image' ? 'image' : 'certificate';
$stmt = $conn->prepare("SELECT $column FROM services WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    http_response_code(404);
    exit('Not Found');
}

$row = $result->fetch_assoc();
$imageData = $row[$column] ?? '';

$stmt->close();
$conn->close();

if (!is_string($imageData) || $imageData === '') {
    http_response_code(404);
    exit('Not Found');
}

$mimeTypes = [
    'ffd8ff' => 'image/jpeg',
    '89504e47' => 'image/png'
];

$hex = bin2hex(substr($imageData, 0, 4));
$contentType = 'image/jpeg';

foreach ($mimeTypes as $magic => $mime) {
    if (strpos($hex, $magic) === 0) {
        $contentType = $mime;
        break;
    }
}

header('Content-Type: ' . $contentType);
header('Content-Length: ' . strlen($imageData));
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

echo $imageData;
exit();
