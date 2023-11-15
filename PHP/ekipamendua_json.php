<?php
    //si no se pone esto, no va a funcionar en el server
    //header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    //header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");

    /**
     * Sarbide kontrola HTTP metodoentzako eta datu baseko konexiorako.
     * @header Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS
     * @include db_konexioa.php
     * @see lortuEkipamendua
     * @see lortuEkipamenduaById
     * @see txertatuEkipamendua
     * @see eguneratuEkipamendua
     * @see ezabatuEkipamendua
     */

    /**
     * Klasea ekipo bakoitzerako
     *
     * @class Ekipamendua
     */
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");    
    include "db_konexioa.php";

    $db = new Datubasea ();

    class Ekipamendua {
        /**
         * @var int $id Ekipoaren id-a.
         */
        public $id;
        /**
         * @var string $izena Ekipoaren izena.
         */
        public $izena;
        /**
         * @var string $deskribapena Ekipoaren deskribapena.
         */
        public $deskribapena;
        /**
         * @var string $marka Ekipoaren marka.
         */
        public $marka;
        /**
         * @var string $modelo Ekipoaren modeloa.
         */
        public $modelo;
        /**
         * @var int $stock Zenbateko ekipoak dauden.
         */
        public $stock;
        /**
         * @var int $idKategoria Kategoria id-a.
         */
        public $idKategoria;
        /**
         * Constructor de la clase Ekipamendua.
         *
         * @param int $id Ekipoaren id-a.
         * @param string $izena Ekipoaren izena.
         * @param string $deskribapena Ekipoaren deskribapena.
         * @param string $marka Ekipoaren marka.
         * @param string $modelo Ekipoaren modeloa.
         * @param int $stock Zenbateko ekipoak dauden.
         * @param int $idKategoria Kategoria id-a.
         */
        public function __construct($id, $izena, $deskribapena, $marka, $modelo, $stock, $idKategoria) {
            $this->id = $id;
            $this->izena = $izena;
            $this->deskribapena = $deskribapena;
            $this->marka = $marka;
            $this->modelo = $modelo;
            $this->stock = $stock;
            $this->idKategoria = $idKategoria;
        }
        /**
         * Id-a lortzeko.
         *
         * @method getId
         * @return int
         */
        public function getId()
        {
            return $this->id;
        }
        /**
         * Izena lortzeko.
         *
         * @method getIzena
         * @return string
         */
        public function getIzena()
        {
            return $this->izena;
        }
        /**
         * Deskribapena lortzeko.
         *
         * @method getDeskribapena
         * @return string
         */
        public function getDeskribapena()
        {
            return $this->deskribapena;
        }
        /**
         * Ekipoaren marka lortzeko.
         *
         * @method getMarka
         * @return string
         */
        public function getMarka()
        {
            return $this->marka;
        }
        /**
         * Ekipoaren modeloa lortzeko.
         *
         * @method getModelo
         * @return string
         */
        public function getModelo()
        {
            return $this->modelo;
        }
        /**
         * Stock kantitatea lortzen du.
         *
         * @method getStock
         * @return int
         */
        public function getStock()
        {
            return $this->stock;
        }
        /**
         * Kategoriaren id-a lortzen du.
         *
         * @method getIdKategoria
         * @return int
         */
        public function getIdKategoria()
        {
            return $this->idKategoria;
        }
    }

    /* if ($_SERVER["REQUEST_METHOD"]=="GET"){
        if (isset($_GET["ezabatu"])){
            ezabatuEkipamendua($_GET["id"]);
        }elseif (isset($_GET["aldatu"])){
            eguneratuEkipamendua($_GET["id"], $_GET["izena"], $_GET["deskribapena"], $_GET["marka"], $_GET["modelo"], $_GET["stock"], $_GET["idKategoria"]);
        }elseif (isset($_GET["id"])){
            txertatuEkipamendua( $_GET["id"], $_GET["izena"], $_GET["deskribapena"], $_GET["marka"], $_GET["modelo"], $_GET["stock"], $_GET["idKategoria"]);
        }           
    } */


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 

    /**
     * 
     * HTTP metodoen erabilera.
     * 
     */
    if ($_SERVER["REQUEST_METHOD"]=="GET"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        if(isset($_GET["num"])){
            $emaitzak = lortuEkipamenduaById($_GET["num"]);
            echo json_encode($emaitzak);
        }else{
            $emaitzak = lortuEkipamendua();
            echo json_encode($emaitzak);
        }
        exit;
    }elseif($_SERVER["REQUEST_METHOD"] == "POST"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        $emaitzak = txertatuEkipamendua($json_data["izena"], $json_data["deskribapena"], $json_data["marka"], $json_data["modelo"], $json_data["stock"], $json_data["idKategoria"]);
        echo json_decode("okai");
    }elseif($_SERVER["REQUEST_METHOD"] == "PUT"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        if(isset($json_data["id"], $json_data["izena"], $json_data["deskribapena"], $json_data["marka"], $json_data["modelo"], $json_data["stock"], $json_data["idKategoria"])){
            $id = $json_data["id"];
            $izena = $json_data["izena"];
            $marka = $json_data["marka"];
            $deskribapena = $json_data["deskribapena"];
            $modelo = $json_data["modelo"];
            $idKategoria = $json_data["idKategoria"];
            $stock = $json_data["stock"];

            eguneratuEkipamendua($id, $izena, $deskribapena, $marka, $modelo, $stock, $idKategoria);
        }
    }elseif($_SERVER["REQUEST_METHOD"] == "DELETE"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        if(isset($json_data)){
            $id = $json_data;
            foreach($id as $value){
                ezabatuEkipamendua($value);
            }
            echo "okey";
        }
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    /**
     * Ekipoa datu basetik ezabatzeko.
     *
     * @method ezabatuEkipamendua
     * @param int $id Ekipoaren id-a.
     * @return void
     */
    function ezabatuEkipamendua($id) {
        global $db;
        $sql = "DELETE FROM inbentarioa WHERE idEkipamendu = '$id'";
        $db->ezabatu($sql);
        $sql = "DELETE FROM ekipamendua WHERE id = '$id'";
        $db->ezabatu($sql);
    }
    /**
     * Datu baseko informazioa eguneratzeko
     *
     * @method eguneratuEkipamendua
     * @param int $id Ekipoaren id-a.
     * @param string $izena Ekipoaren izena.
     * @param string $deskribapena Ekipoaren deskribapena.
     * @param string $marka Ekipoaren marka.
     * @param string $modelo Ekipoaren modeloa.
     * @param int $stock Stock-aren kantitatea.
     * @param int $idKategoria Kategoriaren id-a.
     * @return void
     */
    function eguneratuEkipamendua($id, $izena, $deskribapena, $marka, $modelo, $stock, $idKategoria) {
        global $db;
        $sql = "UPDATE ekipamendua SET izena = '$izena', marka = '$marka', deskribapena = '$deskribapena', modelo = '$modelo', stock = '$stock', idKategoria = (SELECT id FROM kategoria WHERE izena = '$idKategoria') WHERE id = '$id'";
        $db->eguneratu($sql);
    }
    /**
     * Ekipo barria datu basean.
     *
     * @method txertatuEkipamendua
     * @param string $izena Ekipoaren izena.
     * @param string $deskribapena Ekipoaren deskribapena.
     * @param string $marka Ekipoaren marka.
     * @param string $modelo Ekipoaren modeloa.
     * @param int $stock Stock-aren kantitatea.
     * @param int $idKategoria Kategoriaren id-a.
     * @return void
     */
    function txertatuEkipamendua($izena, $deskribapena, $marka, $modelo, $stock, $idKategoria) {
        global $db;
        $sql = "INSERT INTO ekipamendua (izena, deskribapena, marka, modelo, stock, idKategoria) VALUES ('$izena', '$deskribapena', '$marka', '$modelo', '$stock','$idKategoria')";
        $db->txertatu($sql);
    }
    /**
     * Ekipoen informazioa lortzeko.
     *
     * @method lortuEkipamendua
     * @return array|false Daturik ez badago.
     */
    function lortuEkipamendua() {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT E.*, K.izena as kategoriaIzena  FROM ekipamendua E, kategoria K WHERE E.idKategoria = K.id");
        $ekipamendua = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $ekipamendua[] = new Ekipamendua($row["id"], $row["izena"], $row["deskribapena"], $row["marka"],  $row["modelo"], $row["stock"], $row["kategoriaIzena"]);
            }
            return $ekipamendua;
        }else{
            //echo "".$emaitzak;
        }
        
    }
    /**
     * Ekipoen informazioa id-arekin.
     *
     * @method lortuEkipamenduaById
     * @param int $id Ekipoaren id-a.
     * @return array|false Ez badago daturik.
     */
    function lortuEkipamenduaById($id) {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT E.*, K.izena as kategoriaIzena  FROM ekipamendua E, kategoria K WHERE E.id = '$id' AND E.idKategoria = K.id");
        $ekipamendua = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $ekipamendua[] = new Ekipamendua($row["id"], $row["izena"], $row["deskribapena"], $row["marka"],  $row["modelo"], $row["stock"], $row["kategoriaIzena"]);
            }
            return $ekipamendua;
        }else{
        }
    }
?>


<html>
    <head>
    </head>
    <body>
        
    </body>
</html>