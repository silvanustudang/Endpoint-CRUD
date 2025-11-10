<?php
// Pastikan path ini benar (terutama kapitalisasi 'Config' dan 'Models')
include_once __DIR__ . '/../Models/UserModel.php';
include_once __DIR__ . '/../Config/Database.php';

class UserController {
    private $userModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->userModel = new UserModel($db);
    }

    public function handleRequest($method, $uri) {
        switch ($method) {
            case 'POST':
                if ($uri === 'api/users') {
                    $this->create();
                }
                break;
            case 'GET':
                if ($uri === 'api/users') {
                    $this->getAll();
                } elseif (preg_match('/api\/users\/(\d+)/', $uri, $matches)) {
                    $this->getOne($matches[1]);
                }
                break;
            case 'PUT':
                if (preg_match('/api\/users\/(\d+)/', $uri, $matches)) {
                    $this->update($matches[1]);
                }
                break;
            case 'DELETE':
                if (preg_match('/api\/users\/(\d+)/', $uri, $matches)) {
                    $this->delete($matches[1]);
                }
                break;
            default:
                http_response_code(405); 
                echo json_encode(["message" => "Method tidak diizinkan."]);
                break;
        }
    }

    private function create() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['password'])) {
            http_response_code(400); 
            echo json_encode(["message" => "Password dibutuhkan."]);
            return;
        }
        
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        

        if ($this->userModel->create($data)) {
            http_response_code(201); 
            echo json_encode(["message" => "User berhasil dibuat."]);
        } else {
            http_response_code(503); 
            echo json_encode(["message" => "Gagal membuat user (Username atau email mungkin sudah terdaftar)."]);
        }
    }

    private function getAll() {
        $stmt = $this->userModel->readAll();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $users_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                
                $password_clean = trim($password);
                
                $user_item = array(
                    "id" => $id,
                    "username" => $username,
                    "email" => $email,
                    "password" => $password_clean 
                );
                array_push($users_arr, $user_item);
            }
            http_response_code(200);
            echo json_encode($users_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Tidak ada user ditemukan."));
        }
    }
    
    private function getOne($id) {
        $stmt = $this->userModel->readOne($id);
        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            
            if(isset($row['password'])) {
                $row['password'] = trim($row['password']);
            }
            
            http_response_code(200);
            echo json_encode($row);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "User tidak ditemukan."]);
        }
    }
    
    private function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        
        
        if (isset($data['password'])) {
             $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($data)) {
            http_response_code(400); 
            echo json_encode(["message" => "Tidak ada data yang dikirim."]);
            return;
        }

        if ($this->userModel->update($id, $data)) {
            http_response_code(200); 
            echo json_encode(["message" => "User berhasil diperbarui."]);
        } else {
            http_response_code(503); 
            echo json_encode(["message" => "Gagal memperbarui user."]);
        }
    }

    private function delete($id) {
        if ($this->userModel->delete($id)) {
            http_response_code(200);
            echo json_encode(["message" => "User berhasil dihapus."]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Gagal menghapus user. ID mungkin tidak ditemukan."]);
        }
    }
}
?>