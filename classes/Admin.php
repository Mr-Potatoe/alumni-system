<?php
class Admin {
    private $conn;
    private $table = "Admins";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getDashboardStats() {
        $stats = [];
        
        // Total Alumni
        $query = "SELECT COUNT(*) as total FROM Users WHERE registration_status = 'Approved'";
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_alumni'] = $result['total'];

        // Employed Alumni
        $query = "SELECT COUNT(DISTINCT user_id) as employed 
                 FROM EmploymentHistory 
                 WHERE is_current = TRUE";
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['employed_alumni'] = $result['employed'];

        // Pending Approvals
        $query = "SELECT COUNT(*) as pending 
                 FROM Users 
                 WHERE registration_status = 'Pending'";
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['pending_approvals'] = $result['pending'];

        // Current Year Graduates
        $currentYear = date('Y');
        $query = "SELECT COUNT(*) as current_year 
                 FROM Users 
                 WHERE graduation_year = :year";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $currentYear);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['current_year_graduates'] = $result['current_year'];

        return $stats;
    }

    public function getRecentRegistrations($limit = 5) {
        $query = "SELECT user_id, first_name, last_name, graduation_year, registration_date 
                 FROM Users 
                 ORDER BY registration_date DESC 
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingApprovals($limit = 5) {
        $query = "SELECT user_id, first_name, last_name, email, registration_date 
                 FROM Users 
                 WHERE registration_status = 'Pending' 
                 ORDER BY registration_date ASC 
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approveUser($userId) {
        $query = "UPDATE Users 
                 SET registration_status = 'Approved' 
                 WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    public function rejectUser($userId) {
        $query = "UPDATE Users 
                 SET registration_status = 'Rejected' 
                 WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    public function getAllPendingUsers() {
        $query = "SELECT user_id, first_name, last_name, email, graduation_year, registration_date 
                 FROM Users 
                 WHERE registration_status = 'Pending' 
                 ORDER BY registration_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAlumni($search = '', $year = '', $employed = '') {
        $query = "SELECT u.*, 
                 (SELECT company_name FROM EmploymentHistory 
                  WHERE user_id = u.user_id AND is_current = TRUE LIMIT 1) as current_employment 
                 FROM Users u 
                 WHERE registration_status = 'Approved'";
        
        $params = [];
        
        if ($search) {
            $query .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        if ($year) {
            $query .= " AND graduation_year = :year";
            $params[':year'] = $year;
        }
        
        if ($employed !== '') {
            if ($employed == '1') {
                $query .= " AND EXISTS (SELECT 1 FROM EmploymentHistory 
                          WHERE user_id = u.user_id AND is_current = TRUE)";
            } else {
                $query .= " AND NOT EXISTS (SELECT 1 FROM EmploymentHistory 
                          WHERE user_id = u.user_id AND is_current = TRUE)";
            }
        }
        
        $query .= " ORDER BY last_name, first_name";
        
        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGraduationYears() {
        $query = "SELECT DISTINCT graduation_year 
                 FROM Users 
                 WHERE graduation_year IS NOT NULL 
                 ORDER BY graduation_year DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getEmploymentStatistics() {
        $query = "SELECT 
                    CASE 
                        WHEN e.employment_id IS NOT NULL THEN 'Employed'
                        ELSE 'Unemployed'
                    END as status,
                    COUNT(*) as count,
                    (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM Users)) as percentage
                FROM Users u
                LEFT JOIN EmploymentHistory e ON u.user_id = e.user_id 
                    AND e.is_current = TRUE
                GROUP BY 
                    CASE 
                        WHEN e.employment_id IS NOT NULL THEN 'Employed'
                        ELSE 'Unemployed'
                    END";
        
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getIndustryStatistics() {
        $query = "SELECT 
                    COALESCE(e.industry, 'Unknown') as industry,
                    COUNT(*) as count
                FROM Users u
                LEFT JOIN EmploymentHistory e ON u.user_id = e.user_id 
                    AND e.is_current = TRUE
                GROUP BY e.industry
                ORDER BY count DESC";
        
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getYearlyStatistics() {
        try {
            $query = "SELECT graduation_year as year, COUNT(*) as count
                     FROM Users
                     WHERE registration_status = 'Approved'
                     GROUP BY graduation_year
                     ORDER BY graduation_year DESC";
            
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getYearlyStatistics: " . $e->getMessage());
            return [];
        }
    }

    public function getAllAlumniData() {
        try {
            $query = "SELECT 
                        u.first_name,
                        u.middle_name,
                        u.last_name,
                        u.email,
                        u.contact_number,
                        u.graduation_year,
                        u.graduation_semester,
                        eh.company_name,
                        eh.position_title,
                        eh.industry,
                        ua.street_address
                     FROM Users u
                     LEFT JOIN EmploymentHistory eh ON u.user_id = eh.user_id AND eh.is_current = TRUE
                     LEFT JOIN UserAddresses ua ON u.user_id = ua.user_id AND ua.is_current = TRUE
                     WHERE u.registration_status = 'Approved'
                     ORDER BY u.last_name, u.first_name";
            
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllAlumniData: " . $e->getMessage());
            return [];
        }
    }

    public function exportReport($reportType) {
        // Implementation for exporting reports to Excel
        // You'll need to add PHPSpreadsheet or similar library
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        // ... implementation details
    }

    public function getSystemSettings() {
        $query = "SELECT * FROM SystemSettings LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSystemSettings($settings) {
        $query = "UPDATE SystemSettings SET
                 auto_approve = :auto_approve,
                 email_notifications = :email_notifications,
                 maintenance_mode = :maintenance_mode";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($settings);
    }

    public function verifyCurrentPassword($admin_id, $password) {
        $query = "SELECT password_hash FROM " . $this->table . "
                 WHERE admin_id = :admin_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":admin_id", $admin_id);
        $stmt->execute();
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return password_verify($password, $row['password_hash']);
        }
        return false;
    }

    public function updatePassword($admin_id, $new_password) {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $query = "UPDATE " . $this->table . "
                 SET password_hash = :password
                 WHERE admin_id = :admin_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":admin_id", $admin_id);
        
        return $stmt->execute();
    }

    public function validatePasswordStrength($password) {
        // Password must be at least 8 characters
        if (strlen($password) < 8) {
            return false;
        }
        
        // Must contain at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }
        
        // Must contain at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }
        
        // Must contain at least one number
        if (!preg_match('/[0-9]/', $password)) {
            return false;
        }
        
        // Must contain at least one special character
        if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
            return false;
        }
        
        return true;
    }

    public function checkPasswordHistory($admin_id, $new_password) {
        $query = "SELECT password_history FROM " . $this->table . "
                 WHERE admin_id = :admin_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":admin_id", $admin_id);
        $stmt->execute();
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $history = json_decode($row['password_history'], true) ?? [];
            
            foreach ($history as $old_hash) {
                if (password_verify($new_password, $old_hash)) {
                    return false; // Password was used before
                }
            }
            
            // Add new password to history, keep last 5
            $history[] = password_hash($new_password, PASSWORD_DEFAULT);
            if (count($history) > 5) {
                array_shift($history);
            }
            
            // Update password history
            $query = "UPDATE " . $this->table . "
                     SET password_history = :history
                     WHERE admin_id = :admin_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":history", json_encode($history));
            $stmt->bindParam(":admin_id", $admin_id);
            $stmt->execute();
        }
        
        return true;
    }

    public function authenticate($username, $password) {
        $query = "SELECT * FROM Admins WHERE username = :username AND is_active = TRUE";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password_hash'])) {
                return $row;
            }
        }
        return false;
    }


    public function logAudit($userId, $actionType, $details, $ipAddress) {
        $query = "INSERT INTO SystemAuditLog (
            user_id, action_type, action_details, ip_address
        ) VALUES (
            :user_id, :action_type, :details, :ip
        )";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            'user_id' => $userId,
            'action_type' => $actionType,
            'details' => $details,
            'ip' => $ipAddress
        ]);
    }

    public function getAlumniDetails($userId) {
        try {
            $query = "SELECT u.user_id,
                            u.first_name,
                            u.middle_name,
                            u.last_name,
                            u.email,
                            u.contact_number,
                            u.graduation_year,
                            u.graduation_semester,
                            ua.street_address as address,
                            eh.position_title as current_employment,
                            eh.company_name as company
                     FROM Users u 
                     LEFT JOIN UserAddresses ua ON u.user_id = ua.user_id AND ua.is_current = TRUE
                     LEFT JOIN EmploymentHistory eh ON u.user_id = eh.user_id AND eh.is_current = TRUE
                     WHERE u.user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['user_id' => $userId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return false;
            }
            
            // Ensure all fields exist with empty string defaults
            $defaultData = [
                'user_id' => $userId,
                'first_name' => '',
                'middle_name' => '',
                'last_name' => '',
                'email' => '',
                'contact_number' => '',
                'graduation_year' => '',
                'graduation_semester' => '',
                'address' => '',
                'current_employment' => '',
                'company' => ''
            ];
            
            // Merge the default data with actual results, replacing null values with empty strings
            return array_map(function($value) {
                return $value ?? '';
            }, array_merge($defaultData, $result));
            
        } catch (PDOException $e) {
            error_log("Error in getAlumniDetails: " . $e->getMessage());
            return false;
        }
    }

    public function updateAlumni($userId, $userData) {
        try {
            $query = "UPDATE Users SET 
                        first_name = :first_name,
                        middle_name = :middle_name,
                        last_name = :last_name,
                        email = :email,
                        contact_number = :contact_number,
                        graduation_year = :graduation_year,
                        graduation_semester = :graduation_semester
                     WHERE user_id = :user_id";
            
            $stmt = $this->conn->prepare($query);
            
            $result = $stmt->execute([
                'user_id' => $userId,
                'first_name' => $userData['first_name'],
                'middle_name' => $userData['middle_name'],
                'last_name' => $userData['last_name'],
                'email' => $userData['email'],
                'contact_number' => $userData['contact_number'],
                'graduation_year' => $userData['graduation_year'],
                'graduation_semester' => $userData['graduation_semester']
            ]);
            
            if (!$result) {
                error_log("Error updating alumni: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error in updateAlumni: " . $e->getMessage());
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public function updateAlumniAddress($userId, $addressData) {
        // First, set all addresses for this user to not current
        $query = "UPDATE UserAddresses SET is_current = FALSE WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        // Check if there's an existing current address
        $query = "SELECT address_id FROM UserAddresses 
                 WHERE user_id = :user_id AND address_type = :address_type";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            'user_id' => $userId,
            'address_type' => $addressData['address_type']
        ]);

        if ($stmt->rowCount() > 0) {
            // Update existing address
            $query = "UPDATE UserAddresses 
                     SET street_address = :street_address,
                         is_current = :is_current
                     WHERE user_id = :user_id AND address_type = :address_type";
        } else {
            // Insert new address
            $query = "INSERT INTO UserAddresses 
                     (user_id, address_type, street_address, is_current)
                     VALUES 
                     (:user_id, :address_type, :street_address, :is_current)";
        }

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($addressData);
    }

    public function updateAlumniEmployment($userId, $employmentData) {
        // Set all previous employment records to not current
        $query = "UPDATE EmploymentHistory SET is_current = FALSE WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['user_id' => $userId]);

        // Insert new current employment
        $query = "INSERT INTO EmploymentHistory 
                 (user_id, company_name, position_title, employment_type, is_current, start_date)
                 VALUES 
                 (:user_id, :company_name, :position_title, :employment_type, :is_current, CURRENT_DATE)";
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($employmentData);
    }

    public function getCurrentEmployment($userId) {
        try {
            $query = "SELECT * FROM EmploymentHistory 
                     WHERE user_id = :user_id 
                     AND is_current = TRUE 
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCurrentEmployment: " . $e->getMessage());
            return false;
        }
    }

    public function getCurrentAddress($userId) {
        try {
            $query = "SELECT * FROM UserAddresses 
                     WHERE user_id = :user_id 
                     AND is_current = TRUE 
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute(['user_id' => $userId]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getCurrentAddress: " . $e->getMessage());
            return false;
        }
    }

    public function getAllPendingApprovals() {
        try {
            $query = "SELECT user_id, first_name, last_name, email, graduation_year, registration_date 
                     FROM Users 
                     WHERE registration_status = 'Pending' 
                     ORDER BY registration_date ASC";
            
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllPendingApprovals: " . $e->getMessage());
            return [];
        }
    }

    public function getAllRegistrations() {
        try {
            $query = "SELECT user_id, first_name, last_name, email, graduation_year, 
                            registration_date, registration_status 
                     FROM Users 
                     ORDER BY registration_date DESC";
            
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllRegistrations: " . $e->getMessage());
            return [];
        }
    }

    public function sendAnnouncement($data) {
        try {
            // Start transaction
            $this->conn->beginTransaction();
            
            // Insert into CommunicationLog
            $logQuery = "INSERT INTO CommunicationLog (sender_admin_id, communication_type, subject, 
                         message_body, recipient_group) 
                         VALUES (:admin_id, 'Announcement', :subject, :message, :recipient_group)";
            
            $logStmt = $this->conn->prepare($logQuery);
            $logStmt->bindValue(':admin_id', $_SESSION['admin_id']);
            $logStmt->bindValue(':subject', $data['subject']);
            $logStmt->bindValue(':message', $data['message']);
            $logStmt->bindValue(':recipient_group', $data['recipient_group']);
            $logStmt->execute();
            
            $logId = $this->conn->lastInsertId();
            
            // Get recipients based on group
            $recipientQuery = "SELECT user_id FROM Users WHERE is_active = 1";
            
            if ($data['recipient_group'] === 'Specific Graduation Year' && !empty($data['graduation_year'])) {
                $recipientQuery .= " AND graduation_year = :graduation_year";
            }
            
            $recipientStmt = $this->conn->prepare($recipientQuery);
            if ($data['recipient_group'] === 'Specific Graduation Year' && !empty($data['graduation_year'])) {
                $recipientStmt->bindValue(':graduation_year', $data['graduation_year']);
            }
            $recipientStmt->execute();
            
            // Insert into CommunicationRecipients
            $insertRecipientQuery = "INSERT INTO CommunicationRecipients (log_id, user_id, delivery_status) 
                                    VALUES (:log_id, :user_id, 'Sent')";
            $insertRecipientStmt = $this->conn->prepare($insertRecipientQuery);
            
            while ($recipient = $recipientStmt->fetch(PDO::FETCH_ASSOC)) {
                $insertRecipientStmt->bindValue(':log_id', $logId);
                $insertRecipientStmt->bindValue(':user_id', $recipient['user_id']);
                $insertRecipientStmt->execute();
            }
            
            // Log the action in SystemAuditLog
            $auditQuery = "INSERT INTO SystemAuditLog (user_id, action_type, action_details, ip_address) 
                           VALUES (:admin_id, 'Send Announcement', :details, :ip)";
            
            $auditStmt = $this->conn->prepare($auditQuery);
            $auditStmt->bindValue(':admin_id', $_SESSION['admin_id']);
            $auditStmt->bindValue(':details', "Sent announcement: {$data['subject']} to {$data['recipient_group']}");
            $auditStmt->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
            $auditStmt->execute();
            
            $this->conn->commit();
            return true;
            
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error sending announcement: " . $e->getMessage());
            return false;
        }
    }

    public function getYearlyEmploymentTrends() {
        $query = "SELECT 
            e.graduation_year as year,
            (COUNT(CASE WHEN e.employment_status = 'Employed' THEN 1 END) * 100.0 / COUNT(*)) as rate
        FROM Users u
        LEFT JOIN EmploymentHistory e ON u.user_id = e.user_id
        WHERE e.graduation_year IS NOT NULL
        GROUP BY e.graduation_year
        ORDER BY e.graduation_year DESC
        LIMIT 5";

        try {
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting yearly employment trends: " . $e->getMessage());
            return [];
        }
    }

    public function getIndustries() {
        $query = "SELECT DISTINCT industry 
                  FROM EmploymentHistory 
                  WHERE industry IS NOT NULL 
                  ORDER BY industry";

        try {
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error getting industries: " . $e->getMessage());
            return [];
        }
    }

    public function getLocations() {
        $query = "SELECT DISTINCT city 
                  FROM UserAddresses 
                  WHERE city IS NOT NULL 
                  ORDER BY city";

        try {
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error getting locations: " . $e->getMessage());
            return [];
        }
    }

    public function getAlumniDirectory($year = null, $industry = null, $location = null) {
        $query = "SELECT DISTINCT
            u.user_id,
            u.first_name,
            u.last_name,
            u.email,
            u.graduation_year,
            u.graduation_semester,
            u.contact_number,
            e.position_title as current_position,
            e.company_name as company,
            e.industry,
            a.city as location
        FROM Users u
        LEFT JOIN (
            SELECT user_id, position_title, company_name, industry
            FROM EmploymentHistory
            WHERE is_current = TRUE
        ) e ON u.user_id = e.user_id
        LEFT JOIN (
            SELECT user_id, city
            FROM UserAddresses
            WHERE is_current = TRUE
        ) a ON u.user_id = a.user_id
        WHERE u.registration_status = 'Approved'";

        $params = [];

        if ($year) {
            $query .= " AND u.graduation_year = :year";
            $params[':year'] = $year;
        }

        if ($industry) {
            $query .= " AND e.industry = :industry";
            $params[':industry'] = $industry;
        }

        if ($location) {
            $query .= " AND a.city = :location";
            $params[':location'] = $location;
        }

        $query .= " ORDER BY u.graduation_year DESC, u.last_name ASC";

        try {
            // Debug: Print the final query
            error_log("Query: " . $query);
            error_log("Params: " . print_r($params, true));

            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug: Print the number of results
            error_log("Number of results: " . count($results));
            
            return $results;
        } catch (PDOException $e) {
            error_log("Error getting alumni directory: " . $e->getMessage());
            return [];
        }
    }

    public function login($username, $password) {
        try {
            $query = "SELECT admin_id, username, password_hash, role, department_id, 
                             first_name, last_name 
                      FROM Admins 
                      WHERE username = :username AND is_active = 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':username', $username);
            $stmt->execute();
            
            if ($admin = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $admin['password_hash'])) {
                    // Update last login
                    $this->updateLastLogin($admin['admin_id']);
                    
                    return [
                        'success' => true,
                        'admin_id' => $admin['admin_id'],
                        'role' => $admin['role'],
                        'name' => $admin['first_name'] . ' ' . $admin['last_name'],
                        'department_id' => $admin['department_id']
                    ];
                }
            }
            
            return ['success' => false];
            
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false];
        }
    }

    private function updateLastLogin($admin_id) {
        try {
            $query = "UPDATE Admins SET last_login = CURRENT_TIMESTAMP 
                      WHERE admin_id = :admin_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':admin_id', $admin_id);
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating last login: " . $e->getMessage());
        }
    }

    
    // public function updateLastLogin($admin_id) {
    //     $query = "UPDATE " . $this->table . "
    //              SET last_login = CURRENT_TIMESTAMP
    //              WHERE admin_id = :admin_id";
        
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bindParam(":admin_id", $admin_id);
    //     return $stmt->execute();
    // }

    public function getAllDepartments() {
        try {
            $query = "SELECT * FROM Departments ORDER BY department_name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting departments: " . $e->getMessage());
            return [];
        }
    }

    public function createDepartmentHead($data) {
        try {
            // Check if username or email already exists
            $query = "SELECT COUNT(*) FROM Admins WHERE username = :username OR email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':username', $data['username']);
            $stmt->bindValue(':email', $data['email']);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                return [
                    'success' => false,
                    'message' => 'Username or email already exists'
                ];
            }

            // Start transaction
            $this->conn->beginTransaction();

            // Create the Department Head account
            $query = "INSERT INTO Admins (username, email, password_hash, first_name, last_name, 
                                        role, department_id, is_active) 
                     VALUES (:username, :email, :password_hash, :first_name, :last_name, 
                             'Department Head', :department_id, 1)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':username', $data['username']);
            $stmt->bindValue(':email', $data['email']);
            $stmt->bindValue(':password_hash', password_hash($data['password'], PASSWORD_DEFAULT));
            $stmt->bindValue(':first_name', $data['first_name']);
            $stmt->bindValue(':last_name', $data['last_name']);
            $stmt->bindValue(':department_id', $data['department_id']);
            $stmt->execute();

            // Log the action
            $admin_id = $this->conn->lastInsertId();
            $query = "INSERT INTO SystemAuditLog (user_id, action_type, action_details, ip_address) 
                     VALUES (:admin_id, 'Create Department Head', :details, :ip)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':admin_id', $_SESSION['admin_id']);
            $stmt->bindValue(':details', "Created Department Head account for {$data['username']}");
            $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
            $stmt->execute();

            $this->conn->commit();
            return [
                'success' => true,
                'message' => 'Department Head created successfully'
            ];

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error creating department head: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while creating the Department Head'
            ];
        }
    }
}
