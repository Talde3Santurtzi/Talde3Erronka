<?php
    //si no se pone esto, no va a funcionar en el server
    header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");    
    include "db_konexioa.php";

        $db = new Datubasea ();
        /**
         * Inbentarioako klasea.
         *
         * @class Inbentarioa
         */
        class Inbentarioa {
            /**
             * @var int $idEkipamendu Ekipamenduaren id-a.
             */
            public $idEkipamendu;
            /**
             * @var string $etiketa Ekipoaren etiketa.
             */
            public $etiketa;
            /**
             * @var date $erosketaData Erosketa data.
             */
            public $erosketaData;
            /**
             * @var string $marka Ekipoaren marka.
             */
            public $marka;
            /**
             * @var string $modelo Ekipoaren modeloa.
             */
            public $modelo;
            /**
             * Inbentario klaseko eraikitzailea.
             *
             * @method __construct
             * @param string $etiketa Ekipoaren etiketa.
             * @param int $idEkipamendu Ekipoaren id-a.
             * @param string $erosketaData Erisketa data.
             * @param string $marka Ekipoaren marka.
             * @param string $modelo Ekipoaren modeloa.
             */
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
            }else if(isset($_GET["eti"])){
                $emaitzak = lortuInbentarioaByEtiketa($_GET["eti"]);
                echo json_encode($emaitzak);
            }else{
                $emaitzak = lortuInbentarioa();
                echo json_encode($emaitzak);
            }
            exit;
        }elseif($_SERVER["REQUEST_METHOD"] == "POST"){
            $json_data = json_decode(file_get_contents("php://input"), true);
            if(isset($json_data["gehitu"])){
                $emaitzak = txertatuInbentarioaPlusStock($json_data["etiketa"], $json_data["idEkipamendu"], $json_data["erosketaData"]);
            echo json_decode("okai");
            }else{
                $emaitzak = txertatuInbentarioa($json_data["etiketa"], $json_data["idEkipamendu"], $json_data["erosketaData"]);
                echo json_decode("okai");
            }
        }elseif($_SERVER["REQUEST_METHOD"] == "PUT"){
            $json_data = json_decode(file_get_contents("php://input"), true);
            if(isset($json_data["idEkipamendu"], $json_data["etiketa"], $json_data["erosketaData"], $json_data["aurrekoEtiketa"])){
                $idEkipamendu = $json_data["idEkipamendu"];
                $etiketa = $json_data["etiketa"];
                $erosketaData = $json_data["erosketaData"];
                $aurrekoEtiketa = $json_data["aurrekoEtiketa"];

                eguneratuInbentarioa($etiketa, $idEkipamendu, $erosketaData, $aurrekoEtiketa);
            }
        }if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
            $json_data = json_decode(file_get_contents("php://input"), true);
            if (isset($json_data)) {
                // foreach ($json_data as $item) {
                //     $etiketa = $item['etiketa'];
                //     $idEkipamendu = $item['idEkipamendu'];
                //     ezabatuInbentarioa($etiketa, $idEkipamendu);
                // }
                for ($i=0; $i < count($json_data); $i++) { 
                    $datuak = explode(',', $json_data[$i]);
                    $etiketa=$datuak[0];
                    $id=$datuak[1];
                    ezabatuInbentarioa($etiketa, $id);
                }
            }
        }
        /**
         * Inbentariotik ezabatu ekipo bat eta eguneratu.
         *
         * @method ezabatuInbentarioa
         * @param string $etiketa Ezabatzeko ekipoaren etiketa.
         * @param int $idEkipamendu Ekipoaren id-a.
         * @return void
         */
        function ezabatuInbentarioa($etiketa, $idEkipamendu) {
            global $db;
            $sql = "DELETE FROM inbentarioa WHERE etiketa = '$etiketa'";
            $db->ezabatu($sql);

            $sql2 = "UPDATE ekipamendua SET stock = stock - 1 WHERE id = '$idEkipamendu'";
            $db->eguneratu($sql2);
        }
        /**
         * Inbentarioko ekipo bat eguneratzeko.
         *
         * @method eguneratuInbentarioa
         * @param string $etiketa Ekipoaren etiketa.
         * @param int $idEkipamendu Ekipoaren id-a.
         * @param string $erosketaData Erosketa data.
         * @param string $aurrekoEtiketa Aurreko elementuaren etiketa.
         * @return void
         */
        function eguneratuInbentarioa($etiketa, $idEkipamendu, $erosketaData, $aurrekoEtiketa) {
            global $db;
            if($etiketa===$aurrekoEtiketa){
                $sql = "UPDATE inbentarioa SET erosketaData = '$erosketaData' WHERE etiketa = '$etiketa'";
                $db->ezabatu($sql);
            }else{
                txertatuInbentarioaPlusStock($etiketa, $idEkipamendu, $erosketaData);
                ezabatuInbentarioa($aurrekoEtiketa, $idEkipamendu);
            }

            
            //$sql = "UPDATE inbentarioa SET etiketa = '$etiketa', erosketaData = '$erosketaData' WHERE idEkipamendu = '$idEkipamendu'";
            //$db->eguneratu($sql);
        }
        /**
         * Ekipo berri bat sartzeko inbentarioan.
         *
         * @method txertatuInbentarioa
         * @param string $etiketa Ekipoaren etiketa.
         * @param int $idEkipamendu Ekipoaren id-a.
         * @param string $erosketaData Erosketa data.
         * @return void
         */
        function txertatuInbentarioa($etiketa, $idEkipamendu, $erosketaData) {
            global $db;
            $sqlMax = "SELECT stock FROM ekipamendua WHERE id = '$idEkipamendu'";
            $sqlCount = "SELECT count(etiketa) FROM inbentarioa WHERE idEkipamendu = '$idEkipamendu'";
            $sqlMaximo = $db->datuakLortu($sqlMax);
            $sqlCuenta = $db->datuakLortu($sqlCount);
            $arr1 = array();
            $arr2 = array();
            if (is_object($sqlMaximo)){
                while ($row = $sqlMaximo->fetch_assoc()) {
                    $arr1[] = $row;
                }
                echo $arr1[0]["stock"];
            }

            if (is_object($sqlCuenta)){
                while ($row =  $sqlCuenta->fetch_assoc()) {
                    $arr2[] = $row;
                }
                echo $arr2[0]["count(etiketa)"];
            }
            //echo $arr1[0]. " ".$arr2[0];
            if($arr1[0]["stock"] >  $arr2[0]["count(etiketa)"]){
                $sql = "INSERT INTO inbentarioa (etiketa, idEkipamendu, erosketaData) VALUES ('$etiketa', '$idEkipamendu', '$erosketaData')";
                $db->txertatu($sql);
            }else{
                echo "No hay stock suficiente.";
            }
        }
        /**
         * Ekipo berria sartzeko inbentarioan eta eguneratzeko.
         *
         * @param string $etiketa Ekipoaren etiketa.
         * @param int $idEkipamendu Ekipoaren id-a.
         * @param string $erosketaData Erosketa data.
         * @return void
         */
        function txertatuInbentarioaPlusStock($etiketa, $idEkipamendu, $erosketaData) {
            global $db;
                $sql = "INSERT INTO inbentarioa (etiketa, idEkipamendu, erosketaData) VALUES ('$etiketa', '$idEkipamendu', '$erosketaData')";
                $db->txertatu($sql);

                $sql2 = "UPDATE ekipamendua SET stock = stock + 1 WHERE id = '$idEkipamendu'";
                echo $sql2;
                $db->eguneratu($sql2);
                echo "Listo";
        }
        /**
         * Ekipoen array-a lortzeko funtzioa.
         *
         * @return array Ekipoekin betetako array-a.
         */
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
        /**
         * ID batekin dauden ekipoen array-a.
         *
         * @param int $id Ekipoaren id-a.
         * @return array Ekipoekin betetako array-a.
         */
        function lortuInbentarioaById($id) {
            global $db;
            $emaitzak = $db->datuakLortu("SELECT I.*, E.marka, E.modelo FROM inbentarioa I INNER JOIN ekipamendua E ON I.idEkipamendu = E.id WHERE E.id=$id");
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

        // function lortuInbentarioaByEtiketa($eti) {
        //     global $db;
        //     $sql = "SELECT I.etiketa, E.marka, E.modelo FROM inbentarioa I , ekipamendua E WHERE I.idEkipamendu = E.id AND I.etiketa='$eti'";
        //     //$emaitzak = $db->datuakLortu("SELECT I.*, E.marka, E.modelo FROM inbentarioa I INNER JOIN ekipamendua E ON I.idEkipamendu = E.id WHERE I.etiketa='$eti'");
        //     $emaitzak = $db->datuakLortu($sql);
        //     $inbentarioa = array();
        //     if (is_object($emaitzak)) {
        //         while ($row = $emaitzak->fetch_assoc()) {
        //             $inbentarioa = array($row["etiketa"], $row["idEkipamendu"], $row["erosketaData"], $row["marka"], $row["modelo"]);
        //         }
        //         return $inbentarioa;
        //     }else{
        //         return "mal";
        //     }
        // }

        /**
         * Etiketa erabilita ekipoen lorpena.
         *
         * @param string $eti Ekipoen etiketa.
         * @return array Ekipoen array-a.
         */
        function lortuInbentarioaByEtiketa($eti) {
            global $db;
            $sql = "SELECT I.etiketa, E.marka, E.modelo, I.erosketaData, I.idEkipamendu FROM inbentarioa I, ekipamendua E WHERE I.idEkipamendu = E.id AND I.etiketa='$eti'";
            $emaitzak = $db->datuakLortu($sql);
            $inbentarioa = array();
        
            if ($emaitzak->num_rows > 0) {
                while ($row = $emaitzak->fetch_assoc()) {
                    $inbentarioa[] = array(
                        "etiketa" => $row["etiketa"],
                        "marka" => $row["marka"],
                        "modelo" => $row["modelo"],
                        "erosketaData" => $row["erosketaData"],
                        "idEkipamendu" => $row["idEkipamendu"]
                    );
                }
                return $inbentarioa;
            } else {
                return "mal";
            }
        }

?>