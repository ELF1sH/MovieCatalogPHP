<?php
function route($method, $urlList, $requestData) {
    global $Link;
    switch ($method) {
        case 'GET':
            $movieId = $urlList[1];
            if (is_null($movieId)) {
                $moviesRes = $Link->query("SELECT * FROM movie");
                $movies = [];
                if ($moviesRes) {
                    if ($moviesRes->num_rows > 0) {
                        while($row = $moviesRes->fetch_assoc()) {
                            $movieId = $row['id'];
                            $genresRes = $Link->query("SELECT * FROM `movies-genres` WHERE movieId='$movieId'");
                            $genres = [];
                            if ($genresRes->num_rows > 0) {
                                while($rowGenre = $genresRes->fetch_assoc()) {
                                    $genreId = $rowGenre['genreId'];
                                    $genreName = $Link->query("SELECT name FROM genre WHERE id='$genreId'")->fetch_assoc()['name'];
                                    array_push($genres, ['id' => $genreId, 'name' => $genreName]);
                                }
                            }
                            array_push($movies, ['id' => $row['id'], 'name' => $row['name'], 'poster' => $row['poster'], 
                            'year' => $row['year'], 'country' => $row['country'], 'genres' => $genres]);
                        }
                    }
                    setHTTPStatus("200", $movies);
                }
                else {
                    setHTTPStatus("500", ['Error' => $Link->error]);
                }
            }
            else {
                $stmt = $Link->prepare("SELECT * FROM movie WHERE id=?");
                $stmt->bind_param("i", $movieId);
                $res = $stmt->execute();
                if ($res) {
                    $movie = $stmt->get_result()->fetch_assoc();

                    $genresRes = $Link->query("SELECT * FROM `movies-genres` WHERE movieId='$movieId'");
                    $genres = [];
                    if ($genresRes->num_rows > 0) {
                        while($rowGenre = $genresRes->fetch_assoc()) {
                            $genreId = $rowGenre['genreId'];
                            $genreName = $Link->query("SELECT name FROM genre WHERE id='$genreId'")->fetch_assoc()['name'];
                            array_push($genres, ['id' => $genreId, 'name' => $genreName]);
                        }
                    }

                    $movie['genres'] = $genres;
                    setHTTPStatus("200", $movie);
                }
                else {
                    setHTTPStatus("500", ['Error' => $Link->error]);
                }
            }
            break;

        case 'POST':
            $userId = validateToken();
            if ($userId) {
                if (isUserAdmin($userId)) {
                    $name = $requestData->body->name;
                    $year = $requestData->body->year;
                    $country = $requestData->body->country;
                    if (is_null($name) || is_null($year) || is_null($country)) {
                        setHTTPStatus("400", ['message' => "Not all data were provided"]);
                        exit;
                    }
                    $genres = $requestData->body->genres;
                    if (!is_array($genres)) {
                        setHTTPStatus("400", ['message' => 'Property genres must be an array']);
                        exit;
                    }
                    $time = $requestData->body->time;
                    $tagline = $requestData->body->tagline;
                    $director = $requestData->body->country;
                    $budget = $requestData->body->country;
                    $fees = $requestData->body->country;
                    $ageLimit = $requestData->body->country;

                    $SQL_query = "INSERT INTO movie (name, year, country";
                    $props = [$name, $year, $country];
                    $propsTypes = "sis";
                    if (!is_null($time)) {
                        $SQL_query = $SQL_query . ", time";
                        array_push($props, $time);
                        $propsTypes = $propsTypes . "i";
                    }
                    if (!is_null($tagline)) {
                        $SQL_query = $SQL_query . ", tagline";
                        array_push($props, $tagline);
                        $propsTypes = $propsTypes . "s";
                    }
                    if (!is_null($director)) {
                        $SQL_query = $SQL_query . ", director";
                        array_push($props, $director);
                        $propsTypes = $propsTypes . "s";
                    }
                    if (!is_null($budget)) {
                        $SQL_query = $SQL_query . ", budget";
                        array_push($props, $budget);
                        $propsTypes = $propsTypes . "i";
                    }
                    if (!is_null($fees)) {
                        $SQL_query = $SQL_query . ", fees";
                        array_push($props, $fees);
                        $propsTypes = $propsTypes . "i";
                    }
                    if (!is_null($ageLimit)) {
                        $SQL_query = $SQL_query . ", ageLimit";
                        array_push($props, $ageLimit);
                        $propsTypes = $propsTypes . "i";
                    }
                    $SQL_query = $SQL_query . ") VALUES (";
                    for ($i = 0; $i < count($props); $i++) {
                        $i == 0 ? $supple = "?" : $supple = ", ?";
                        $SQL_query = $SQL_query . $supple;
                    }
                    $SQL_query = $SQL_query . ")";

                    $stmt = $Link->prepare($SQL_query);
                    $stmt->bind_param($propsTypes, ...$props);
                    $res = $stmt->execute();
                    if ($res) {
                        $movieId = $Link->query("SELECT MAX(id) FROM movie")->fetch_assoc()['MAX(id)'];
                        $movie = $Link->query("SELECT * FROM movie WHERE id='$movieId'")->fetch_assoc();
                        unset($movie['description'], $movie['tagline'], $movie['director'], $movie['budget']);
                        for ($i = 0; $i < count($genres); $i++) {
                            $stmt = $Link->prepare("INSERT INTO `movies-genres` (movieId, genreId) VALUES (?, ?)");
                            $stmt->bind_param("ii", $movieId, $genres[$i]);
                            $res = $stmt->execute();
                            if (!$res) {
                                setHTTPStatus("500", ['message' => $Link->error]);
                                exit;
                            }
                        }

                        $genresRes = $Link->query("SELECT * FROM `movies-genres` WHERE movieId='$movieId'");
                        $genres = [];
                        if ($genresRes->num_rows > 0) {
                            while($row = $genresRes->fetch_assoc()) {
                                array_push($genres, $row['genreId']);
                            }
                        }
                        $movie['genres'] = $genres;

                        setHTTPStatus("200", $movie);
                    }
                    else {
                        setHTTPStatus("500", ['message' => $Link->error]);
                    }
                }
                else {
                    setHTTPStatus("403", ['message' => "Forbidden. Only admin has access to this method"]);
                }
            }
            break;

        case "DELETE":
            $userId = validateToken();
            if ($userId) {
                if (isUserAdmin($userId)) {
                    $movieId = $urlList[1];
                    if (is_null($movieId)) {
                        setHTTPStatus("400", ['message' => "Bad request. Check URL"]);
                    }
                    else if (!is_numeric($movieId)){
                        setHTTPStatus("400", ['message' => "Bad request. Check URL"]);
                    }
                    else {
                        $stmt = $Link->prepare("DELETE FROM movie WHERE id=?");
                        $stmt->bind_param("i", $movieId);
                        $res = $stmt->execute();
                        if ($res) {
                            setHTTPStatus("200", ['message' => "OK"]);
                        }
                        else {
                            setHTTPStatus("500", ['message' => $Link->error]);
                        }
                    }
                }
                else {
                    setHTTPStatus("403", ['message' => "Only admin has access to this method"]);
                }
            }
            break;

        default:
            setHTTPStatus("405", ['message' => "There is no $method method for /movies"]);
            break;
    }
}