<?php
require_once "shared/session_manager.php";
require_once "shared/utilita.php";
require_once "shared/struttura.php";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php echo $meta; ?>
    <title>HOME</title>
    <?php echo $link_CSS; ?>
    <link rel="stylesheet" type="text/css" href="CSS/main_pre_header.css" media="screen">
    <?php echo $temaCSS; ?>
    <?php echo $link_alte_pagine; ?>
    <script type="text/javascript" src="utility.js"></script>
</head>
<body onresize="correctNav();">
<p id="signature">-LD</p>
<div class="gridContainer">
    <?php echo $header; ?>
    <div class="page_header">
        <h1>PWR<span id="cancelletto">-</span>BANK</h1>
    </div>
    <?php echo $nav; ?>
    <?php echo $menu; ?>
    <div class="main">
        <section>
            <h2>PWR Bank, la rivoluzione</h2>
            <p>Fai il login per accedere a tutte le fantastiche opzioni da noi offerte,
                che comprendono <span class="mexProm">(e si limitano a)</span>
                effettuare un pagamento e consultare lo storico delle transazioni.</p>
            <p>Metti al sicuro il frutto del tuo duro lavoro, scegli il futuro, scegli PWR Bank!</p>
        </section>
        <section>
            <?php echo $noscript; ?>
        </section>
    </div>
    <?php echo $footer; ?>
</div>
</body>
</html>