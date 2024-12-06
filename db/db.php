<?php
$serverName = "DESKTOP-NNQBUK5";
$connectionInfo = array("Database" => "RoyTransmotors");

$conn = sqlsrv_connect($serverName, $connectionInfo);

if ($conn === false) {

    echo "Connection could not be established.<br />";
    die(print_r(sqlsrv_errors(), true));
}

?>