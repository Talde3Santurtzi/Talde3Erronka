<?php
    include "db_konexioa.php";

    $db = new Datubasea();

    class Kategoria
    {
        public $id;
        public $izena;

        public function __construct($id, $izena)
        {
            $this->id = $id;
            $this->izena = $izena;
        }

        public function getId()
        {
            return $this->id;
        }

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

    function ezabatuKategoria($id)
    {
        global $db;
        $sql = "DELETE FROM kategoria WHERE id = '$id'";
        $db->ezabatu($sql);
    }

    function eguneratuKategoria($id, $izena)
    {
        global $db;
        $sql = "UPDATE kategoria SET izena = '$izena' WHERE id = '$id'";
        $db->eguneratu($sql);
    }

    function txertatuKategoria($izena)
    {
        global $db;
        $sql = "INSERT INTO kategoria (izena) VALUES ('$izena')";
        $db->txertatu($sql);
    }

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