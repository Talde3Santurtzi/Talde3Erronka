<?php
    //si no se pone esto, no va a funcionar en el server
    header("Access-Control-Allow-Headers:{$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    
    include "db_konexioa.php";

    $db = new Datubasea ();

    class Kokalekua {
        private $idGela;
        private $etiketa;
        private $hasieraData;
        private $amaieraData;
        
        public function __construct($etiketa, $idGela, $hasieraData, $amaieraData) {
            $this->etiketa = $etiketa;
            $this->idGela = $idGela;
            $this->hasieraData = $hasieraData;
            $this->amaieraData = $amaieraData;
        }

        public function getEtiketa()
        {
        return $this->etiketa;
        }

        public function getIdGela()
        {
        return $this->idGela;
        }

        public function getHasieraData()
        {
        return $this->hasieraData;
        }

        public function getAmaieraData()
        {
        return $this->amaieraData;
        }
    }

    if ($_SERVER["REQUEST_METHOD"]=="GET"){
        if (isset($_GET["ezabatu"])){
            ezabatuKokalekua($_GET["etiketa"], $_GET["hasieraData"]);
        }elseif (isset($_GET["aldatu"])){
            eguneratuKokalekua($_GET["etiketa"], $_GET["idGela"], $_GET["hasieraData"], $_GET["amaieraData"]);
        }elseif (isset($_GET["etiketa"])){
            txertatuKokalekua($_GET["etiketa"], $_GET["idGela"], $_GET["hasieraData"], $_GET["amaieraData"]);
        }           
    }

    function ezabatuKokalekua($etiketa, $hasieraData) {
        global $db;
        $sql = "DELETE FROM kokalekua WHERE etiketa = '$etiketa' AND hasieraData = '$hasieraData'";
        $db->ezabatu($sql);
    }

    function eguneratuKokalekua($etiketa, $idGela, $hasieraData, $amaieraData) {
        global $db;
        $sql = "UPDATE kokalekua SET idGela = '$idGela', amaieraData = '$amaieraData' WHERE etiketa = '$etiketa' AND hasieraData = '$hasieraData'";
        $db->eguneratu($sql);
    }

    function txertatuKokalekua($etiketa, $idGela, $hasieraData, $amaieraData) {
        global $db;
        $sql = "INSERT INTO kokalekua (etiketa, idGela, hasieraData, amaieraData) VALUES ('$etiketa', '$idGela', '$hasieraData', '$amaieraData')";
        $db->txertatu($sql);
    }

    function lortuKokalekuak() {
        global $db;
        $emaitzak = $db->datuakLortu("SELECT * FROM kokalekua");
        $kokalekuak = array();
        if (is_object($emaitzak)) {
            while ($row = $emaitzak->fetch_assoc()) {
                $kokalekuak[] = new Kokalekua($row["etiketa"], $row["idGela"], $row["hasieraData"], $row["amaieraData"]);
            }
            return $kokalekuak;
        }else{
            //echo "".$emaitzak;
        }
        
    }

?>
<html>
    <head>
    </head>
    <body>
        <table border=1>
            <tr>
                <th>Etiketa</th><th>ID gela</th><th>Hasiera data</th><th>Amaiera data</th>
            </tr>
            <?php
                $emaitzak = lortuKokalekuak();
                if (!empty($emaitzak)) {
                    foreach ($emaitzak as $kokalekua) {
            ?>
            <tr>
                <form action=<?php echo $_SERVER["PHP_SELF"]?> method=" GET">
                <td><input type="text" name="etiketa" value="<?php echo $kokalekua->getEtiketa(); ?>" readonly></td>
                    <td><input type="text" name="idGela" value="<?php echo $kokalekua->getIdGela(); ?>"></td>
                    <td><input type="text" name="hasieraData" value="<?php echo $kokalekua->getHasieraData(); ?>" readonly></td>
                    <td><input type="text" name="amaieraData" value="<?php echo $kokalekua->getAmaieraData(); ?>"></td>
                    
                    <td><button type="submit" name="aldatu" value="">Aldatu</button></td>
                    <td><button type="submit" name="ezabatu" value="">Ezabatu</button></td>
                </form>
            </tr>
            <?php    
                    }
                }
            ?>
        </table>
        <br>
        <div>
        <form action=<?php echo $_SERVER["PHP_SELF"]?> method="GET">
            <label>Etiketa: </label><input type=text name=etiketa>
            <label>ID Gela: </label><input type=text name=idGela>
            <label>Hasiera data: </label><input type=text name=hasieraData>
            <label>Amaiera data: </label><input type=text name=amaieraData>
            <input type=submit value=Bidali>
        </form>
        </div>
    </body>
</html>