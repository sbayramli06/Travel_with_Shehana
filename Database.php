<?php
class Database {
    private $host = 'mysql-shehana.alwaysdata.net';
    private $user = 'shehana';
    private $pass = 'YOUR_PASSWORD';
    private $dbname = 'shehana_drivingexperience';
    private $conn;
    
    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        
        $this->conn->set_charset("utf8mb4");
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    
    public function __destruct() {
        $this->conn->close();
    }
}
?>
