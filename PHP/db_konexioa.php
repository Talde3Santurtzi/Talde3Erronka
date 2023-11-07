<?php
    class Datubasea {
        private $servername;
        private $username;
        private $password;
        private $datubase;
        private $conn;

        public function __construct($servername = "192.168.201.103", $username = "talde3", $password = "talde3", $datubase = "3WAG2E1") {
            $this->servername = $servername;
            $this->username = $username;
            $this->password = $password;
            $this->datubase = $datubase;
        }

        public function konexioa() {
            $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->datubase);

            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
            //echo "Konexioa eginda. <br>";
        }

        public function eguneratu($sql) {
            $this->konexioa();
            //echo $sql;
            if ($this->conn->query ($sql))
            {
                //echo "Datuak egueratu dira.";
            }else{
                //echo "Datuak ezin dira eguneratu". $conn->error;
            }
            $this->conn->close();
        }

        public function txertatu($sql) {
            $this->konexioa();
            //echo $sql;
            if ($this->conn->query ($sql))
            {
                //echo "Datuak txertatu dira. <br>";
            }else{
                //echo "Datuak ez dira txertatu. <br>". $conn->error;
            }
            $this->conn->close();
        }

        public function ezabatu($sql) {
            $this->konexioa();
            //echo $sql. "<br>";
            if ($this->conn->query ($sql))
            {
                //echo "Datuak ezabatu dira. <br>";
            }else{
                //echo "Datuak ez dira ezabatu. <br>";
            }
            $this->conn->close();
        }

        public function datuakLortu($sql) {
            $this->konexioa();
            $emaitza = $this->conn->query($sql);
            if ($emaitza->num_rows>0){   
                return $emaitza;
            }else{
                //echo "Ez dago daturik. <br>";
            }
            $this->conn->close();
        }

        public function closeConnection() {
            $this->conn->close();
        }
    }
?>