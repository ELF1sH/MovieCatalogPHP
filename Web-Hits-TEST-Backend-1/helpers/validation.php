<?php
    function validatePassword($pass) {
        if (strlen($pass) < 8) {
            return "Password is less than 8 symbols.";
        }
        $pass_array = str_split($pass);
        foreach ($pass_array as $letter) {
            if (!preg_match("/[A-Za-z0-9_]/", $letter)) {
                return "Unavailable symbols in password.";
            }
        }
        return "OK";
    }

    function validateLogin($login) {
        if (strlen($login) < 5) {
            return "Username is less than 5 symbols.";
        }
        $login_array = str_split($login);
        foreach ($login_array as $letter) {
            if (!preg_match("/[A-Za-z0-9_]/", $letter)) {
                return "Unavailable symbols in username.";
            }
        }
        return "OK";
    }

    function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Bad email.";
        }
        return "OK";
    }

    function validateGender($gender) {
        if ($gender != 0 && $gender != 1) {
            return "There is no gender with id $gender.";
        }
        return "OK";
    }

    function validateToken() {
        global $Link;
        $token = substr(getallheaders()['Authorization'], 7);
        if (!empty($token)) {
            $tokenResult = $Link->query("SELECT * FROM token WHERE value='$token'")->fetch_assoc();
            if (is_null($tokenResult)) {
                setHTTPStatus("400", ['message' => "Token does not exist"]);
                return null;
            }
            else {
                $expiryDate = $tokenResult['expiryDate'];
                $expiryDate = DateTime::createFromFormat('Y-m-d H:i:s', $expiryDate);
                $currentDateTime = new DateTime();
                if ($expiryDate < $currentDateTime) {
                    setHTTPStatus("403", ['message' => "Permission denied. Your Authorization token has been expired"]);
                    return null;
                }
                $userId = $tokenResult['userId'];
                return $userId;
            }
        }
        else {
            setHTTPStatus("403", ['message' => "Permission denied. You have no Authorization token"]);
            return null;
        }
    }
?>