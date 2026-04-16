<?php
$pageTitle = $pageTitle ?? 'KOSERA';
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body<?php echo $bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass) . '"' : ''; ?>>
