<?php
    require_once('./utility.php');

    initCrossHeaders();

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        http_response_code(400);
        return;
    }

    $postData = file_get_contents('php://input');
    $data = json_decode($postData, true);

    $identifier = $data['identifier'];
    $score = $data['score'];
    $game = $data['game'];
    $meta = isset($data['meta']) ? $data['meta'] : '{}';

    $result = [
        'status' => 'error',
        'message' => 'Не удалось сохранить результат.'
    ];

    $gamesChecked = checkGame($game);

    if ($gamesChecked && $identifier && $score && $meta) {
        if (addData($identifier, $score, $game, $meta)) {
            $result['status'] = 'done';
            $result['message'] = 'Результат успешно сохранен.';
        }
    } else {
        if (!$identifier) {
            $result['message'] = 'Необходимо указать "identifier"';
        } else if (!$score) {
            $result['message'] = 'Укажите количество очков!';
        } else if (!$gamesChecked) {
            $result['message'] = 'Недопустимое имя игры!';
        }
    }

    echo json_encode($result);
