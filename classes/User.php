<?php
class User {
    private $conn;
    private $table = 'Users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        $query = "SELECT user_id, email, password_hash, first_name, last_name 
                 FROM " . $this->table . " 
                 WHERE email = :email AND is_active = TRUE";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password_hash'])) {
                return $row;
            }
        }
        return false;
    }

    public function register($data) {
        $query = "INSERT INTO " . $this->table . "
                (email, password_hash, first_name, last_name, middle_name, graduation_year, department_id)
                VALUES
                (:email, :password, :fname, :lname, :mname, :grad_year, :department_id)";

        $stmt = $this->conn->prepare($query);
        
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":fname", $data['first_name']);
        $stmt->bindParam(":lname", $data['last_name']);
        $stmt->bindParam(":mname", $data['middle_name']);
        $stmt->bindParam(":grad_year", $data['graduation_year']);
        $stmt->bindParam(":department_id", $data['department_id']);

        return $stmt->execute();
    }

    public function requestPasswordReset($email) {
        $query = "SELECT user_id, first_name 
                  FROM " . $this->table . " 
                  WHERE email = :email AND is_active = TRUE";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            // Here you would typically:
            // 1. Generate a reset token
            // 2. Save it to the database
            // 3. Send an email with the reset link
            // For now, we'll just return true
            return true;
        }
        return false;
    }

    public function getUserById($user_id) {
        $query = "SELECT * FROM " . $this->table . " 
                 WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($user_id, $data) {
        $query = "UPDATE " . $this->table . "
                SET first_name = :fname,
                    last_name = :lname,
                    middle_name = :mname,
                    contact_number = :contact,
                    birth_date = :bdate,
                    gender = :gender,
                    graduation_semester = :semester
                WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":fname", $data['first_name']);
        $stmt->bindParam(":lname", $data['last_name']);
        $stmt->bindParam(":mname", $data['middle_name']);
        $stmt->bindParam(":contact", $data['contact_number']);
        $stmt->bindParam(":bdate", $data['birth_date']);
        $stmt->bindParam(":gender", $data['gender']);
        $stmt->bindParam(":semester", $data['graduation_semester']);
        $stmt->bindParam(":user_id", $user_id);

        return $stmt->execute();
    }

    public function verifyCurrentPassword($user_id, $password) {
        $query = "SELECT password_hash FROM " . $this->table . "
                 WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        
        if($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return password_verify($password, $row['password_hash']);
        }
        return false;
    }

    public function updatePassword($user_id, $new_password) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $query = "UPDATE " . $this->table . "
                 SET password_hash = :password
                 WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":user_id", $user_id);
        
        return $stmt->execute();
    }
}
