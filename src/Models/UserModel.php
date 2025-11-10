<?php
class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

   
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($query);

        
        $username_clean = trim($data['username']);
        $email_clean = trim($data['email']);
        $password_clean = trim($data['password']);

    
        $stmt->bindParam(":username", $username_clean);
        $stmt->bindParam(":email", $email_clean);
        $stmt->bindParam(":password", $password_clean); 

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            
            return false;
        }
    }

    
    public function readAll() {
        $query = "SELECT id, username, email, password FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
 
    public function readOne($id) {
        $query = "SELECT id, username, email, password FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

  
    public function update($id, $data) {
        $setClauses = [];
        
        
        $clean_data = [];
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $clean_data[$key] = trim($value); 
                $setClauses[] = "{$key} = :{$key}";
            }
        }
        
        if (empty($setClauses)) { return false; }

        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $setClauses) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        foreach ($clean_data as $key => &$value) {
             
             $stmt->bindParam(":$key", $value);
        }
        
        $stmt->bindParam(":id", $id);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

   
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        return $stmt->execute();
    }
}
?>