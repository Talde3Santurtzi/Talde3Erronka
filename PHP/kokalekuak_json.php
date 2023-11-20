<?php
    //si no se pone esto, no va a funcionar en el server
    header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");    
    include "db_konexioa.php";

    $db = new Datubasea ();
    /**
    * Kokalekua klasea.
    *
    * @class Kokalekua
    */
    class Kokalekua {
        /**
        * @var int $idGela Gelaren id-a.
        */
        public $idGela;
        /**
        * @var string $etiketa Inbentarioaren etiketa.
        */
        public $etiketa;
        /**
        * @var string $hasieraData erreserba hasten den data.
        */
        public $hasieraData;
        /**
        * @var string $amaiera erreserba amaitzen den data.
        */
        public $amaieraData;
        /**
        * Kategoria klasearen eraikitzailea.
        *
        * @method __construct
        * @param int $idGela Gelaren id-a.
        * @param string $etiketa Inbentarioaren etiketa.
        * @param string $hasieraData erreserba hasten den data.
        * @param string $amaiera erreserba amaitzen den data.
        */
        public function __construct($etiketa, $idGela, $hasieraData, $amaieraData) {
            $this->etiketa = $etiketa;
            $this->idGela = $idGela;
            $this->hasieraData = $hasieraData;
            $this->amaieraData = $amaieraData;
        }
        /**
         * Etiketa lortzeko.
         *
         * @method getEtiketa
         * @return int
         */
        public function getEtiketa()
        {
            return $this->etiketa;
        }
        /**
         * IdGela lortzeko.
         *
         * @method getIdGela
         * @return string
         */
        public function getIdGela()
        {
            return $this->idGela;
        }
        /**
         * HasieraData lortzeko.
         *
         * @method getHasieraData
         * @return string
         */
        public function getHasieraData()
        {
            return $this->hasieraData;
        }
        /**
         * AmaieraData lortzeko.
         *
         * @method getAmaieraData
         * @return string
         */
        public function getAmaieraData()
        {
            return $this->amaieraData;
        }
    }

        /**
         * Request metodoaren arabera zer egin behar den jakiteko
         */
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        if (isset($_GET["num"])) {
            $emaitzak = lortuKokalekuakByEtiketa($_GET["num"]);
            echo json_encode($emaitzak);
        } else {
            $emaitzak = lortuKokalekuak();
            echo json_encode($emaitzak);
        }
        exit;
    } elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        $emaitzak = txertatuKokalekua($json_data["etiketa"], $json_data["idGela"], $json_data["hasieraData"], $json_data["amaieraData"]);
        echo json_encode($emaitzak);
    } elseif ($_SERVER["REQUEST_METHOD"] == "PUT") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        if (isset($json_data["datosAntiguos"], $json_data["etiketa"], $json_data["idGela"], $json_data["hasieraData"], $json_data["amaieraData"])) {
            $datosAntiguos = $json_data["datosAntiguos"];
            $etiketa = $json_data["etiketa"];
            $idGela = $json_data["idGela"];
            $hasieraData = $json_data["hasieraData"];
            $amaieraData = $json_data["amaieraData"];
            $emaitzak=eguneratuKokalekua($datosAntiguos, $etiketa, $idGela, $hasieraData, $amaieraData);
            echo json_encode($emaitzak);
        }
    } elseif ($_SERVER["REQUEST_METHOD"] == "DELETE") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        if (isset($json_data)) {
            $id = $json_data;
            foreach ($id as $value) {
                ezabatuKokalekua($value);
            }
            echo "OK";
        }
    }
        /**
         * Kokalekua ezabatzeko.
         *
         * @method ezabatuKokalekua
         * @return string
         */
    function ezabatuKokalekua($value) {
        global $db;
        $datuak=explode(",",$value);
        $sql = "DELETE FROM kokalekua WHERE etiketa = '$datuak[0]' AND idGela = '$datuak[1]' AND hasieraData = '$datuak[2]'";
        $db->ezabatu($sql);
    }
        /**
         * Kokalekua eguneratzeko + balioztapenak.
         *
         * @method eguneratuKokalekua
         * @return string
         */
    function eguneratuKokalekua($datosAntiguos, $etiketa, $idGela, $hasieraData, $amaieraData) {
        global $db;
        $datosAnt = explode(",", $datosAntiguos);
        $emaitzak = $db->datuakLortu("SELECT hasieraData, amaieraData FROM kokalekua WHERE etiketa = '$datosAnt[0]'");
        $bul = true;
        $kokalekuak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $kokalekuak[] = array($row["hasieraData"], $row["amaieraData"]);
                if($hasieraData <= $amaieraData){
                    if ($row["hasieraData"] <= $hasieraData || $row["amaieraData"] >= $hasieraData){
                        if($row["hasieraData"] <= $amaieraData || $row["amaieraData"] >= $amaieraData){
                            if($row["amaieraData"] >= $hasieraData && $row["hasieraData"] <= $amaieraData) {
                                $bul = false;
                            }
                        }   
                    }
                    if($bul){
                        $sql = "UPDATE kokalekua SET idGela = '$idGela', hasieraData = '$hasieraData', amaieraData = '$amaieraData', etiketa = '$etiketa' WHERE etiketa = '$datosAnt[0]' AND idGela = '$datosAnt[1]' AND hasieraData = '$datosAnt[2]'";
                        $db->eguneratu($sql);
                        return "Ekipamendu hau dago libre data honetarako.";
                    }else{
                        $sql = "UPDATE kokalekua SET idGela = '$idGela', etiketa = '$etiketa' WHERE etiketa = '$datosAnt[0]' AND idGela = '$datosAnt[1]' AND hasieraData = '$datosAnt[2]'";
                        $db->eguneratu($sql);
                        return "Ekipamendu hau ez dago libre data honetarako.";
                    }
                }else{
                    return "Hasierako data amaierakoa baino handiagoa da.";
                }
            }
        }else{
            if($hasieraData <= $amaieraData){
                if($bul){
                    $sql = "UPDATE kokalekua SET idGela = '$idGela', hasieraData = '$hasieraData', amaieraData = '$amaieraData', etiketa = '$etiketa' WHERE etiketa = '$datosAnt[0]' AND idGela = '$datosAnt[1]' AND hasieraData = '$datosAnt[2]'";
                    $db->eguneratu($sql);
                }
            }else{
                return "Hasierako data amaierakoa baino handiagoa da.";
            }
        } 


        // $datosAnt = explode(",", $datosAntiguos);
        // $sql = "UPDATE kokalekua SET idGela = '$idGela', hasieraData = '$hasieraData', amaieraData = '$amaieraData', etiketa = '$etiketa' WHERE etiketa = '$datosAnt[0]' AND idGela = '$datosAnt[1]' AND hasieraData = '$datosAnt[2]'";
        // $db->eguneratu($sql);
    }
        /**
         * Kokalekua txertatzeko + balioztapenak.
         *
         * @method txertatuKokalekua
         * @return string
         */
    function txertatuKokalekua($etiketa, $idGela, $hasieraData, $amaieraData) {
        global $db;
        $datos = explode(",", $etiketa);
        $emaitzak = $db->datuakLortu("SELECT hasieraData, amaieraData FROM kokalekua WHERE etiketa = '$datos[0]'");
        $bul = true;
        $kokalekuak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $kokalekuak[] = array($row["hasieraData"], $row["amaieraData"]);
                if($hasieraData <= $amaieraData){
                    if ($row["hasieraData"] <= $hasieraData || $row["amaieraData"] >= $hasieraData){
                        if($row["hasieraData"] <= $amaieraData || $row["amaieraData"] >= $amaieraData){
                            if($row["amaieraData"] >= $hasieraData && $row["hasieraData"] <= $amaieraData) {
                                $bul = false;
                            }
                        }   
                    }
                    if($bul){
                        $sql = "INSERT INTO kokalekua (etiketa, idGela, hasieraData, amaieraData) VALUES ('$etiketa', '$idGela', '$hasieraData', '$amaieraData')";
                        $db->txertatu($sql);
                        return "Ekipamendu hau dago libre data honetarako.";
    
                    }else{
                        return "Ekipamendu hau ez dago libre data honetarako.";
                    }
                }else{
                    return "Hasierako data amaierakoa baino handiagoa da.";
                }
            }
        }else{
            if($hasieraData <= $amaieraData){
                if($bul){
                    $sql = "INSERT INTO kokalekua (etiketa, idGela, hasieraData, amaieraData) VALUES ('$etiketa', '$idGela', '$hasieraData', '$amaieraData')";
                    $db->txertatu($sql);
                }
            }else{
                return "Hasierako data amaierakoa baino handiagoa da.";
            }
        } 
    }
        /**
         * Kokalekuen datuak lortzeko.
         *
         * @method lortuKokalekuak
         * @return array
         */
    function lortuKokalekuak() {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT I.etiketa, G.izena, I.idEkipamendu, E.marka, E.modelo, K.idGela, K.hasieraData, K.amaieraData FROM kokalekua K, ekipamendua E, inbentarioa I, gela G WHERE K.etiketa = I.etiketa AND  I.idEkipamendu = E.id AND G.id = K.idGela;");
        $kokalekuak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $kokalekuak[] = array($row["etiketa"],  $row["idEkipamendu"], $row["marka"], $row["modelo"], $row["idGela"], $row["hasieraData"], $row["amaieraData"], $row["izena"]);
            }
            return $kokalekuak;
        }else{
        } 
    }
        /**
         * Kokaleku bat lortu bere id-aren arabera.
         *
         * @method lortuKokalekuakByEtiketa
         * @return array
         */
    function lortuKokalekuakByEtiketa($etiketa) {
        $datos = explode(",", $etiketa);
        global $db;
        $emaitzak = $db->datuakLortu("SELECT K.etiketa, G.izena, E.marka, E.modelo, K.idGela, K.hasieraData, K.amaieraData FROM kokalekua K, ekipamendua E, inbentarioa I, gela G WHERE K.etiketa = '$datos[0]' AND K.idGela = '$datos[1]' AND I.etiketa = K.etiketa AND I.idEkipamendu = E.id AND K.idGela = G.id  AND K.hasieraData = '$datos[2]' GROUP BY K.etiketa");
        $kokalekuak = array();
    
        if ($emaitzak->num_rows > 0) {
            while ($row = $emaitzak->fetch_assoc()) {
                $kokalekuak[] = array(
                    "etiketa" => $row["etiketa"],
                    "marka" => $row["marka"],
                    "modelo" => $row["modelo"],
                    "idGela" => $row["idGela"],
                    "izena" => $row["izena"],
                    "hasieraData" => $row["hasieraData"],
                    "amaieraData" => $row["amaieraData"]
                );
            }
            return $kokalekuak;
        } else {
            return "mal";
        }
    }
?>