<?php
    //si no se pone esto, no va a funcionar en el server
    header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    
    include "db_konexioa.php";

    $db = new Datubasea ();

    class Ekipamendua {
        public $id;
        public $izena;
        public $deskribapena;
        public $marka;
        public $modelo;
        public $stock;
        public $idKategoria;
        
        public function __construct($id, $izena, $deskribapena, $marka, $modelo, $stock, $idKategoria) {
            $this->id = $id;
            $this->izena = $izena;
            $this->deskribapena = $deskribapena;
            $this->marka = $marka;
            $this->modelo = $modelo;
            $this->stock = $stock;
            $this->idKategoria = $idKategoria;
        }

        public function getId()
        {
            return $this->id;
        }

        public function getIzena()
        {
            return $this->izena;
        }

        public function getDeskribapena()
        {
            return $this->deskribapena;
        }

        public function getMarka()
        {
            return $this->marka;
        }

        public function getModelo()
        {
            return $this->modelo;
        }

        public function getStock()
        {
            return $this->stock;
        }

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



    function ezabatuEkipamendua($id) {
        global $db;
        $sql = "DELETE FROM ekipamendua WHERE id = '$id'";
        $db->ezabatu($sql);
    }

    function eguneratuEkipamendua($id, $izena, $deskribapena, $marka, $modelo, $stock, $idKategoria) {
        global $db;
        $sql = "UPDATE ekipamendua SET izena = '$izena', marka = '$marka', deskribapena = '$deskribapena', modelo = '$modelo', stock = '$stock', idKategoria = (SELECT id FROM kategoria WHERE izena = '$idKategoria') WHERE id = '$id'";
        $db->eguneratu($sql);
    }

    function txertatuEkipamendua($izena, $deskribapena, $marka, $modelo, $stock, $idKategoria) {
        global $db;
        $sql = "INSERT INTO ekipamendua (izena, deskribapena, marka, modelo, stock, idKategoria) VALUES ('$izena', '$deskribapena', '$marka', '$modelo', '$stock','$idKategoria')";
        $db->txertatu($sql);
    }

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
            //echo "".$emaitzak;
        }
    }
?>


<html>
    <head>
    </head>
    <body>
        
    </body>
</html>