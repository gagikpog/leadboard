<?php
    require_once('./pass.php');

    function addData($identifier, $score, $game, $meta) {
        $authData = getMySQLAuthData();
        $serverName = $authData['serverName'];
        $username = $authData['username'];
        $password = $authData['password'];
        $dbname = $authData['dbname'];

        try {
            $conn = new PDO("mysql:host=$serverName;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO `board` (`identifier`, `score`, `game`, `meta`) VALUES (:identifier, :score, :game, :meta)";
            $stmt = $conn->prepare($sql);
            $data = array(':identifier' => $identifier, ':score' => $score, ':game' => $game, ':meta' => $meta);
            return $stmt->execute($data);
        } catch(PDOException $e) {
            // var_dump($e);
            http_response_code(500);
        }
        return false;
    }

    function getLeaderBoardTyLevel($game, $order, $limit) {

        $authData = getMySQLAuthData();
        $serverName = $authData['serverName'];
        $username = $authData['username'];
        $password = $authData['password'];
        $dbname = $authData['dbname'];

        try {
            $conn = new PDO("mysql:host=$serverName;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT `identifier`, MAX(score) as score, `created`, `meta` FROM `board` WHERE `game` = '$game' GROUP BY `identifier` ORDER BY `score` DESC LIMIT $limit";
            
            if ($order == 'ASC') {
                $sql = "SELECT `identifier`, MIN(score) as score, `created`, `meta` FROM `board` WHERE `game` = '$game' GROUP BY `identifier` ORDER BY `score` ASC LIMIT $limit";
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();

            $res = [];

            foreach ($data as $row) {
                $res[] = ['identifier' => $row['identifier'], 'score' => $row['score'], 'date' => $row['created'], 'meta' => $row['meta']];
            }

            return $res;

        } catch(PDOException $e) {
            // var_dump($e);
            http_response_code(500);
        }
        return false;
    }

    function initCrossHeaders() {
        header('Content-type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Access-Control-Allow-Headers: *');
    }

    function checkGame($game) {
        $games = ['calendar'];
        return in_array($game, $games);
    }
