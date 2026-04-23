<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include __DIR__ . "/../config/db.php";

if (!isset($_GET["game_id"])) {
    echo json_encode(["success" => false, "message" => "Game ID saknas"]);
    exit;
}

$game_id = (int)$_GET["game_id"];

$sql = "SELECT * FROM games WHERE id = $game_id";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Spelet finns inte"]);
    exit;
}

$game = $result->fetch_assoc();

if ((int)$game["is_ai"] !== 1) {
    echo json_encode(["success" => false, "message" => "Detta är inte ett AI-spel"]);
    exit;
}

if ($game["winner"] !== null) {
    echo json_encode(["success" => false, "message" => "Spelet är redan slut"]);
    exit;
}

if ((int)$game["current_player"] !== 2) {
    echo json_encode(["success" => false, "message" => "Det är inte AI:s tur"]);
    exit;
}

if ($game["ai_move_at"] === null) {
    echo json_encode(["success" => false, "message" => "Ingen AI-tid satt"]);
    exit;
}

if (strtotime($game["ai_move_at"]) > time()) {
    echo json_encode(["success" => false, "message" => "AI väntar fortfarande"]);
    exit;
}

$board = json_decode($game["board"], true);

function placePiece(&$board, $col, $player) {
    for ($row = 5; $row >= 0; $row--) {
        if ($board[$row][$col] == 0) {
            $board[$row][$col] = $player;
            return true;
        }
    }
    return false;
}

function canPlace($board, $col) {
    return $col >= 0 && $col <= 6 && $board[0][$col] == 0;
}

function checkWinner($board, $player) {
    for ($row = 0; $row < 6; $row++) {
        for ($col = 0; $col < 7; $col++) {
            if (
                $col + 3 < 7 &&
                $board[$row][$col] == $player &&
                $board[$row][$col + 1] == $player &&
                $board[$row][$col + 2] == $player &&
                $board[$row][$col + 3] == $player
            ) return true;

            if (
                $row + 3 < 6 &&
                $board[$row][$col] == $player &&
                $board[$row + 1][$col] == $player &&
                $board[$row + 2][$col] == $player &&
                $board[$row + 3][$col] == $player
            ) return true;

            if (
                $row + 3 < 6 && $col + 3 < 7 &&
                $board[$row][$col] == $player &&
                $board[$row + 1][$col + 1] == $player &&
                $board[$row + 2][$col + 2] == $player &&
                $board[$row + 3][$col + 3] == $player
            ) return true;

            if (
                $row - 3 >= 0 && $col + 3 < 7 &&
                $board[$row][$col] == $player &&
                $board[$row - 1][$col + 1] == $player &&
                $board[$row - 2][$col + 2] == $player &&
                $board[$row - 3][$col + 3] == $player
            ) return true;
        }
    }

    return false;
}

function simulateMove($board, $col, $player) {
    $copy = $board;
    if (placePiece($copy, $col, $player)) {
        return $copy;
    }
    return null;
}

function countWindowsScore($window, $aiPlayer, $humanPlayer) {
    $aiCount = 0;
    $humanCount = 0;
    $emptyCount = 0;

    foreach ($window as $cell) {
        if ($cell == $aiPlayer) $aiCount++;
        elseif ($cell == $humanPlayer) $humanCount++;
        else $emptyCount++;
    }

    $score = 0;

    if ($aiCount == 4) $score += 100000;
    if ($aiCount == 3 && $emptyCount == 1) $score += 120;
    if ($aiCount == 2 && $emptyCount == 2) $score += 15;

    if ($humanCount == 3 && $emptyCount == 1) $score -= 140;
    if ($humanCount == 2 && $emptyCount == 2) $score -= 12;
    if ($humanCount == 4) $score -= 100000;

    return $score;
}

function evaluateBoard($board, $aiPlayer) {
    $humanPlayer = $aiPlayer == 1 ? 2 : 1;
    $score = 0;

    for ($row = 0; $row < 6; $row++) {
        if ($board[$row][3] == $aiPlayer) $score += 10;
    }

    for ($row = 0; $row < 6; $row++) {
        for ($col = 0; $col < 4; $col++) {
            $window = [$board[$row][$col], $board[$row][$col + 1], $board[$row][$col + 2], $board[$row][$col + 3]];
            $score += countWindowsScore($window, $aiPlayer, $humanPlayer);
        }
    }

    for ($row = 0; $row < 3; $row++) {
        for ($col = 0; $col < 7; $col++) {
            $window = [$board[$row][$col], $board[$row + 1][$col], $board[$row + 2][$col], $board[$row + 3][$col]];
            $score += countWindowsScore($window, $aiPlayer, $humanPlayer);
        }
    }

    for ($row = 0; $row < 3; $row++) {
        for ($col = 0; $col < 4; $col++) {
            $window = [$board[$row][$col], $board[$row + 1][$col + 1], $board[$row + 2][$col + 2], $board[$row + 3][$col + 3]];
            $score += countWindowsScore($window, $aiPlayer, $humanPlayer);
        }
    }

    for ($row = 3; $row < 6; $row++) {
        for ($col = 0; $col < 4; $col++) {
            $window = [$board[$row][$col], $board[$row - 1][$col + 1], $board[$row - 2][$col + 2], $board[$row - 3][$col + 3]];
            $score += countWindowsScore($window, $aiPlayer, $humanPlayer);
        }
    }

    return $score;
}

function getValidColumns($board) {
    $valid = [];
    for ($col = 0; $col < 7; $col++) {
        if (canPlace($board, $col)) $valid[] = $col;
    }
    return $valid;
}

function pickBestAiMove($board, $aiPlayer) {
    $humanPlayer = $aiPlayer == 1 ? 2 : 1;
    $validCols = getValidColumns($board);

    foreach ($validCols as $col) {
        $testBoard = simulateMove($board, $col, $aiPlayer);
        if ($testBoard !== null && checkWinner($testBoard, $aiPlayer)) {
            return $col;
        }
    }

    foreach ($validCols as $col) {
        $testBoard = simulateMove($board, $col, $humanPlayer);
        if ($testBoard !== null && checkWinner($testBoard, $humanPlayer)) {
            return $col;
        }
    }

    $bestScore = -9999999;
    $bestCol = $validCols[0];
    $preferredOrder = [3, 2, 4, 1, 5, 0, 6];

    foreach ($preferredOrder as $col) {
        if (!in_array($col, $validCols)) continue;

        $testBoard = simulateMove($board, $col, $aiPlayer);
        if ($testBoard === null) continue;

        $score = evaluateBoard($testBoard, $aiPlayer);

        $humanCanWinNext = false;
        $nextValidCols = getValidColumns($testBoard);

        foreach ($nextValidCols as $nextCol) {
            $humanBoard = simulateMove($testBoard, $nextCol, $humanPlayer);
            if ($humanBoard !== null && checkWinner($humanBoard, $humanPlayer)) {
                $humanCanWinNext = true;
                break;
            }
        }

        if ($humanCanWinNext) $score -= 5000;

        if ($score > $bestScore) {
            $bestScore = $score;
            $bestCol = $col;
        }
    }

    return $bestCol;
}

$aiCol = pickBestAiMove($board, 2);
placePiece($board, $aiCol, 2);

$winner = null;
$status = "playing";
$current_player = 1;

if (checkWinner($board, 2)) {
    $winner = 2;
    $status = "finished";
}

$boardJson = json_encode($board);

if ($winner !== null) {
    $updateSql = "UPDATE games 
                  SET board = '$boardJson', winner = 2, status = '$status', ai_move_at = NULL
                  WHERE id = $game_id";
} else {
    $updateSql = "UPDATE games 
                  SET board = '$boardJson', current_player = 1, ai_move_at = NULL
                  WHERE id = $game_id";
}

if ($conn->query($updateSql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Något gick fel"]);
}
?>