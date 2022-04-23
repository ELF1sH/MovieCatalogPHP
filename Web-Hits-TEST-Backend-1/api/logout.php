<?php
function route_logout($method, $urlList, $requestData) {
    global $Link;
    $userId = validateToken();
    if (!is_null($userId)) {
        $stmt = $Link->prepare("DELETE FROM token WHERE userId=?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        setHTTPStatus("200", ['message' => "OK"]);
    }
}
