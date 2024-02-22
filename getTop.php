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
    $order = isset($data['order']) ? $data['order'] : 'DESC';
    $limit = isset($data['limit']) && is_numeric($data['limit']) ? $data['limit'] : 10;

    $gamesChecked = checkGame($game);

    if ($gamesChecked) {
        $res = getLeaderBoardTyLevel($game, $order, $limit);
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
