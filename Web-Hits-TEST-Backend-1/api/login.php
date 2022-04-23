<?php
    function route_login($method, $urlList, $requestData) {
        global $Link;
        global $TokenValidityPeriod;
        $token = substr(getallheaders()['Authorization'], 7);
        if (empty($token)) {
            $username = $requestData->body->username;
            $password = $requestData->body->password;
            if (is_null($username) || is_null($password)) {
                setHTTPStatus("400", ['message' => "Bad Request. Not all data were provided"]);
            }
            else {
                $resultPassword = validatePassword($password);
                $resultLogin = validateLogin($username);
                if ($resultPassword == "OK" && $resultLogin == "OK") {
                    $password = hash("sha1", $password);
                    $stmt = $Link->prepare("SELECT id FROM user WHERE username=? AND password=?");
                    $stmt->bind_param("ss", $username, $password);
                    $stmt->execute();
                    $loginResult = $stmt->get_result()->fetch_assoc();
                    if (!is_null($loginResult)) {
                        $userId = $loginResult['id'];
                        
                        $token = bin2hex(random_bytes(16));
                        $expiryDate = new DateTime();
                        $expiryDate->modify('+' . $TokenValidityPeriod . ' minute');
                        $expiryDate = $expiryDate->format('Y-m-d H:i:s');

                        $stmt = $Link->prepare("INSERT INTO token(value, userId, expiryDate) VALUES(?, ?, '$expiryDate')");
                        $stmt->bind_param("si", $token, $userId);
                        $stmt->execute();
                        setHTTPStatus("200", ['token' => $token]);
                    }
                    else {
                        setHTTPStatus("400", ['message' => "Bad request. Input data are incorrect"]);
                    }
                }
                else {
                    setHTTPStatus("403", ["message" => $resultPassword == "OK" ? $resultLogin : $resultPassword]);
                }
            }
        }
        else {
            setHTTPStatus("403", ['message' => "Permission denied. You already have Authorization token"]);
        }
    }
?>