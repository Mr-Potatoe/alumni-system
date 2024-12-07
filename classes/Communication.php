<?php
class Communication {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getUserCommunications($userId) {
        $query = "SELECT cl.*, cr.delivery_status 
                 FROM CommunicationLog cl 
                 JOIN CommunicationRecipients cr ON cl.log_id = cr.log_id 
                 WHERE cr.user_id = :user_id 
                 ORDER BY cl.sent_timestamp DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getUserCommunications: " . $e->getMessage());
            return [];
        }
    }
}
