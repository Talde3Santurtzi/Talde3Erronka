<?php 
include("Algo.php");

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    header("Content-Type: application/json; charset=UTF-8");
    $algo=new Algo();
    $algo->setNombre("Manolo");
    $algo->setApellido("Pedro");
    $otro=new Algo();
    $otro->setNombre("Manolo2");
    $otro->setApellido("Pedro2");
    $array=array($algo,$otro);

    $outp = $array->fetch_all(MYSQLI_ASSOC);    

    $jsonEn=json_encode($outp);
    echo $jsonEn;
}

?>