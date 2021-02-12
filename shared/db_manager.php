<?php
function createConnectionRead() {
    return mysqli_connect(null, "uReadOnly", "posso_solo_leggere", "pagamenti");
}

function createConnectionWrite() {
    return mysqli_connect(null, "uReadWrite", "SuperPippo!!!", "pagamenti");
}