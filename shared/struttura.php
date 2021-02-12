<?php
//echo "<!-- ".$_COOKIE["tema"]." -->";
$temaCSS = "<link id=\"tema\" rel=\"stylesheet\" type=\"text/css\" href=\"CSS/light_theme.css\" media=\"screen\">";
$img = "<img src = \"IMG/PoliTO_logo_light.png\" alt = \"Politecnico di Torino\">";

if ($_COOKIE["tema"] == "scuro") {
    $temaCSS = "<link id=\"tema\" rel=\"stylesheet\" type=\"text/css\" href=\"CSS/dark_theme.css\" media=\"screen\">";
    $img = "<img src = \"IMG/PoliTO_logo_dark.png\" alt = \"Politecnico di Torino\">";
}

$path_pagina = $_SERVER["PHP_SELF"];
$path_array = explode("/", $path_pagina);
$nome_pagina = $path_array[count($path_array)-1];

$meta = "<meta charset=\"utf-8\">
    <meta name=\"author\" content=\"Luca Debernardi\">
    <meta name=\"description\" content=\"Esame PWR 2020\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, user-scalable=1\">
    <meta name=\"keywords\" content=\"PWR, Esame, 2020, Politecnico di Torino, Programmazione web\">";

$link_CSS = "<link rel=\"icon\" type=\"image/png\" href=\"IMG/favicon.png\">
    <link href=\"https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@100;200;300;400;500;531;600;700;800;900&display=swap\" rel=\"stylesheet\">
    <link href=\"https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap\" rel=\"stylesheet\">
    <link href=\"https://fonts.googleapis.com/css2?family=Anonymous+Pro:ital,wght@0,400;0,700;1,400;1,700&display=swap\" rel=\"stylesheet\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"CSS/generico.css\" media=\"screen\">
    <link rel=\"stylesheet\" type=\"text/css\" href=\"CSS/stampa.css\" media=\"print\">";


$header = "<div class=\"header\">
        <div id=\"sito\">
            <p>PWR-BANK</p>
        </div >
        <div id=\"user\">
            <p>User: ANONIMO</p>
            <p>Saldo: 0,00€</p>
        </div >
    </div>";
if ($_SESSION["autenticato"]) {
    $header = "<div class=\"header\">
        <div id=\"sito\">
            <p>PWR-BANK</p>
        </div >
        <div id=\"user\">
            <p>User: {$_SESSION["username"]}</p>
            <p>Saldo: ".getFormatoSaldo($_SESSION["saldo"])."€</p>
        </div >
    </div>";
}


$elencoPagine = ["Home", "Paga", "Log", "Login"];
$nav = "<div class=\"nav\" id=\"navigation\" onclick=\"handleNav();\">
        <a>Menu</a>
    </div>";
for ($cnt = 0; $cnt < count($elencoPagine); $cnt++) {
    $pagina = $elencoPagine[$cnt];
    if ($pagina === "Login" && $_SESSION["autenticato"]) {
        if (strpos(strtolower($path_pagina), "/" . strtolower($pagina) . ".") !== false) {
            $nav .= "\n<div class=\"nav active\" id=\"nav_" . ($cnt + 1) . "\">
        <a>$pagina</a>
    </div>";
        } else {
            $nav .= "\n<div class=\"nav\" id=\"nav_" . ($cnt + 1) . "\">
        <a>$pagina</a>
    </div>";
        }
        $nav .= "\n<div class=\"nav\" id=\"nav_" . ($cnt + 2) . "\">
        <form action='home.php' method='post'><button type='submit' name='logout' value='logout' id='anav' class='logout'>Logout</button></form>
    </div>";
    }
    else {
        if (strpos(strtolower($path_pagina), "/" . strtolower($pagina) . ".") !== false) {
            $nav .= "\n<div class=\"nav active\" id=\"nav_" . ($cnt + 1) . "\">
        <a href=\"#\">$pagina</a>
    </div>";
        } else {
            $nav .= "\n<div class=\"nav\" id=\"nav_" . ($cnt + 1) . "\">
        <a href=\"" . strtolower($pagina) . ".php\">$pagina</a>
    </div>";
        }
        if ($pagina === "Login" && !$_SESSION["autenticato"]) {
            $nav .= "\n<div class=\"nav\" id=\"nav_" . ($cnt + 2) . "\">
        <a>Logout</a>
    </div>";
        }
    }
}
    $nav .= "\n<div class=\"nav\" id=\"selectTema\" onmouseenter=\"anteprimaTema();\" onmouseleave=\"resetTema();\">
        <a onclick=\"switchTema() \">Tema</a>
    </div>";

$link_alte_pagine = "<link rel=\"Sito del corso\" type=\"text/html\" href=\"//security.polito.it/~lioy/01nbe/\">
    <link rel=\"Sito Politecnico di Torino\" type=\"text/html\" href=\"//www.polito.it\">";
for ($cnt = 0; $cnt < count($elencoPagine); $cnt++) {
    $pagina = $elencoPagine[$cnt];
    if (!strpos(strtolower($path_pagina), "/" . strtolower($pagina) . ".")) {
        $link_alte_pagine.="\n  <link rel=\"$pagina\" type=\"text/html\" href='".strtolower($pagina).".php'>";
    }
}




$menu = "<div class=\"menu\">
        <h2>Benvenuto ANONIMO</h2>
        <p>Al momento il tuo saldo è 0,00€.</p>
        <ul>
            <li><a href=\"login.php\">Login</a></li>
            <li><a>Logout</a></li>
        </ul>
    </div>";
if ($_SESSION["autenticato"]) {
    $menu = "<div class=\"menu\">
        <h2>Benvenuto {$_SESSION["nome"]}</h2>
        <p>Al momento il tuo saldo è ".getFormatoSaldo($_SESSION["saldo"])."€</p>
        <ul>
            <li><a>Login</a></li>
            <li><form action='home.php' method='post'><button type='submit' name='logout' value='logout' class='logout'>Logout</button></form></li>
        </ul>
    </div>";
}

$footer = "<div class=\"footer\">
        <div id=\"io\">
            <p>&copy; <abbr title=\"s244685@studenti.polito.it\">Luca Debernardi</abbr> 2020</p>
            <p>- Esame <a href=\"//security.polito.it/~lioy/01nbe/\" title=\"Sito del corso &quot;Progettazione di servizi web e reti di calcolatori&quot;\">PWR</a>: $nome_pagina</p>
        </div>
        <a href=\"//www.polito.it\" title=\"Sito del Politecnico di Torino\">$img</a>
    </div>";