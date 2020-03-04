<?php
function mishaninLog($text) {
    $f = fopen($_SERVER["DOCUMENT_ROOT"]."/mishanin_log.txt", "a+");
    fwrite($f, $text."\n");
}