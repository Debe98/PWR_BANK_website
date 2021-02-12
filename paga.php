<?php
require_once "shared/session_manager.php";
require_once "shared/utilita.php";
require_once "shared/user_manager.php";
require_once "shared/struttura.php";
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <?php echo $meta; ?>
    <title>Paga</title>
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
        <h1>Paga<span id="cancelletto">!</span></h1>
    </div>
    <?php echo $nav; ?>
    <?php echo $menu; ?>
    <div class="main">
        <section>
            <?php echo $sectionResult; ?>
        </section>
        <section>
            <?php
            if (!$formPresentePaga)
                echo $noscript;
            ?>
        </section>
    </div>
    <?php echo $footer; ?>
</div>
</body>
</html>