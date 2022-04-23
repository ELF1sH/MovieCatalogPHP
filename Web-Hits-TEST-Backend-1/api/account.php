<?php
function route($method, $urlList, $requestData) {
    global $Link;
    switch ($method) {
        case 'POST':
            switch ($urlList[1]) {
                case "register":
                    include_once "register.php";
                    route_register($method, $urlList, $requestData);
                    break;
                case "login":
                    include_once "login.php";
                    route_login($method, $urlList, $requestData);
                    break;
                case "logout":
                    include_once "logout.php";
                    route_logout($method, $urlList, $requestData);
                    break;
                default:
                    setHTTPStatus("400", ['message' => 'Bad request. Check URL']);
                    break;
            }
            break;

        case 'GET':
            $userId = validateToken();
            if ($userId) {
                if ($urlList[1] != 'profile' || !$urlList[2]) {
                    setHTTPStatus("400", ['message' => 'Bad request. Check URL']);
                    exit;
                }
                if (!is_numeric($urlList[2])) {
                    setHTTPStatus("400", ['message' => "userId must be a number"]);
                    exit;
                }
                if (isUserAdmin($userId) || $userId == $urlList[2]) {
                    $stmt = $Link->prepare("SELECT * FROM user WHERE id=?");
                    $stmt->bind_param("i", $urlList[2]);
                    $stmt->execute();
                    $userResult = $stmt->get_result()->fetch_assoc();
                    if (is_null($userResult)) {
                        setHTTPStatus("409", ['message' => "There is no user with ID '$urlList[2]'"]);
                    }
                    else {
                        unset($userResult['password']);
                        unset($userResult['isAdmin']);
                        setHTTPStatus("200", $userResult);
                    }
                }
                else {
                    setHTTPStatus("403", ['message' => "Forbidden"]);
                }
            }
            break;

        case 'PUT':
            $userId = validateToken();
            if ($userId) {
                if ($urlList[1] != 'profile' || !$urlList[2]) {
                    setHTTPStatus("400", ['message' => 'Bad request. Check URL']);
                    exit;
                }
                if (!is_numeric($urlList[2])) {
                    setHTTPStatus("400", ['message' => "userId must be a number"]);
                    exit;
                }
                if (isUserAdmin($userId) || $userId == $urlList[2]) {
                    if (!doesUserExist($urlList[2])) {
                        setHTTPStatus("409", ['message' => "There is no user with ID $urlList[2]"]);
                        exit;
                    }

                    $username = $requestData->body->username;
                    $email = $requestData->body->email;
                    $name = $requestData->body->name;
                    $birthDate = $requestData->body->birthDate;
                    $gender = $requestData->body->gender;
                    if (is_null($username) || is_null($email) || is_null($name)) {
                        setHTTPStatus("400", ['message' => 'Not all data were provided']);
                    }

                    $mes = validateLogin($username);
                    if ($mes != "OK") {
                        setHTTPStatus("400", ['message' => $mes]);
                        exit;
                    }
                    $mes = validateEmail($email);
                    if ($mes != "OK") {
                        setHTTPStatus("400", ['message' => $mes]);
                        exit;
                    }
                    if (!is_null($gender)) {
                        $mes = validateGender($gender);
                        if ($mes != "OK") {
                            setHTTPStatus("400", ['message' => $mes]);
                            exit;
                        }
                    }

                    $SQL_query = "UPDATE user SET username=?, email=?, name=?";
                    $props = [];
                    $propsTypes = "sss";
                    if ($birthDate) {
                        $SQL_query = $SQL_query . ", birthDate=?";
                        array_push($props, $birthDate);
                        $propsTypes = $propsTypes . "s";
                    }
                    if (!is_null($gender)) {
                        $SQL_query = $SQL_query . ", gender=?";
                        array_push($props, $gender);
                        $propsTypes = $propsTypes . "i";
                    }
                    $SQL_query = $SQL_query . " WHERE id=?";
                    array_push($props, $urlList[2]);
                    $propsTypes = $propsTypes . "i";

                    $stmt = $Link->prepare($SQL_query);
                    $stmt->bind_param($propsTypes, $username, $email, $name, ...$props);
                    $res = $stmt->execute();
                    if ($res) {
                        setHTTPStatus("200", ['message' => "OK"]);
                    }
                    else {
                        setHTTPStatus("500", ['message' => $Link->error]);
                    }
                }
                else {
                    setHTTPStatus("403", ['message' => "Forbidden"]);
                }
            }
            break;
        default:
            setHTTPStatus("405", ['message' => "There is no $method method for /account"]);
            break;
    }
}