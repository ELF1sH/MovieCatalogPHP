<?php
function route_register($method, $urlList, $requestData) {
    global $Link;
    global $TokenValidityPeriod;
    $token = substr(getallheaders()['Authorization'], 7);
    if (empty($token)) {
        $name = $requestData->body->name;
        $username = $requestData->body->username;
        $password = $requestData->body->password;
        $email = $requestData->body->email;
        $birthDate = $requestData->body->birthDate;
        $gender = $requestData->body->gender;
        if (is_null($name) || is_null($username) || is_null($password) || is_null($email)) {
            setHTTPStatus("400", ['message' => "Bad Request. Not all data was provided"]);
        }
        else {
            $resultPassword = validatePassword($password);
            $resultLogin = validateLogin($username);
            $resultEmail = validateEmail($email);
            $resultGender = validateGender($gender);
            if ($resultPassword == "OK" && $resultLogin == "OK" && $resultEmail == "OK" && $resultGender == "OK") {
                $password = hash("sha1", $password);

                $sqlQuery = "INSERT INTO user(username, name, password, email";
                if ($birthDate) {
                    $sqlQuery = $sqlQuery . ", birthDate";
                }
                if (!is_null($gender)) {
                    $sqlQuery = $sqlQuery . ", gender";
                }
                $sqlQuery = $sqlQuery . ") VALUES('$username', '$name', '$password', '$email'";
                if ($birthDate) {
                    $sqlQuery = $sqlQuery . ", '$birthDate'";
                }
                if (!is_null($gender)) {
                    $sqlQuery = $sqlQuery . ", '$gender'";
                }
                $sqlQuery = $sqlQuery . ")";

                $registerResult = $Link->query($sqlQuery);
                if (!$registerResult) {
                    if ($Link->errno == 1062) {
                        setHTTPStatus("409", ["Error" => "Username '$username' or email '$email' has already been taken"]);
                    }
                    else {
                        setHTTPStatus("400", ["Error" => "Bad Request. " . $Link->error]);
                    }
                }
                else {
                    $token = bin2hex(random_bytes(16));
                    $expiryDate = new DateTime();
                    $expiryDate->modify('+' . $TokenValidityPeriod . ' minute');
                    $expiryDate = $expiryDate->format('Y-m-d H:i:s');

                    $stmt = $Link->prepare("SELECT id FROM user WHERE username=?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $user = $stmt->get_result()->fetch_assoc();
                    $userId = $user['id'];

                    $stmt = $Link->prepare("INSERT INTO token(value, userId, expiryDate) VALUES(?, ?, '$expiryDate')");
                    $stmt->bind_param("si", $token, $userId);
                    $tokenResult = $stmt->execute();

                    if (!$tokenResult) {
                        setHTTPStatus("400", ["message" => "Bad Request. " . $Link->error]);
                    }
                    else {
                        setHTTPStatus("200", ["token" => $token]);
                    }
                }
            }
            else {
                $errorMsg = "";
                if ($resultPassword != "OK") $errorMsg = $errorMsg . " '$resultPassword'";
                if ($resultLogin != "OK") $errorMsg = $errorMsg . " '$resultLogin'";
                if ($resultEmail != "OK") $errorMsg = $errorMsg . " '$resultEmail'";
                if ($resultGender != "OK") $errorMsg = $errorMsg . " '$resultGender'";
                setHTTPStatus("400", ["message" => $errorMsg]);
            }
        }
    }
    else {
        setHTTPStatus("403", ["message" => "Permission denied. You already have Authorization token"]);
    }
}
?>