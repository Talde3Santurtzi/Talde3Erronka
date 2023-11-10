<?php
    //si no se pone esto, no va a funcionar en el server
    header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");    
    include "db_konexioa.php";

    $db = new Datubasea ();

    class Erabiltzailea {
        public $nan;
        public $izena;
        public $abizena;
        public $erabiltzailea;
        public $pasahitza;
        public $rola;
        
        public function __construct($nan, $izena, $abizena, $erabiltzailea, $pasahitza, $rola) {
            $this->nan = $nan;
            $this->izena = $izena;
            $this->abizena = $abizena;
            $this->erabiltzailea = $erabiltzailea;
            $this->pasahitza = $pasahitza;
            $this->rola = $rola;
        }

        public function getNan()
        {
            return $this->nan;
        }

        public function getIzena()
        {
            return $this->izena;
        }

        public function getAbizena()
        {
            return $this->abizena;
        }

        public function getErabiltzailea()
        {
            return $this->erabiltzailea;
        }

        public function getPasahitza()
        {
            return $this->pasahitza;
        }

        public function getRola()
        {
            return $this->rola;
        }

    }

    if ($_SERVER["REQUEST_METHOD"]=="GET"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        if(isset($_GET["num"])){
            $emaitzak = lortuErabiltzaileaById($_GET["num"]);
            echo json_encode($emaitzak);
        }else{
            $emaitzak = lortuErabiltzailea();
            echo json_encode($emaitzak);
        }
        exit;
    }elseif($_SERVER["REQUEST_METHOD"] == "POST"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        $emaitzak = txertatuErabiltzailea($json_data["nan"], $json_data["izena"], $json_data["abizena"], $json_data["erabiltzailea"], $json_data["pasahitza"], $json_data["rola"]);
        echo json_decode("okai");
    }elseif($_SERVER["REQUEST_METHOD"] == "PUT"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        if(isset($json_data["nan"], $json_data["izena"], $json_data["abizena"], $json_data["erabiltzailea"], $json_data["pasahitza"], $json_data["rola"], $json_data["aurrekoNan"])){
            $nan = $json_data["nan"];
            $izena = $json_data["izena"];
            $abizena = $json_data["abizena"];
            $erabiltzailea = $json_data["erabiltzailea"];
            $pasahitza = $json_data["pasahitza"];
            $rola = $json_data["rola"];
            $aurrekoNan = $json_data["aurrekoNan"];

            eguneratuErabiltzailea($nan, $izena, $abizena, $erabiltzailea, $pasahitza, $rola, $aurrekoNan);
        }
    }elseif($_SERVER["REQUEST_METHOD"] == "DELETE"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        if(isset($json_data)){
            $nan = $json_data;
            foreach($nan as $value){
                ezabatuErabiltzailea($value);
            }
            echo "okey";
        }
    }

    function ezabatuErabiltzailea($nan) {
        global $db;
        $sql = "DELETE FROM erabiltzailea WHERE nan = '$nan'";
        $db->ezabatu($sql);
    }

    function eguneratuErabiltzailea($nan, $izena, $abizena, $erabiltzailea, $pasahitza, $rola, $aurrekoNan) {
        global $db;
        ezabatuErabiltzailea($aurrekoNan);
        txertatuErabiltzailea($nan, $izena, $abizena, $erabiltzailea, $pasahitza, $rola);
        //$sql = "UPDATE erabiltzailea SET izena = '$izena', erabiltzailea = '$erabiltzailea', abizena = '$abizena', pasahitza = '$pasahitza', rola = '$rola' WHERE nan = '$nan'";
        //$db->eguneratu($sql);
    }

    function txertatuErabiltzailea($nan, $izena, $abizena, $erabiltzailea, $pasahitza, $rola) {
        global $db;
        $sql = "INSERT INTO erabiltzailea (nan, izena, abizena, erabiltzailea, pasahitza, rola) VALUES ('$nan', '$izena', '$abizena', '$erabiltzailea', '$pasahitza', '$rola')";
        $db->txertatu($sql);
        
    }

    function lortuErabiltzailea() {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT * FROM erabiltzailea");
        $erabiltzaileak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $erabiltzaileak[] = new Erabiltzailea($row["nan"], $row["izena"], $row["abizena"], $row["erabiltzailea"],  $row["pasahitza"], $row["rola"]);
            }
            return $erabiltzaileak;
        }
    }

    function lortuErabiltzaileaById($nan) {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT * FROM erabiltzailea WHERE nan = '$nan'");
        $erabiltzaileak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $erabiltzaileak[] = new Erabiltzailea($row["nan"], $row["izena"], $row["abizena"], $row["erabiltzailea"],  $row["pasahitza"], $row["rola"]);
            }
            return $erabiltzaileak;
        }
    }

    /*function nanOndo($nan)
    {
        $dni = $nan;
        $error = false;

        function devuelveletra($nums) {
            $letras = "TRWAGMYFPDXBNJZSQVHLCKE";
            $resto = $nums % 23;
            return $letras[$resto];
        }

        $dni = strtoupper($dni);
        $lon = strlen(str_replace(" ", "", $dni));

        if ($lon !== 9 && $lon !== 8) {
            echo "Longitud del DNI incorrecta";
            $error = true;
        }

        if ($lon == 8) {
            $letra2 = devuelveletra(intval(substr($dni, 0, 8)));
            $dni = substr($dni, 0, 8) . $letra2;
            return $dni;
        } else {
            if ($lon == 9) {
                if (is_numeric(substr($dni, 0, 8)) && substr($dni, 8, 1) == devuelveletra(intval(substr($dni, 0, 8)))) {
                    return $dni;
                } else {
                    $error = true;
                }
            }
        } if ($error) {
            return "Mal";
        }
    }*/
?>

<!DOCTYPE html>
<html>
<head>
    <title>php...</title>
</head>
<body>
    
</body>
</html>