<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'todo_list';
    private $username = 'root'; // Ganti dengan username database kamu
    private $password = ''; // Ganti dengan password database kamu
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return null;
    }
}

?>
