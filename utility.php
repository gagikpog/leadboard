<?php
    require_once('./pass.php');

    function addData($identifier, $score, $game, $meta, $mode = 'default') {
        $authData = getMySQLAuthData();
        $serverName = $authData['serverName'];
        $username = $authData['username'];
        $password = $authData['password'];
        $dbname = $authData['dbname'];

        try {
            $conn = new PDO("mysql:host=$serverName;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "INSERT INTO `board` (`identifier`, `score`, `game`, `mode`, `meta`) VALUES (:identifier, :score, :game, :mode, :meta)";
            $stmt = $conn->prepare($sql);
            $data = array(':identifier' => $identifier, ':score' => $score, ':game' => $game, ':mode' => $mode, ':meta' => $meta);
            return $stmt->execute($data);
        } catch(PDOException $e) {
            // var_dump($e);
            http_response_code(500);
        }
        return false;
    }

    function getLeaderBoardTyLevel($game, $order, $limit, $mode = 'default') {

        $authData = getMySQLAuthData();
        $serverName = $authData['serverName'];
        $username = $authData['username'];
        $password = $authData['password'];
        $dbname = $authData['dbname'];

        try {
            $conn = new PDO("mysql:host=$serverName;dbname=$dbname", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $order = $order === 'ASC' ? 'ASC' : 'DESC';
            $comparison = $order === 'ASC' ? '<' : '>';
            $limit = max(1, (int) $limit);

            $sql = "SELECT `identifier`, `score`, `created`, `meta`
                FROM `board`
                WHERE `game` = :game AND `mode` = :mode
                  AND NOT EXISTS (
                    SELECT 1 FROM `board` better
                    WHERE better.`game` = `board`.`game`
                      AND better.`mode` = `board`.`mode`
                      AND better.`identifier` = `board`.`identifier`
                      AND (
                        better.`score` $comparison `board`.`score`
                        OR (better.`score` = `board`.`score` AND better.`created` < `board`.`created`)
                        OR (better.`score` = `board`.`score` AND better.`created` = `board`.`created`
                            AND better.`id` < `board`.`id`)
                      )
                  )
                ORDER BY `score` $order, `created` ASC, `id` ASC
                LIMIT :limit";

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':game', $game, PDO::PARAM_STR);
            $stmt->bindValue(':mode', $mode, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
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
        if (!is_string($game) || $game === '') {
            return false;
        }

        $authData = getMySQLAuthData();
        $serverName = $authData['serverName'];
        $username = $authData['username'];
        $password = $authData['password'];
        $dbname = $authData['dbname'];

        try {
            $conn = new PDO("mysql:host=$serverName;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare('SELECT 1 FROM `games` WHERE `id` = :game LIMIT 1');
            $stmt->execute([':game' => $game]);

            return $stmt->fetchColumn() !== false;
        } catch (PDOException $e) {
            http_response_code(500);
            return false;
        }
    }
