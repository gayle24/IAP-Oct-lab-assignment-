<?php
class User {
    private $conn;
    private $table = "users";

    public $name;
    public $email;
    public $password;
    public $code;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        $query = "INSERT INTO " . $this->table . " (name, email, password, code) 
                  VALUES (:name, :email, :password, :code)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":code", $this->code);
        return $stmt->execute();
    }

    public function readAll() {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table);
        $stmt->execute();
        return $stmt;
    }
}
?>
