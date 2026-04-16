<?php

if (!function_exists('userUsesLegacySchema')) {
    function userUsesLegacySchema(mysqli $conn): bool
    {
        $columns = [];
        $result = $conn->query('SHOW COLUMNS FROM users');
        if ($result) {
            while ($column = $result->fetch_assoc()) {
                $columns[] = $column['Field'];
            }
        }

        return in_array('nama_panjang', $columns, true)
            && in_array('nomor_telepon', $columns, true)
            && in_array('password', $columns, true);
    }
}

if (!function_exists('findUserByEmail')) {
    function findUserByEmail(mysqli $conn, string $email): ?array
    {
        if (userUsesLegacySchema($conn)) {
            $stmt = $conn->prepare('SELECT id, nama_panjang AS name, email, nomor_telepon AS phone, password AS password_hash FROM users WHERE email = ? LIMIT 1');
        } else {
            $stmt = $conn->prepare('SELECT id, name, email, phone, password_hash FROM users WHERE email = ? LIMIT 1');
        }

        if (!$stmt) {
            error_log('User lookup prepare failed: ' . $conn->error);
            return null;
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc() ?: null;
        $stmt->close();

        return $user;
    }
}

if (!function_exists('userEmailExists')) {
    function userEmailExists(mysqli $conn, string $email): bool
    {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        if (!$stmt) {
            error_log('User exists check prepare failed: ' . $conn->error);
            return false;
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();

        return $exists;
    }
}

if (!function_exists('createUser')) {
    function createUser(mysqli $conn, string $name, string $phone, string $email, string $passwordHash): bool
    {
        if (userUsesLegacySchema($conn)) {
            $stmt = $conn->prepare('INSERT INTO users (nama_panjang, nomor_telepon, email, password) VALUES (?, ?, ?, ?)');
        } else {
            $stmt = $conn->prepare('INSERT INTO users (name, phone, email, password_hash) VALUES (?, ?, ?, ?)');
        }

        if (!$stmt) {
            error_log('User insert prepare failed: ' . $conn->error);
            return false;
        }

        $stmt->bind_param('ssss', $name, $phone, $email, $passwordHash);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
