<?php
    include "db_konexioa.php";

    $db = new Datubasea();

    class Gela
    {
        public $id;
        public $izena;
        public $taldea;

        public function __construct($id, $izena, $taldea)
        {
            $this->id = $id;
            $this->izena = $izena;
            $this->taldea = $taldea;
        }

        public function getId()
        {
            return $this->id;
        }

        public function getIzena()
        {
            return $this->izena;
        }

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
                echo json_encode("okai");
    } elseif ($_SERVER["REQUEST_METHOD"] == "PUT") {
        $json_data = json_decode(file_get_contents("php://input"), true);
        // Realizar actualización de datos en la base de datos
        if (isset($json_data["id"], $json_data["izena"], $json_data["taldea"])) {
            $id = $json_data["id"];
            $izena = $json_data["izena"];
            $taldea = $json_data["taldea"];
            eguneratuGela($id, $izena, $taldea);
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

    function ezabatuGela($id)
    {
        global $db;
        $sql = "DELETE FROM gela WHERE id = '$id'";
        $db->ezabatu($sql);
    }

    function eguneratuGela($id, $izena, $taldea)
    {
        global $db;
        $sql = "UPDATE gela SET izena = '$izena', taldea = '$taldea' WHERE id = '$id'";
        $db->eguneratu($sql);
    }

    function txertatuGela($izena, $taldea)
    {
        global $db;
        $sql = "INSERT INTO gela (izena, taldea) VALUES ('$izena', '$taldea')";
        $db->txertatu($sql);
    }

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

<!DOCTYPE html>
<html>
<head>
    <title>php...</title>
</head>
<body>
    
</body>
</html>