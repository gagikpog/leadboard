<?php
    require_once(__DIR__ . '/utility.php');

    initCrossHeaders();
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');
        echo json_encode(['status' => 'error', 'message' => 'Используйте GET.']);
        return;
    }

    $authData = getMySQLAuthData();
    $serverName = $authData['serverName'];
    $dbname = $authData['dbname'];
    try {
        $conn = new PDO(
            "mysql:host=$serverName;dbname=$dbname;charset=utf8mb4",
            $authData['username'],
            $authData['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $stmt = $conn->query('SELECT `id`, `name` FROM `games` ORDER BY `name`');
        echo json_encode([
            'status' => 'done',
            'message' => 'Список игр получен.',
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Не удалось получить список игр.']);
    }
