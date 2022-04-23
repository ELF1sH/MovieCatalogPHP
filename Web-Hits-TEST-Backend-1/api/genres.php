<?php
function route($method, $urlList, $requestData) {
    global $Link;
    switch ($method) {
        case 'GET':
            $genresResult = $Link->query("SELECT * FROM genre");
            $genres = [];
            if ($genresResult) {
                if ($genresResult->num_rows > 0) {
                    while($row = $genresResult->fetch_assoc()) {
                        array_push($genres, ['id' => $row['id'], 'name' => $row['name']]);
                    }
                }
                setHTTPStatus("200", $genres);
            }
            else {
                setHTTPStatus("500", ['Error' => $Link->error]);
            }
            break;
        default:
            setHTTPStatus("405", ['message' => "There is no $method method for /genres"]);
            break;
    }
}