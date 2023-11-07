<?php
    include "db_konexioa.php";
    $db = new Datubasea();

    class Erabiltzaile{
        private $nan;
        private $izena;
        private $abizena;
        private $erabiltzailea;
        private $pasahitza;
        private $rola;

        public function __construct($nan, $izena, $abizena, $erabiltzailea, $pasahitza, $rola) {
            $this->nan = $nan;
            $this->izena = $izena;
            $this->abizena = $abizena;
            $this->erabiltzailea = $erabiltzailea;
            $this->pasahitza = $pasahitza;
            $this->rola = $rola;
        }

        // public function getNan(){
        //     return $this->nan;
        // }

        // public function getIzena(){
        //     return $this->izena;
        // }

        // public function getAbizena(){
        //     return $this->abizena;
        // }

        public function getErabiltzailea(){
            return $this->erabiltzailea;
        }

        public function getPasahitza(){
            return $this->pasahitza;
        }

        public function getRola(){
            return $this->rola;
        }


        // public function getRola(){
        //     return $this->rola;
        // }
    }

    if ($_SERVER["REQUEST_METHOD"]=="POST"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        if (isset($json_data)){
            erabiltzaileExists($json_data["erabiltzailea"],$json_data["pasahitza"]);

        }
    }

    function lortuDatuak()
    {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT * FROM erabiltzailea");
        $erabiltzaileak = array();

        if ($emaitzak !== null) {
            while ($row = $emaitzak->fetch_assoc()) {
                $erabiltzaileak[] = new Erabiltzaile($row["nan"], $row["izena"], $row["abizena"], $row["erabiltzailea"], $row["pasahitza"], $row["rola"]);
            }
        }
        return $erabiltzaileak;
    }
    
    function erabiltzaileExists($erabiltzailea, $pasahitza)
    {
        $erabiltzaileak = lortuDatuak();
        $bool = false;
        foreach ($erabiltzaileak as $erabiltzaile) {
            if ($erabiltzaile->getErabiltzailea() == $erabiltzailea) {
                if ($pasahitza == $erabiltzaile->getPasahitza()) {
                    // Erabiltzailea eta pasahitza zuzenak dira
                    // echo "Sesioan sartu zara.";
                    // return true;
                    $rol=$erabiltzaile->getRola();
                    echo json_encode("Erabiltzailea_eta_pasahitza_ondo_sartuta_daude,".$rol.",");
                } else {
                    // Pasahitza okerra
                    // echo "Pasahitza okerra.";
                    // return false;
                    echo json_encode("Pasahitza_txarto_sartuta_dago");
                }
                $bool = true;
            }
            //else{
                // Erabiltzailea ez da aurkitzen
                // echo "Erabiltzaile hori ez da existitzen.";
                // return false;
            //}
        }
        if (!$bool) {
            echo json_encode("Erabiltzailea_ez_da_existitzen");
        }
        
    }
?>

<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
    </body>
</html>