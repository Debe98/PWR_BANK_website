<?php
require_once "shared/session_manager.php";
require_once "shared/utilita.php";
require_once "shared/struttura.php";
$login_section = "<h2>Compila il form per registrarti:</h2>
            <div class=\"accessoContainer\">
                <div class=\"accessoForm\">
                    <form action=\"paga.php\" method=\"post\" onsubmit=\"return formChecker(this)\" onreset=\"formReset()\">
                        <p class=\"fieldLabel\">Username:</p>
                        <input type=\"text\" class=\"fieldInput\" name=\"username\" value=\"{$_COOKIE["username"]}\">
                        <p class=\"fieldLabel\">Password:</p>
                        <input type=\"password\" class=\"fieldInput\" name=\"password\">
                        <div class=\"button-flex-container\">
                            <button type=\"submit\" class=\"subBut\">OK</button>
                            <button type=\"reset\" class=\"canBut\">Pulisci</button>
                        </div>
                    </form>
                </div>
                <div class=\"avvertenzeForm\">
                    $noscript
                    <h3>Attenzione!</h3>
                    <!--Generati automaticamente:-->
                    <!--<div id=\"(field.name)Errore\" class=\"erroreSpecifico\">-->
                    <!--    <p class=\"erroreCampo\">(field.Name):</p>-->
                    <!--    <div class=\"listaErrori\">...</div>-->
                    <!--</div>-->
                </div>
            </div>
            <p class=\"pie\">Hai dimenticato la password? Peccato...</p>";
if ($_SESSION["autenticato"]) {
    $login_section = "<h2>Sei già autenticato!</h2>\n<p class=\"plogout\">Se vuoi fare il logout clicca</p> ";
    $login_section .= "<form action='home.php' method='post' class='logout'><button type='submit' name='logout' value='logout' class='logout'>";
    $login_section .= "QUI</button></form><pre class=\"plogout\">.</pre>";
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php echo $meta; ?>
    <title>Login</title>
    <?php echo $link_CSS; ?>
    <link rel="stylesheet" type="text/css" href="CSS/others_pre_header.css" media="screen">
    <link rel="stylesheet" type="text/css" href="CSS/formManager.css" media="screen">
    <link rel="stylesheet" type="text/css" href="CSS/stampa_form.css" media="print">
    <?php echo $temaCSS; ?>
    <?php echo $link_alte_pagine; ?>
    <script type="text/javascript" src="utility.js"></script>
    <script type="text/javascript" src="form_checker.js"></script>
</head>
<body onresize="correctNav();">
<p id="signature">-LD</p>
<div class="gridContainer">
    <?php echo $header; ?>
    <div class="page_header">
        <h1>Login<span id="cancelletto">:</span></h1>
    </div>
    <?php echo $nav; ?>
    <?php echo $menu; ?>
    <div class="main">
        <section>
            <?php echo $login_section; ?>
        </section>
        <section>
            <?php
            if ($_SESSION["autenticato"])
                echo $noscript;
            ?>
        </section>
    </div>
    <?php echo $footer; ?>
</div>
</body>
</html>