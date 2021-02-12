<?php
$formPresentePaga = false;
$user = $_POST["username"];
$pw = $_POST["password"];

$inputValido = false;
if (isset($user) && isset($pw)) {
    $inputValido = true;
}

$sectionResult = "";

require_once "db_manager.php";
$con = createConnectionRead();
if (mysqli_connect_errno()) {
    $sectionResult = "<h2>Errore nel collegamento:</h2>\n<p>Impossibile accedere al DB</p>\n<p>Errore: ".mysqli_connect_error()."</p>";
}
else {
    if (!$_SESSION["autenticato"] && !$inputValido) {
        $sectionResult  = "<h2>Errore di autenticazione:</h2>\n<p>Inserisci delle credenziali.</p>";
        $report_numero_beneficiari = ottieni_numero_beneficiari($con);
        $sectionResult .= $report_numero_beneficiari;
    }
    else {
        $report_login = "";
        if (!$_SESSION["autenticato"]) {
            $report_login = login($con, $user, $pw);
            if ($report_login === ""){
                $sectionResult = "<script type='text/javascript'> console.log(\"Benvenuto $user nel mio umile sito!\") </script>\n";
            }
        }
        if ($report_login !== "") {
            $sectionResult = $report_login;
        }
        else {
            $report_beneficiari = ottieni_beneficiari($con);
            if (strpos(strtolower($report_beneficiari), "table") === false) {
                $sectionResult .= $report_beneficiari;
            }
            else {
                $formPresentePaga = true;
                $sectionResult .= "<h2>Effettua un pagamento:</h2>
                            <div class=\"accessoContainer\">
                                <div class=\"accessoForm\">
                                    <form method='post' action='log.php' onsubmit=\"return formChecker(this)\" onreset=\"formReset()\">
                                        <p class=\"fieldLabel\">Beneficiari:</p>
                                        $report_beneficiari
                                        <p class=\"fieldLabel\">Importo:</p>
                                        <input type=\"text\" class=\"fieldInput\" name=\"importo\">
                                        <div class=\"button-flex-container\">
                                            <button type=\"submit\" class=\"subBut\">Procedi</button>
                                        </div>
                                    </form>
                                </div>
                                <div class=\"avvertenzeForm\">
                                    <h3>Attenzione!</h3>
                                    $noscript
                                    <!--Generati automaticamente:-->
                                    <!--<div id=\"(field.name)Errore\" class=\"erroreSpecifico\">-->
                                    <!--    <p class=\"erroreCampo\">(field.Name):</p>-->
                                    <!--    <div class=\"listaErrori\">...</div>-->
                                    <!--</div>-->
                                </div>
                            </div>
                            <p class=\"pie\">Prova i nostri bonifici istantanei!</p>";
            }
        }
    }
}
if ($con !== false)
    mysqli_close($con);

function login ($con, $user, $pw) {
    $login_report = "";
    $query = "SELECT usr.nome, usr.saldo, usr.negozio FROM usr WHERE usr.nick = ? AND usr.pwd = ?;";
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        $login_report = "<h2>Errore di connessione:</h2>\n<p>Impossibile preparare la query.</p>";
    }
    else {
        mysqli_stmt_bind_param($stmt, "ss", $user, $pw);

        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nome, $saldo, $negozio);

        //Per far funzionare il cnt di righe
        mysqli_stmt_store_result($stmt);
        $righe = mysqli_stmt_num_rows($stmt);

        if ($righe !== 1) {
            $login_report = "<h2>Dati errati:</h2>\n<p>Spiacenti, le credenziali inserite non sono valide</p>";
        } else {
            while (mysqli_stmt_fetch($stmt)) {
                $_SESSION["autenticato"] = true;
                $_SESSION["username"] = $user;
                $_SESSION["nome"] = $nome;
                $_SESSION["saldo"] = $saldo;
                $_SESSION["negozio"] = $negozio;
                setcookie("username", $user, time()+(60*60*24*5));
            }
            mysqli_stmt_close($stmt);
        }
    }
    return $login_report;
}

function ottieni_beneficiari($con) {
    $query = "SELECT usr.nome, usr.nick FROM usr WHERE usr.negozio = 1 AND usr.nick != ?;";
    if ($_SESSION["negozio"] === 1) {
        $query = "SELECT usr.nome, usr.nick FROM usr WHERE usr.nick != ?;";
    }
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        $beneficiari_report = "<h2>Errore di connessione:</h2>\n<p>Impossibile preparare la query.</p>";
    }
    else {
        mysqli_stmt_bind_param($stmt, "s", $_SESSION["username"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nome, $user);

        //Per far funzionare il cnt di righe
        mysqli_stmt_store_result($stmt);
        $righe = mysqli_stmt_num_rows($stmt);

        if ($righe < 1) {
            $beneficiari_report = "<h2>Spiacenti:</h2>\n<p>Nessun utente trovato</p>";
        } else {
            $beneficiari_report = "<table id='beneficiariTable'><tr><th>Nome</th><th>Paga</th></tr>";
            $beneficiari_report .= "<tr><td>Nessuno</td><td><input type='radio' name='beneficiari' class=\"fieldInput\" value='' checked></td></tr>";
            while (mysqli_stmt_fetch($stmt)) {
                $beneficiari_report .= "<tr><td>$nome</td><td><input type='radio' class=\"fieldInput\" name='beneficiari' value='$user'></td></tr>";
            }
            $beneficiari_report .= "</table>";
            mysqli_stmt_close($stmt);
        }
    }
    return $beneficiari_report;
}

function ottieni_numero_beneficiari($con) {
    $beneficiari_report = "";
    $query = "SELECT usr.negozio, COUNT(DISTINCT usr.nick) AS cnt FROM usr GROUP BY usr.negozio ORDER BY usr.negozio ASC;";
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        $beneficiari_report = "<h2>Errore di connessione:</h2>\n<p>Impossibile preparare la query.</p>";
    }
    else {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $negozio, $cnt);

        //Per far funzionare il cnt di righe
        mysqli_stmt_store_result($stmt);

        while (mysqli_stmt_fetch($stmt)) {
            if ($negozio == 0) {
                if ($cnt !== 1)
                    $beneficiari_report = "<p>Nel sistema sono presenti $cnt utenti ";
                else
                    $beneficiari_report = "<p>Nel sistema è presente $cnt utente ";
            }
            elseif ($negozio == 1) {
                if ($cnt !== 1)
                    $beneficiari_report .= "e $cnt negozi.</p>";
                else
                    $beneficiari_report .= "e $cnt negozio.</p>";
            }
        }
        $beneficiari_report .= "\n<p class=\"pie\">Per utilizzare tutte le funzioni offerte, si prega di fare il <a href='login.php'>login</a>.</p>";
        mysqli_stmt_close($stmt);
    }
    return $beneficiari_report;
}