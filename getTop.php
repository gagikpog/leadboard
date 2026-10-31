<?php
    require_once('./utility.php');

    initCrossHeaders();

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        http_response_code(400);
        return;
    }

    $result = [
        'status' => 'error',
        'message' => 'Не удалось получить результат.'
    ];

    $postData = file_get_contents('php://input');
    $data = json_decode($postData, true);

    $game = $data['game'];
    $mode = array_key_exists('mode', $data) ? $data['mode'] : 'default';
    $order = isset($data['order']) ? $data['order'] : 'DESC';
    $limit = isset($data['limit']) && is_numeric($data['limit']) ? $data['limit'] : 10;

    if (!is_string($mode) || $mode === '' || strlen($mode) > 50) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Режим должен быть непустой строкой длиной до 50 байт.'
        ]);
        return;
    }

    if (!in_array($order, ['ASC', 'DESC'], true)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Порядок сортировки должен быть ASC или DESC.'
        ]);
        return;
    }

    $gamesChecked = checkGame($game);

    if ($gamesChecked) {
        $res = getLeaderBoardTyLevel($game, $order, $limit, $mode);
        if ($res !== false) {
            $result['status'] = 'done';
            $result['message'] = 'Результат успешно поучен.';
            $result['data'] = $res;
        }
    } else {
        if (!$game) {
            $result['message'] = 'Укажите игру!';
        } else
        if (!$gamesChecked) {
            $result['message'] = 'Игра указанна неверно!';
        }
    }

    echo json_encode($result);
