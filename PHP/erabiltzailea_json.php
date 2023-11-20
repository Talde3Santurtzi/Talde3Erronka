<?php
    //si no se pone esto, no va a funcionar en el server
    header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");    
    include "db_konexioa.php";

    $db = new Datubasea ();
    /**
     * Erabiltzaile baterako klasea.
     *
     * @class Erabiltzailea
     */
    class Erabiltzailea {
        /**
         * @var int $nan Erabiltzailearen izena.
         */
        public $nan;
        /**
         * @var string $izena Erabiltzailearen nan-a.
         */
        public $izena;
        /**
         * @var string $abizena Erabiltzailearen abizena.
         */
        public $abizena;
        /**
         * @var string $erabiltzailea Erabiltzailearen usuarioa.
         */
        public $erabiltzailea;
        /**
         * @var string $pasahitza pasahitza.
         */
        public $pasahitza;
        /**
         * @var string $rola Rola.
         */
        public $rola;
        /**
         * Constructor de la clase Erabiltzailea.
         *
         * @method __construct
         * @param string $nan Erabiltzailearen nan-a.
         * @param string $izena Erabiltzailearen izena.
         * @param string $abizena Erabiltzailearen abizena.
         * @param string $erabiltzailea Erabiltzailearen usuarioa.
         * @param string $pasahitza Pasahitza.
         * @param string $rola Rola.
         */
        public function __construct($nan, $izena, $abizena, $erabiltzailea, $pasahitza, $rola) {
            $this->nan = $nan;
            $this->izena = $izena;
            $this->abizena = $abizena;
            $this->erabiltzailea = $erabiltzailea;
            $this->pasahitza = $pasahitza;
            $this->rola = $rola;
        }
        /**
         * NAN-a lortzeko.
         *
         * @method getNAN
         * @return int
         */
        public function getNan()
        {
            return $this->nan;
        }
        /**
         * Izena-a lortzeko.
         *
         * @method getIzena
         * @return string
         */
        public function getIzena()
        {
            return $this->izena;
        }
        /**
         * Abizena-a lortzeko.
         *
         * @method getAbizena
         * @return string
         */
        public function getAbizena()
        {
            return $this->abizena;
        }
        /**
         * Erabiltzailea-a lortzeko.
         *
         * @method getErabiltzailea
         * @return string
         */
        public function getErabiltzailea()
        {
            return $this->erabiltzailea;
        }
        /**
         * Pasahitza-a lortzeko.
         *
         * @method getPasahitza
         * @return string
         */
        public function getPasahitza()
        {
            return $this->pasahitza;
        }
        /**
         * Rola-a lortzeko.
         *
         * @method getRola
         * @return string
         */
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
        echo json_encode($emaitzak);
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

            $emaitzak = eguneratuErabiltzailea($nan, $izena, $abizena, $erabiltzailea, $pasahitza, $rola, $aurrekoNan);
            echo json_encode($emaitzak);
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
    /**
     * Usuarioa ezabatzeko datu basetik.
     *
     * @method ezabatuErabiltzailea
     * @param string $nan Erabiltzailearen nan-a.
     * @return void
     */

     function nanOndo($nan)
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
    }

    function ezabatuErabiltzailea($nan) {
        global $db;
        $sql = "DELETE FROM erabiltzailea WHERE nan = '$nan'";
        $db->ezabatu($sql);
    }
    /**
     * Datu basea eguneratzeko.
     *
     * @method eguneratuErabiltzailea
     * @param string $nan Erabiltzailearen nan-a.
     * @param string $izena Erabiltzailearen izena.
     * @param string $abizena Erabiltzailearen abizena.
     * @param string $erabiltzailea Erabiltzailearen usuarioa.
     * @param string $pasahitza Pasahitza.
     * @param string $rola Rola.
     * @param string $aurrekoNan Aurreko usuarioaren nan-a.
     * @return void
     */
    function eguneratuErabiltzailea($nan, $izena, $abizena, $erabiltzailea, $pasahitza, $rola, $aurrekoNan) {
        global $db;
        $bienmal=nanOndo($nan);
        if($bienmal=="Mal"){
            return "NAN hau txarto dago"; 
        }else{
             $sql = "SELECT nan FROM erabiltzailea";
        $bul = true;
        $emaitzak = $db->datuakLortu($sql);
        $erabiltzaileak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $erabiltzaileak = array($row["nan"]);
                if ($row["nan"] == $nan){
                    $bul = false;
                }
            }
        }

        if ($bul){
            //ezabatuErabiltzailea($aurrekoNan);
            $sql = "INSERT INTO erabiltzailea (nan, izena, abizena, erabiltzailea, pasahitza, rola) VALUES ('$nan', '$izena', '$abizena', '$erabiltzailea', '$pasahitza', '$rola')";
            $db->txertatu($sql);
            return  $nan." ".$izena." ".$abizena." ".$erabiltzailea." ". $pasahitza." ".$rola; 
        }else {
            return "NAN hau erabiltzen ari da"; 
        }
        
        if($nan==$aurrekoNan){
            $sql = "UPDATE erabiltzailea SET izena = '$izena', abizena = '$abizena', erabiltzailea = '$erabiltzailea', pasahitza = '$pasahitza', rola = '$rola' WHERE nan = '$nan'";
            $db->eguneratu($sql);
        }
        }
       
        
        //$sql = "UPDATE erabiltzailea SET izena = '$izena', erabiltzailea = '$erabiltzailea', abizena = '$abizena', pasahitza = '$pasahitza', rola = '$rola' WHERE nan = '$nan'";
        //$db->eguneratu($sql);
    }
    /**
     * Datuak sartzeko datu basean.
     *
     * @method txertatuErabiltzailea
     * @param string $nan Erabiltzauilearen nan-a.
     * @param string $izena Erabiltzailearen izena.
     * @param string $abizena Erabiltzailearen abizena.
     * @param string $erabiltzailea Erabiltzailearen usuarioa.
     * @param string $pasahitza Pasahitza.
     * @param string $rola Rola.
     * @return void
     */
    function txertatuErabiltzailea($nan, $izena, $abizena, $erabiltzailea, $pasahitza, $rola) {
        global $db;
        $bienmal=nanOndo($nan);
        if($bienmal=="Mal"){
            return "NAN hau txarto dago"; 
        }else{
            $sql = "SELECT nan FROM erabiltzailea";
            $bul = true;
            $emaitzak = $db->datuakLortu($sql);
            $erabiltzaileak = array();
            if (is_object($emaitzak)) {
                while ($row = $emaitzak->fetch_assoc()) {
                    $erabiltzaileak = array($row["nan"]);
                    if ($row["nan"] == $nan){
                        $bul = false;
                    }
                }
            }
    
            if ($bul){
                $sql = "INSERT INTO erabiltzailea (nan, izena, abizena, erabiltzailea, pasahitza, rola) VALUES ('$nan', '$izena', '$abizena', '$erabiltzailea', '$pasahitza', '$rola')";
                $db->txertatu($sql);
            }else {
                return "NAN hau erabiltzen ari da"; 
            }
            }
        
        }
        
    
    /**
     * Erabiltzailearen informazioa lortzeko.
     *
     * @method lortuErabiltzailea
     * @return array Erabiltzaile objetuen array-a.
     */
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

    /**
     * Erabiltzaileen informazioa nan-arekin.
     *
     * @method lortuErabiltzaileaById
     * @param string $nan Erabiltzaileen nan.
     * @return array Erabiltzaile objetuen array-a.
     */
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

    
?>  