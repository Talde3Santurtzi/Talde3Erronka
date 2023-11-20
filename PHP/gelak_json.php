<?php
    //si no se pone esto, no va a funcionar en el server
    header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");  
    
    include "db_konexioa.php";

    $db = new Datubasea();
    /**
     * Gela baten klasea
     *
     * @class Gela
     */
    class Gela
    {
        /**
         * @var int $id Erabiltzailearen id-a.
         */
        public $id;
        /**
         * @var string $izena Erabiltazilearen izena.
         */
        public $izena;
        /**
         * @var string $abizena Erabiltzailearen abizena.
         */
        public $taldea;
        /**
         * Constructor de la clase Gela.
         *
         * @method __construct
         * @param int $id Identificador único de la Gela.
         * @param string $izena Nombre de la Gela.
         * @param string $taldea Taldea (grupo) al que pertenece la Gela.
         */
        public function __construct($id, $izena, $taldea)
        {
            $this->id = $id;
            $this->izena = $izena;
            $this->taldea = $taldea;
        }
        /**
         * Gelaren id-a lortzeko
         *
         * @method getId
         * @return int Gelaren id-a.
         */
        public function getId()
        {
            return $this->id;
        }
        /**
         * Gelaren izena lortzeko.
         *
         * @method getIzena
         * @return string Gelaren izena.
         */
        public function getIzena()
        {
            return $this->izena;
        }
        /**
         * Gelaren taldea lortzeko.
         *
         * @method getTaldea
         * @return string Gelaren taldea.
         */
        public function getTaldea()
        {
            return $this->taldea;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // Devolver datos JSON al front-end
        $json_data = json_decode(file_get_contents("php://input"), true);
        if(isset($_GET["num"])){
            $emaitzak = lortuGelakById($_GET["num"]);
            echo json_encode($emaitzak);
        }else{
            $emaitzak = lortuGelak();
            echo json_encode($emaitzak);
        }
        exit;
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Manejar solicitudes POST
        $json_data = json_decode(file_get_contents("php://input"), true);
        $emaitzak = txertatuGela($json_data["izena"],$json_data["taldea"]);
        echo json_encode($emaitzak);
    } elseif ($_SERVER["REQUEST_METHOD"] == "PUT") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        // Realizar actualización de datos en la base de datos
        if (isset($json_data["id"], $json_data["izena"], $json_data["taldea"])) {
            $id = $json_data["id"];
            $izena = $json_data["izena"];
            $taldea = $json_data["taldea"];
            eguneratuGela($id, $izena, $taldea);
            echo json_encode($id."".$izena." ".$taldea);
        }
    } elseif ($_SERVER["REQUEST_METHOD"] == "DELETE") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        // Realizar eliminación de datos en la base de datos
        if (isset($json_data)) {
            $id = $json_data;
            foreach ($id as $value) {
                ezabatuGela($value);
            }
            echo "OK";
        }
    }
    /**
     * Gela ezabatzeko datu basetik.
     *
     * @method ezabatuGela
     * @param int $id Gelaren id-a.
     * @return void
     */
    function ezabatuGela($id)
    {
        global $db;
        $sql = "DELETE FROM kokalekua WHERE idGela = '$id'";
        $db->ezabatu($sql);
        $sql = "DELETE FROM gela WHERE id = '$id'";
        $db->ezabatu($sql);
    }
    /**
     * Gela eguneratzeko.
     *
     * @method eguneratuGela
     * @param int $id Gelaren id-a.
     * @param string $izena Gelaren izena.
     * @param string $taldea Gelaren taldea.
     * @return void
     */
    function eguneratuGela($id, $izena, $taldea)
    {
        global $db;
        $sql = "UPDATE gela SET izena = '$izena', taldea = '$taldea' WHERE id = '$id'";
        $db->eguneratu($sql);
    }
    /**
     * Gelak sartzeko.
     *
     * @method txertatuGela
     * @param string $izena Gelaren izena.
     * @param string $taldea Gelaren taldea.
     * @return void
     */
    function txertatuGela($izena, $taldea)
    {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT izena from gela");
        $bul = true;
        $gelak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $gelak = $row["izena"];
                if($row["izena"] == $izena){
                    $bul = false;
                }
            }
            if ($bul){
                $sql = "INSERT INTO gela (izena, taldea) VALUES ('$izena', '$taldea')";
                $db->txertatu($sql);
                return "Ondo txertatu da";

            }else{
                return "Gela hori jada existitzen da. Probatu beste izenarekin.";
            }
        }else{
            $sql = "INSERT INTO gela (izena, taldea) VALUES ('$izena', '$taldea')";
                $db->txertatu($sql);
                return "Ondo txertatu da";

        }
    }
    /**
     * Gelen informazioa lortzen du.
     *
     * @method lortuGelak
     * @return array Gelen array-a.
     */
    function lortuGelak()
    {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT * FROM gela");
        $gelak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $gelak[] = new Gela($row["id"], $row["izena"], $row["taldea"]);
            }
            return $gelak;
        }
    }
    /**
     * Gelen informazioa lortzen du.
     *
     * @method lortuGelakById
     * @param int $id Gelaren id-a.
     * @return array Gelen array-a.
     */
    function lortuGelakById($id)
    {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT * FROM gela WHERE id = '$id'");
        $gelak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $gelak[] = new Gela($row["id"], $row["izena"], $row["taldea"]);
            }
            return $gelak;
        }
    }

?>