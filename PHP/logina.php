<?php
    //si no se pone esto, no va a funcionar en el server
   header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
   header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");    
    include "db_konexioa.php";
    $db = new Datubasea();
    /**
    * Erabiltzaile klasea.
    *
    * @class Erabiltzaile
    */
    class Erabiltzaile{
        /**
        * @var int $nan Erabiltzailearen nan-a.
        */
        private $nan;
        /**
        * @var string $izena Erabiltzailearen izena.
        */
        private $izena;
        /**
        * @var string $abizena Erabiltzailearen abizena.
        */
        private $abizena;
        /**
        * @var string $erabiltzailea Erabiltzailea.
        */
        private $erabiltzailea;
        /**
        * @var string $pasahitza Erabiltzailearen pasahitza.
        */
        private $pasahitza;
        /**
        * @var string $rola Erabiltzailearen rola.
        */
        private $rola;
        /**
        * Erabiltzaile klasearen eraikitzailea.
        *
        * @method __construct
        * @param int $nan Erabiltzailearen nan-a.
        * @param string $izena Erabiltzailearen izena.
        * @param string $abizena Erabiltzailearen abizena.
        * @param string $erabiltzailea Erabiltzailea.
        * @param string $pasahitza Erabiltzailearen pasahitza.
        * @param string $rola Erabiltzailearen rola.
        */
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
        /**
         * Erabiltzailea lortzeko.
         *
         * @return string Erabiltzailea.
         */
        public function getErabiltzailea(){
            return $this->erabiltzailea;
        }
        /**
         * Erabiltzailearen pasahitza lortzen du.
         *
         * @return string Pasahitza.
         */
        public function getPasahitza(){
            return $this->pasahitza;
        }
        /**
         * Erabiltzailearen rola lortzeko.
         *
         * @return string Erabiltzailearen rola.
         */
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
    /**
     * Erabiltzailearen datuak lortzen ditu.
     *
     * @return array Datuak gordetzeko array-a.
     */
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
    /**
     * Jakitzeko erabiltzailea eta pasahitza ondo dauden.
     *
     * @param string $erabiltzailea Erabiltzailea.
     * @param string $pasahitza Erabiltzailearen pasahitza.
     * @return void
     */
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