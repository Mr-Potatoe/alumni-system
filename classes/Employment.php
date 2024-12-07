<?php
class Employment {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function addEmployment($data) {
        $query = "INSERT INTO EmploymentHistory 
                 (user_id, company_name, position_title, employment_type, 
                  start_date, end_date, is_current, industry, location) 
                 VALUES 
                 (:user_id, :company_name, :position_title, :employment_type,
                  :start_date, :end_date, :is_current, :industry, :location)";
                  
        try {
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':company_name', $data['company_name'], PDO::PARAM_STR);
            $stmt->bindParam(':position_title', $data['position_title'], PDO::PARAM_STR);
            $stmt->bindParam(':employment_type', $data['employment_type'], PDO::PARAM_STR);
            $stmt->bindParam(':start_date', $data['start_date'], PDO::PARAM_STR);
            $stmt->bindParam(':end_date', $data['end_date'], PDO::PARAM_STR);
            $stmt->bindParam(':is_current', $data['is_current'], PDO::PARAM_BOOL);
            $stmt->bindParam(':industry', $data['industry'], PDO::PARAM_STR);
            $stmt->bindParam(':location', $data['location'], PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error in addEmployment: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateEmployment($employmentId, $data) {
        $query = "UPDATE EmploymentHistory 
                 SET company_name = :company_name,
                     position_title = :position_title,
                     employment_type = :employment_type,
                     start_date = :start_date,
                     end_date = :end_date,
                     is_current = :is_current,
                     industry = :industry,
                     location = :location
                 WHERE employment_id = :employment_id 
                 AND user_id = :user_id";
                 
        try {
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':employment_id', $employmentId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':company_name', $data['company_name'], PDO::PARAM_STR);
            $stmt->bindParam(':position_title', $data['position_title'], PDO::PARAM_STR);
            $stmt->bindParam(':employment_type', $data['employment_type'], PDO::PARAM_STR);
            $stmt->bindParam(':start_date', $data['start_date'], PDO::PARAM_STR);
            $stmt->bindParam(':end_date', $data['end_date'], PDO::PARAM_STR);
            $stmt->bindParam(':is_current', $data['is_current'], PDO::PARAM_BOOL);
            $stmt->bindParam(':industry', $data['industry'], PDO::PARAM_STR);
            $stmt->bindParam(':location', $data['location'], PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error in updateEmployment: " . $e->getMessage());
            return false;
        }
    }
    
    public function deleteEmployment($employmentId, $userId) {
        $query = "DELETE FROM EmploymentHistory 
                 WHERE employment_id = :employment_id 
                 AND user_id = :user_id";
                 
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employment_id', $employmentId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error in deleteEmployment: " . $e->getMessage());
            return false;
        }
    }
    
    public function getUserEmployment($userId) {
        $query = "SELECT * FROM EmploymentHistory 
                 WHERE user_id = :user_id 
                 ORDER BY is_current DESC, start_date DESC";
                 
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getUserEmployment: " . $e->getMessage());
            return [];
        }
    }
    
    public function getCurrentEmployment($userId) {
        $query = "SELECT * FROM EmploymentHistory 
                 WHERE user_id = :user_id 
                 AND is_current = 1 
                 LIMIT 1";
                 
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getCurrentEmployment: " . $e->getMessage());
            return false;
        }
    }
    
    public function getEmploymentById($employmentId, $userId) {
        $query = "SELECT * FROM EmploymentHistory 
                 WHERE employment_id = :employment_id 
                 AND user_id = :user_id";
                 
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':employment_id', $employmentId, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getEmploymentById: " . $e->getMessage());
            return false;
        }
    }
}
