<?php
    function isUserAdmin($userId) {
        global $Link;
        $isAdmin = $Link->query("SELECT isAdmin FROM user WHERE id='$userId'")->fetch_assoc()['isAdmin'];
        if ($isAdmin) return true;
        return false;
    }

    function doesUserExist($userId) {
        global $Link;
        $result = $Link->query("SELECT * FROM user WHERE id='$userId'")->fetch_assoc();
        if (is_null($result)) return false;
        return true;
    }
?>