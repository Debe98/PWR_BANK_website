<?php
$formPresenteLog = false;

$sectionResult = "";
if ((!isset($_POST["importo"]) && !isset($_POST["beneficiari"])) || !$_SESSION["autenticato"]);
elseif (!isset($_POST["importo"]) && !isset($_POST["beneficiari"])) {
    $sectionResult = "<h2>Errore nella richiesta:</h2>\n<p>Inserisci entrambi i parametri richiesti</p>\n";
    $sectionResult .= "<p class='pie'>Torna ai pagamenti <a href='paga.php'>QUI</a></p>";
}
else {
    require_once "db_manager.php";
    $con = createConnectionRead();
    if (mysqli_connect_errno()) {
        $sectionResult = "<h2>Errore nel collegamento:</h2>\n<p>Impossibile accedere al DB</p>\n<p>Errore: ".mysqli_connect_error()."</p>";
        mysqli_close($con);
    }
    else {
        $errors_values = check_values($con);
        mysqli_close($con);
        if ($errors_values !== "") {
            $sectionResult = "$errors_values";
        }
        else {
            $con = createConnectionWrite();
            if (mysqli_connect_errno()) {
                $sectionResult = "<h2>Errore nel collegamento:</h2>\n<p>Impossibile accedere al DB</p>\n<p>Errore: ".mysqli_connect_error()."</p>";
            }
            else {
                $sectionResult = esegui_transazione($con);
            }
            if ($con !== false)
                mysqli_close($con);
        }
    }
}


$sectionResearch = "";
if (!$_SESSION["autenticato"]) {
    $sectionResearch = "<h2>Attenzione!</h2>\n<p>L’elenco dei pagamenti è disponibile solo per gli utenti autenticati.</p>";
}
else {
    $formPresenteLog = true;
    $sectionResearch = "<h2>Resoconto</h2>
    <p>Seleziona i pagamenti che vuoi visualizzare.</p>
        <div class='accessoContainer'>
        <div class='accessoForm'>
            <form method='post' action='{$_SERVER["PHP_SELF"]}' name='log' onsubmit='return formChecker(this)' onreset='formReset()'>
                <p class='fieldLabel'>Tipologia:</p>
                <select name='tipologia' class='fieldInput'>
                    <option label='Nessuno' value='none'>
                    <option label='Ricevuti' value='entrata'>
                    <option label='Ordinati' value='uscita'>
                    <option label='Tutti' value='tutti'>
                </select>
                <p class='fieldLabel'>Periodo:</p>
                <select name='periodo' class='fieldInput'>
                    <option label='Nessuno' value='none'>
                    <option label='Ultimo mese' value='1'>
                    <option label='Ultimo trimestre' value='3'>
               </select>
               <div class=\"button-flex-container\">
                    <button type=\"submit\" class=\"subBut\">Cerca</button>
               </div>
            </form>
        </div>
        <div class='avvertenzeForm'>
            <h3>Attenzione!</h3>
            $noscript
            <!--Generati automaticamente:-->
            <!--<div id=\"(field . name)Errore\" class=\"erroreSpecifico\">-->
            <!--    <p class=\"erroreCampo\">(field.Name):</p>-->
            <!--    <div class=\"listaErrori\">...</div>-->
            <!--</div>-->
        </div>
    </div>";
}

$tipologia = $_POST["tipologia"];
$periodo = $_POST["periodo"];
$esito_ricerca = "";
if (isset($tipologia) && isset($periodo)) {
    $error = "";
    if ($tipologia === 'none' || $tipologia === null || $tipologia === "") {
        $error .= "<p>Scegli una tipologia!</p>\n";
    }
    else if ($tipologia !== "entrata" && $tipologia !== "uscita" && $tipologia !== "tutti") {
        $error .= "<p>Tipologia inserita non valida!</p>\n";
    }
    if ($periodo === 'none' || $periodo === null || $periodo === "") {
        $error .= "<p>Scegli un periodo!</p>\n";
    }
    else if ($periodo !== "1" && $periodo !== "3") {
        $error .= "<p>Periodo inserito non valido!</p>\n";
    }

    if ($error !== "") {
        $esito_ricerca .= $error;
    }
    else {
        require_once "db_manager.php";
        $con = createConnectionRead();
        if (mysqli_connect_errno()) {
            $esito_ricerca = "<h2>Errore nel collegamento:</h2>\n<p>Impossibile accedere al DB</p>\n<p>Errore: ".mysqli_connect_error()."</p>";
        }
        else {
            $esito_ricerca = richiedi_log($con, $tipologia, $periodo);
            mysqli_close($con);
        }
    }
}

//FUNZIONI

function check_values($con) {
    $_POST["importo"] = get_amount(trim($_POST["importo"]));
    $importo = $_POST["importo"];
    //Verifica importo
    if (!$importo){
        return "<h2>Errore nella richiesta:</h2>\n<p>L'importo non è in un formato valido.</p>";
    }
    $query = "SELECT usr.saldo FROM usr WHERE usr.nick = ?;";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $_SESSION["username"]);

    if (!$stmt) {
        return "<h2>Errore di connessione:</h2>\n<p>Impossibile preparare la query.</p>";
    }
    else {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $saldo);

        //Per far funzionare il cnt di righe
        mysqli_stmt_store_result($stmt);
        $righe = mysqli_stmt_num_rows($stmt);

        if ($righe !== 1) {
            return "<h2>Spiacenti:</h2>\n<p>Errore nel collegamento con l'account</p>";
        } else {
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            if ( (int) $saldo < $importo) {
                return "<h2>Spiacenti:</h2>\n<p>Il saldo non è sufficiente per eseguire l'operazione (".getFormatoSaldo($saldo)."€ < ".getFormatoSaldo($importo)."€)</p>";
            }
        }
    }
    //Verifica beneficiario
    if ($_POST["beneficiari"] === ""){
        return "<h2>Spiacenti:</h2>\n<p>Non è stato selezionato alcun beneficiario.</p>";
    }
    $query = "SELECT usr.saldo, usr.nome FROM usr WHERE usr.nick = ?;";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $_POST["beneficiari"]);

    if (!$stmt) {
        return "<h2>Errore di connessione:</h2>\n<p>Impossibile preparare la query.</p>";
    }
    else {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $saldo_beneficiario, $nome_beneficiario);

        //Per far funzionare il cnt di righe
        mysqli_stmt_store_result($stmt);
        $righe = mysqli_stmt_num_rows($stmt);
        if ($righe !== 1) {
            return "<h2>Spiacenti:</h2>\n<p>L'utente '{$_POST["beneficiari"]}' non è stato trovato</p>";
        }
        else {
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
            $_POST["saldo_beneficiario"] = $saldo_beneficiario;
            $_POST["nome_beneficiario"] = $nome_beneficiario;
        }
    }
    return "";
}

function esegui_transazione($con) {
    if (!isset($_POST["importo"]) || !isset($_POST["saldo_beneficiario"]) || !isset($_POST["beneficiari"])){
        return "<h2>Spiacenti:</h2>\n<p>Impossibile ottemperare alla richiesta perché è andato perso un parametro</p>";
    }
    $user_pagante = $_SESSION["username"];
    $saldo_pagante = $_SESSION["saldo"];
    $user_beneficiario = $_POST["beneficiari"];
    $saldo_beneficiario = $_POST["saldo_beneficiario"];
    $importo = $_POST["importo"];
    //return"$user_pagante, $saldo_pagante, $user_beneficiario, $saldo_beneficiario, $importo";
    if ($user_beneficiario === $user_pagante) {
        return "<h2>Spiacenti:</h2>\n<p>Non puoi pagarti da solo</p>";
    }

    //Aggiornamento dei saldi;
    update_saldo($con, $user_pagante, ($saldo_pagante-$importo));
    $_SESSION["saldo"] = ($saldo_pagante-$importo);
    update_saldo($con, $user_beneficiario, ($saldo_beneficiario+$importo));
    //Creazione del log;
    crea_log($con, $user_pagante, $user_beneficiario, $importo);

    $esito = "<h2>Successo!</h2>\n<p>La transazione è avvenuta correttamente, grazie per aver scelto PWR Bank!</p>
              <p class='pie'>({$_SESSION["nome"]} | {$_POST["nome_beneficiario"]} | ".getFormatoSaldo($importo)."€ | ".date("Y-m-d H:i:s").").</p>";
    return $esito;
}

function update_saldo($con, $username, $saldo) {
    $query = "UPDATE usr SET usr.saldo = ? WHERE usr.nick = ?;";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "is", $saldo, $username);

    if (!$stmt) {
        return "<h2>Errore di connessione:</h2>\n<p>Impossibile preparare la query.</p>";
    }
    else {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return "";
    }
}

function crea_log($con, $pagante, $beneficiario, $importo) {
    $data = date("Y-m-d H:i:s");
    $query = "INSERT INTO log(log.src, log.dst, log.importo, log.`data`) VALUES(?, ?, ?, ?);";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssis", $pagante, $beneficiario, $importo, $data);

    if (!$stmt) {
        return "<h2>Errore di connessione:</h2>\n<p>Impossibile preparare la query.</p>";
    }
    else {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return "";
    }
}

function richiedi_log($con, $tipologia, $periodo) {
    $user = $_SESSION["username"];
    $user_no = "";
    $query = "SELECT usr1.nome, usr2.nome, log.importo, log.`data` FROM log, usr AS usr1, usr AS usr2
                WHERE (log.`data` >= ? AND log.`data` < ?) AND (log.src = ? OR log.dst = ?) AND (log.src = usr1.nick AND log.dst = usr2.nick)";
    $mese = date("m");
    $anno = date("Y");
    if ($periodo == "1")
        $data_inizio = date("Y-m-d H:i:s", mktime(0, 0, 0, $mese, 1, $anno));
    else if ($periodo == "3")
        $data_inizio = date("Y-m-d H:i:s", mktime(0, 0, 0, $mese-2, 1, $anno));
    else {
        return "<h2>Errore:</h2>\n<p>Errore sconosciuto.</p>";
    }
    $data_fine = date("Y-m-d H:i:s", mktime( 0, 0, 0, $mese+1, 1, $anno));

    $stmt = mysqli_prepare($con, $query);
    if ($tipologia === "entrata")
        mysqli_stmt_bind_param($stmt, "ssss", $data_inizio, $data_fine, $user_no, $user);
    else if ($tipologia === "uscita")
        mysqli_stmt_bind_param($stmt, "ssss", $data_inizio, $data_fine, $user, $user_no);
    else if ($tipologia === "tutti")
        mysqli_stmt_bind_param($stmt, "ssss", $data_inizio, $data_fine, $user, $user);
    else {
        return "<h2>Errore:</h2>\n<p>Errore sconosciuto.</p>";
    }

    $resoconto_script = "<script type='text/javascript'>console.log('$data_inizio, $data_fine, $user')</script>";
    $resoconto = "";
    if ($tipologia === "tutti") {
        $resoconto .= "Tutti i bonifici";
    }
    else {
        $resoconto .= "Bonifici in $tipologia";
    }
    if ($periodo == "1")
        $resoconto .= " nell'ultimo mese.";
    else
        $resoconto .= " negli ultimi tre mesi.";

    if (!$stmt) {
        return "<h2>Errore di connessione:</h2>\n<p>Impossibile preparare la query.</p>";
    }
    else {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $origine, $destinazione, $importo, $data);

        //Per far funzionare il cnt di righe
        mysqli_stmt_store_result($stmt);
        $righe = mysqli_stmt_num_rows($stmt);
        if ($righe === 0) {
            return "$resoconto_script\n<h2>Spiacenti:</h2>\n<p>Nessun pagamento trovato per: ".strtolower($resoconto)."</p>";
        }
        else {
            $risultato = "$resoconto_script\n<table id='logTable'><tr><th>#N</th><th>Esecutore</th><th>Beneficiario</th><th>Importo</th><th>Data e ora</th></tr>";
            $cnt = 1;
            while (mysqli_stmt_fetch($stmt)) {
                $risultato .= "<tr><td>$cnt</td><td>$origine</td><td>$destinazione</td><td>".getFormatoSaldo($importo)."€</td><td>$data</td></tr>";
                $cnt++;
            }
            $risultato .= "</table>";
            mysqli_stmt_close($stmt);
            return "<div class='xOverfowManager'>$risultato</div>\n<p>$resoconto</p>";
        }
    }
}

function get_amount ($val) {
    if (!preg_match('/^(([1-9][0-9]+)|([0-9]))(,[0-9]{1,2})?$/', $val)) {
        return false;
    }
    if (preg_match('/^(([1-9][0-9]+)|([0-9]))$/', $val)) {
        return (int) ($val."00");
    }
    if (preg_match('/^(([1-9][0-9]+)|([0-9])),[0-9]$/', $val)) {
        return (int) (preg_replace("/,/", "", $val)."0");
    }
    return (int) preg_replace("/,/", "", $val);
}