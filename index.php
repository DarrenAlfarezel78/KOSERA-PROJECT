<?php
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/ServiceController.php';

$page = trim((string) ($_GET['page'] ?? 'services'));
$segments = array_values(array_filter(explode('/', $page), 'strlen'));
$section = $segments[0] ?? 'services';
$action = $segments[1] ?? '';
$identifier = $segments[2] ?? '';

if ($section === 'auth') {
	if ($action === 'login') {
		appLoginController();
		exit();
	} elseif ($action === 'register') {
		appRegisterController();
		exit();
	} elseif ($action === 'logout') {
		appLogoutController();
		exit();
	}
} elseif ($section === 'services') {
	if ($action === '' || $action === 'list') {
		appIndexController();
		exit();
	} elseif ($action === 'create') {
		appFormController();
		exit();
	} elseif ($action === 'store') {
		appSaveServiceController();
		exit();
	} elseif ($action === 'show' && ctype_digit($identifier)) {
		$_GET['id'] = $identifier;
		appDetailController();
		exit();
	} elseif ($action === 'edit' && ctype_digit($identifier)) {
		$_GET['id'] = $identifier;
		appFormController();
		exit();
	} elseif ($action === 'delete' && ctype_digit($identifier)) {
		$_GET['id'] = $identifier;
		appDeleteServiceController();
		exit();
	} elseif ($action === 'image' && ctype_digit($identifier)) {
		$_GET['id'] = $identifier;
		$_GET['type'] = $segments[3] ?? 'image';
		appImageController();
		exit();
	} else {
		appIndexController();
		exit();
	}
} else {
	header('Location: ' . appUrl('services'));
	exit();
}
