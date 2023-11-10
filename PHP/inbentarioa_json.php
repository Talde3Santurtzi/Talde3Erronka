<?php
    //si no se pone esto, no va a funcionar en el server
    header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    
    include "db_konexioa.php";

    $db = new Datubasea ();

    class Inbentarioa {
        public $idEkipamendu;
        public $etiketa;
        public $erosketaData;
        public $marka;
        public $modelo;
        
        public function __construct($etiketa, $idEkipamendu, $erosketaData, $marka, $modelo) {
            $this->etiketa = $etiketa;
            $this->idEkipamendu = $idEkipamendu;
            $this->erosketaData = $erosketaData;
            $this->marka = $marka;
            $this->modelo = $modelo;
        }
    }

    if ($_SERVER["REQUEST_METHOD"]=="GET"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        if(isset($_GET["num"])){
            $emaitzak = lortuInbentarioaById($_GET["num"]);
            echo json_encode($emaitzak);
        }else{
            $emaitzak = lortuInbentarioa();
            echo json_encode($emaitzak);
        }
        exit;
    }elseif($_SERVER["REQUEST_METHOD"] == "POST"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        $emaitzak = txertatuInbentarioa($json_data["etiketa"], $json_data["id"], $json_data["erosketaData"]);
        echo json_decode("okai");
    }elseif($_SERVER["REQUEST_METHOD"] == "PUT"){
        $json_data = json_decode(file_get_contents("php://input"), true);
        if(isset($json_data["id"], $json_data["etiketa"], $json_data["erosketaData"])){
            $idEkipamendu = $json_data["id"];
            $etiketa = $json_data["etiketa"];
            $erosketaData = $json_data["erosketaData"];

            eguneratuInbentarioa($etiketa, $idEkipamendu, $erosketaData);
        }
    }if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
        $json_data = json_decode(file_get_contents("php://input"), true);
    
        if (isset($json_data)) {
            foreach ($json_data as $item) {
                $etiketa = $item['etiketa'];
                $idEkipamendu = $item['id'];
                ezabatuInbentarioa($etiketa, $idEkipamendu);
            }
        }
    }
    
    function ezabatuInbentarioa($etiketa, $idEkipamendu) {
        global $db;
        $sql = "DELETE FROM inbentarioa WHERE etiketa = '$etiketa'";
        $db->ezabatu($sql);

        $sql2 = "UPDATE ekipamendua SET stock = (SELECT stock FROM ekipamendua WHERE id = '$idEkipamendu') - 1 WHERE id = '$idEkipamendu'";
        $db->eguneratu($sql2);
    }

    function eguneratuInbentarioa($etiketa, $idEkipamendu, $erosketaData) {
        global $db;
        $sql = "UPDATE inbentarioa SET etiketa = '$etiketa', erosketaData = '$erosketaData' WHERE idEkipamendu = '$idEkipamendu'";
        $db->eguneratu($sql);
    }

    function txertatuInbentarioa($etiketa, $idEkipamendu, $erosketaData) {
        global $db;
        $sql = "INSERT INTO inbentarioa (etiketa, idEkipamendu, erosketaData) VALUES ('$etiketa', '$idEkipamendu', '$erosketaData')";
        $db->txertatu($sql);
    }

    function lortuInbentarioa() {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT I.*, E.marka, E.modelo FROM inbentarioa I INNER JOIN ekipamendua E ON I.idEkipamendu = E.id");
        $inbentarioa = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $inbentarioa[] = new Inbentarioa($row["etiketa"], $row["idEkipamendu"], $row["erosketaData"], $row["marka"], $row["modelo"]);
            }
            return $inbentarioa;
        }else{
            //echo "".$emaitzak;
        }
    }

    function lortuInbentarioaById() {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT I.*, E.marka, E.modelo FROM inbentarioa I INNER JOIN ekipamendua E ON I.idEkipamendu = E.id");
        $inbentarioa = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $inbentarioa[] = new Inbentarioa($row["etiketa"], $row["idEkipamendu"], $row["erosketaData"], $row["marka"], $row["modelo"]);
            }
            return $inbentarioa;
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