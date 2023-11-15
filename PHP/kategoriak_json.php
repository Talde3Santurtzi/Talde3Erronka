<?php
    //si no se pone esto, no va a funcionar en el server
    header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");    
    include "db_konexioa.php";

    $db = new Datubasea();
    /**
    * Kategoria klasea.
    *
    * @class Kategoria
    */
    class Kategoria
    {
        /**
        * @var int $id Kategoriaren id-a.
        */
        public $id;
        /**
        * @var string $izena Kategoriaren izena.
        */
        public $izena;
        /**
        * Kategoria klasearen eraikitzailea.
        *
        * @method __construct
        * @param int $id Kategoriaren id-a.
        * @param string $izena Kategoriaren izena.
        */
        public function __construct($id, $izena)
        {
            $this->id = $id;
            $this->izena = $izena;
        }
        /**
         * Kategoriaren id-a lortzeko.
         *
         * @return int Kategoriaren id-a.
         */
        public function getId()
        {
            return $this->id;
        }
        /**
         * Kategoriaren izena lortzeko.
         *
         * @return string Kategoriaren izena
         */
        public function getIzena()
        {
            return $this->izena;
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        if (isset($_GET["num"])) {
            $emaitzak = lortuKategoriaById($_GET["num"]);
            echo json_encode($emaitzak);
        } else {
            $emaitzak = lortuKategoriak();
            echo json_encode($emaitzak);
        }
        exit;
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        $emaitzak = txertatuKategoria($json_data["izena"]);
        echo json_encode("okai");
    } elseif ($_SERVER["REQUEST_METHOD"] == "PUT") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        if (isset($json_data["id"], $json_data["izena"])) {
            $id = $json_data["id"];
            $izena = $json_data["izena"];
            eguneratuKategoria($id, $izena);
        }
    } elseif ($_SERVER["REQUEST_METHOD"] == "DELETE") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        if (isset($json_data)) {
            $id = $json_data;
            foreach ($id as $value) {
                ezabatuKategoria($value);
            }
            echo "OK";
        }
    }
    /**
     * Kategoria bat ezabatzeko datu basetik eta honen ekipoak.
     *
     * @param int $id Kategoriaren id-a ezabatzeko.
     * @return void
     */
    function ezabatuKategoria($id)
    {
        global $db;
        $sql = "DELETE FROM ekipamendua WHERE idKategoria = '$id'";
        $db->ezabatu($sql);
        $sql = "DELETE FROM kategoria WHERE id = '$id'";
        $db->ezabatu($sql);
    }
    /**
     * Kategoria baten izena eguneratzeko.
     *
     * @param int $id ID de la categoría a actualizar.
     * @param string $izena Nuevo nombre de la categoría.
     * @return void
     */
    function eguneratuKategoria($id, $izena)
    {
        global $db;
        $sql = "UPDATE kategoria SET izena = '$izena' WHERE id = '$id'";
        $db->eguneratu($sql);
    }
    /**
     * Kategoria berria sartzeko dat basean.
     *
     * @param string $izena Kategoria berriaren izena.
     * @return void
     */
    function txertatuKategoria($izena)
    {
        global $db;
        $sql = "INSERT INTO kategoria (izena) VALUES ('$izena')";
        $db->txertatu($sql);
    }
    /**
     * Kategoria array bat lortzen du.
     *
     * @return array Kategoriak gordetzeko array-a.
     */
    function lortuKategoriak()
    {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT * FROM kategoria");
        $kategoriak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $kategoriak[] = new Kategoria($row["id"], $row["izena"]);
            }
            return $kategoriak;
        }
    }
    /**
     * Kategoria bat lortzen du id-ekin.
     *
     * @param int $id Kategoriaren id-a.
     * @return array Kategoriaren array-a.
     */
    function lortuKategoriaById($id)
    {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT * FROM kategoria WHERE id = '$id'");
        $kategoriak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $kategoriak[] = new Kategoria($row["id"], $row["izena"]);
            }
            return $kategoriak;
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
    <title>php...</title>
    </head>
    <body>
    </body>
</html>