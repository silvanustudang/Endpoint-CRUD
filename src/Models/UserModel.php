<?php
class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // [C] - CREATE USER
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->conn->prepare($query);

        // 🛠️ PERBAIKAN: Gunakan trim() untuk menghilangkan spasi/newline di awal/akhir string
        $username_clean = trim($data['username']);
        $email_clean = trim($data['email']);
        $password_clean = trim($data['password']);

        // Bind data
        $stmt->bindParam(":username", $username_clean);
        $stmt->bindParam(":email", $email_clean);
        $stmt->bindParam(":password", $password_clean); 

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Error handling untuk duplikat (UNIQUE constraint)
            return false;
        }
    }

    // [R] - READ ALL USERS (Modified to include password)
    public function readAll() {
        $query = "SELECT id, username, email, password FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    // [R] - READ ONE USER (Modified to include password)
    public function readOne($id) {
        $query = "SELECT id, username, email, password FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt;
    }

    // [U] - UPDATE USER
    public function update($id, $data) {
        $setClauses = [];
        
        // 🛠️ PERBAIKAN: Bersihkan data di sini sebelum binding
        $clean_data = [];
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $clean_data[$key] = trim($value); // Bersihkan nilai yang masuk
                $setClauses[] = "{$key} = :{$key}";
            }
        }
        
        if (empty($setClauses)) { return false; }

        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $setClauses) . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        foreach ($clean_data as $key => &$value) {
             // Menggunakan & (reference) untuk bindParam
             $stmt->bindParam(":$key", $value);
        }
        
        $stmt->bindParam(":id", $id);
        
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    // [D] - DELETE USER
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);

        return $stmt->execute();
    }
}
?>