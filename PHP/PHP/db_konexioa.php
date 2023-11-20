<?php
    /**
     * Clase Datubasea datubasearekin konektatzeko MySQL dela eta MySQLi erabiliz.
     *
     * @class Datubasea
     */
    class Datubasea {
        /**
         * @var string $servername Datu basearen helbidea.
         */
        private $servername;
        /**
         * @var string $username Erabiltzailearen izena.
         */
        private $username;
        /**
         * @var string $password Erabiltzailearen pasahitza.
         */
        private $password;
        /**
         * @var string $datubase Datu basearen izena.
         */
        private $datubase;
        /**
         * @var mysqli $conn Konexioa sortarazteko objetua.
         */
        private $conn;
        /**
         * Clase Datubasea-ren eraikitzailea.
         *
         * @param string $servername Datu basearen helbidea.
         * @param string $username Erabiltzailearen izena.
         * @param string $password Erabiltzailearen pasahitza.
         * @param string $datubase Datu basearen izena.
         */
        public function __construct($servername = "192.168.201.103", $username = "talde3", $password = "talde3", $datubase = "3WAG2E1") {
            $this->servername = $servername;
            $this->username = $username;
            $this->password = $password;
            $this->datubase = $datubase;
        }

        /**
         * Konexioa egin datu basera MySQLi erabilita.
         *
         * @method konexioa
         * @throws Exception Errorea badago.
         */
        public function konexioa() {
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->datubase);

            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
            //echo "Konexioa eginda. <br>";
        }
        /**
         * Kontsulta bat datu basea eguneratzeko
         *
         * @method eguneratu
         * @param string $sql Kontsulta SQL datu basea eguneratzeko
         */
        public function eguneratu($sql) {
            $this->konexioa();
            
            if ($this->conn->query ($sql))
            {  
            }else{   
            }
            $this->conn->close();
        }
        /**
         * Konstulta bat datuak sartzeko datu basera.
         *
         * @method txertatu
         * @param string $sql Konstulta SQL datuak sartzeko.
         */
        public function txertatu($sql) {
            $this->konexioa();
            
            if ($this->conn->query ($sql))
            {
            }else{
            }
            $this->conn->close();
        }
        /**
         * Datuak ezabatzeko kontsulta bat.
         *
         * @method ezabatu
         * @param string $sql Kontsulta SQL datuakezabatzeko.
         */
        public function ezabatu($sql) {
            $this->konexioa();
            
            if ($this->conn->query ($sql))
            {
            }else{
            }
            $this->conn->close();
        }
        /**
         * Datuak lortzeko kontsulta bat.
         *
         * @method datuakLortu
         * @param string $sql Konstulta SQL datuak lortzeko.
         * @return mysqli_result|false Kontsultaren erantzuna.
         */
        public function datuakLortu($sql) {
            $this->konexioa();
            $emaitza = $this->conn->query($sql);
            if ($emaitza->num_rows>0){   
                return $emaitza;
            }else{
            }
            $this->conn->close();
        }
        /**
         * Konexioa itxi.
         *
         * @method closeConnection
         */
        public function closeConnection() {
            $this->conn->close();
        }
    }
?>