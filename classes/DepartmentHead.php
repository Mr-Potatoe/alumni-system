<?php
class DepartmentHead {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDepartmentStats($department_id) {
        $stats = [
            'total_alumni' => 0,
            'employed_alumni' => 0,
            'recent_graduates' => 0
        ];

        try {
            // Get total alumni in department
            $query = "SELECT COUNT(*) as total FROM Users 
                     WHERE department_id = :dept_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':dept_id', $department_id);
            $stmt->execute();
            $stats['total_alumni'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Get employed alumni
            $query = "SELECT COUNT(DISTINCT u.user_id) as employed 
                     FROM Users u 
                     JOIN EmploymentHistory eh ON u.user_id = eh.user_id 
                     WHERE u.department_id = :dept_id 
                     AND eh.is_current = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':dept_id', $department_id);
            $stmt->execute();
            $stats['employed_alumni'] = $stmt->fetch(PDO::FETCH_ASSOC)['employed'];

            // Get recent graduates (last year)
            $query = "SELECT COUNT(*) as recent FROM Users 
                     WHERE department_id = :dept_id 
                     AND graduation_year = YEAR(CURRENT_DATE)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':dept_id', $department_id);
            $stmt->execute();
            $stats['recent_graduates'] = $stmt->fetch(PDO::FETCH_ASSOC)['recent'];

            return $stats;
        } catch (PDOException $e) {
            error_log("Error getting department stats: " . $e->getMessage());
            return $stats;
        }
    }

    public function sendDepartmentAnnouncement($data) {
        try {
            $this->conn->beginTransaction();
            
            // Insert into CommunicationLog
            $logQuery = "INSERT INTO CommunicationLog (sender_admin_id, communication_type, 
                        subject, message_body, recipient_group) 
                        VALUES (:admin_id, 'Announcement', :subject, :message, 'Department')";
            
            $logStmt = $this->conn->prepare($logQuery);
            $logStmt->bindValue(':admin_id', $_SESSION['admin_id']);
            $logStmt->bindValue(':subject', $data['subject']);
            $logStmt->bindValue(':message', $data['message']);
            $logStmt->execute();
            
            $logId = $this->conn->lastInsertId();
            
            // Get department recipients
            $recipientQuery = "SELECT user_id FROM Users 
                             WHERE department_id = :dept_id AND is_active = 1";
            $recipientStmt = $this->conn->prepare($recipientQuery);
            $recipientStmt->bindValue(':dept_id', $_SESSION['department_id']);
            $recipientStmt->execute();
            
            // Insert recipients
            $insertRecipientQuery = "INSERT INTO CommunicationRecipients 
                                   (log_id, user_id, delivery_status) 
                                   VALUES (:log_id, :user_id, 'Sent')";
            $insertRecipientStmt = $this->conn->prepare($insertRecipientQuery);
            
            while ($recipient = $recipientStmt->fetch(PDO::FETCH_ASSOC)) {
                $insertRecipientStmt->bindValue(':log_id', $logId);
                $insertRecipientStmt->bindValue(':user_id', $recipient['user_id']);
                $insertRecipientStmt->execute();
            }
            
            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error sending department announcement: " . $e->getMessage());
            return false;
        }
    }

    public function getDepartmentAlumni($department_id, $filters = []) {
        try {
            $query = "SELECT u.*, 
                      (SELECT company_name FROM EmploymentHistory 
                       WHERE user_id = u.user_id AND is_current = 1 
                       LIMIT 1) as current_employment 
                      FROM Users u 
                      WHERE u.department_id = :dept_id";
            
            if (!empty($filters['graduation_year'])) {
                $query .= " AND u.graduation_year = :grad_year";
            }
            
            if (!empty($filters['employment_status'])) {
                if ($filters['employment_status'] === 'employed') {
                    $query .= " AND EXISTS (SELECT 1 FROM EmploymentHistory eh 
                               WHERE eh.user_id = u.user_id AND eh.is_current = 1)";
                } else {
                    $query .= " AND NOT EXISTS (SELECT 1 FROM EmploymentHistory eh 
                               WHERE eh.user_id = u.user_id AND eh.is_current = 1)";
                }
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':dept_id', $department_id);
            
            if (!empty($filters['graduation_year'])) {
                $stmt->bindValue(':grad_year', $filters['graduation_year']);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error getting department alumni: " . $e->getMessage());
            return [];
        }
    }

    public function getEmploymentStats($department_id) {
        try {
            $stats = [
                'employment_rate' => 0,
                'avg_time_to_employment' => 0,
                'top_industry' => '',
                'employment_types' => [],
                'industries' => []
            ];
            
            // Get employment rate
            $query = "SELECT 
                        COUNT(DISTINCT u.user_id) as total_alumni,
                        COUNT(DISTINCT CASE WHEN eh.is_current = 1 THEN u.user_id END) as employed_alumni
                      FROM Users u 
                      LEFT JOIN EmploymentHistory eh ON u.user_id = eh.user_id
                      WHERE u.department_id = :dept_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':dept_id', $department_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stats['employment_rate'] = $result['total_alumni'] > 0 
                ? round(($result['employed_alumni'] / $result['total_alumni']) * 100) 
                : 0;
            
            // Get employment types distribution
            $query = "SELECT employment_type, COUNT(*) as count
                     FROM EmploymentHistory eh
                     JOIN Users u ON eh.user_id = u.user_id
                     WHERE u.department_id = :dept_id AND eh.is_current = 1
                     GROUP BY employment_type";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':dept_id', $department_id);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['employment_types'][$row['employment_type']] = $row['count'];
            }
            
            // Get industry distribution
            $query = "SELECT industry, COUNT(*) as count
                     FROM EmploymentHistory eh
                     JOIN Users u ON eh.user_id = u.user_id
                     WHERE u.department_id = :dept_id AND eh.is_current = 1
                     GROUP BY industry
                     ORDER BY count DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':dept_id', $department_id);
            $stmt->execute();
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $stats['industries'][$row['industry']] = $row['count'];
            }
            
            // Get top industry
            $stats['top_industry'] = array_key_first($stats['industries']) ?? 'N/A';
            
            return $stats;
            
        } catch (PDOException $e) {
            error_log("Error getting employment stats: " . $e->getMessage());
            return $stats;
        }
    }

    public function getAlumniDetails($userId, $departmentId) {
        $query = "SELECT u.*, a.* 
                  FROM users u 
                  JOIN alumni a ON u.user_id = a.user_id 
                  WHERE u.user_id = ? AND a.department_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $userId, $departmentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
} 