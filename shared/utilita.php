<?php

error_reporting(E_ERROR);

if ($_SESSION["autenticato"]) {
    $result_saldo = get_saldo();
    if ($result_saldo === false) {
        terminaSessione();
    }
    else {
        $_SESSION["saldo"] = $result_saldo;
    }
}


$noscript = "<noscript>Attenzione, per molte funzioni di questo sito web è richiesto l'utilizzo di Javascript.
            Se disabilitato, invitiamo quindi i nostri utenti a riattivarlo,
            per poter godere a pieno di tutte le funzioni offerte.</noscript>";

function get_saldo() {
    require_once "db_manager.php";
    $con = createConnectionRead();
    if (mysqli_connect_errno()) {
        return false;
    }
    $query = "SELECT usr.saldo FROM usr WHERE usr.nick = ?";
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        mysqli_close($con);
        return false;
    }
    mysqli_stmt_bind_param($stmt, "s", $_SESSION["username"]);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $saldo);

    //Per far funzionare il cnt di righe
    mysqli_stmt_store_result($stmt);
    $righe = mysqli_stmt_num_rows($stmt);

    if ($righe !== 1) {
        mysqli_close($con);
        return false;
    }
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($con);
    return $saldo;
}

function getFormatoSaldo($saldo){
    $len = mb_strlen($saldo);
    if ($len === 1){
        return "0,0".$saldo;
    }
    if ($len === 2){
        return "0,".$saldo;
    }
    return substr($saldo, 0, $len-2).",".substr($saldo, $len-2, 2);
}