<?php

function avviaSessione () {
    if (session_status() !== PHP_SESSION_ACTIVE)
        session_start();
}

function terminaSessione() {
    //session_regenerate_id();
    //$_SESSION = array();
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time()-30000,
        $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    session_unset();
}

if ($_POST["logout"] === "logout") {
    terminaSessione();
}
else {
    avviaSessione();
}