<?php
class Address {
    private $conn;
    private $table = 'UserAddresses';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addAddress($data) {
        // If this is set as current address, unset all other current addresses
        if ($data['is_current']) {
            $this->unsetCurrentAddresses($data['user_id']);
        }

        $query = "INSERT INTO " . $this->table . "
                (user_id, address_type, street_address, city, state, country, 
                postal_code, is_current)
                VALUES
                (:user_id, :type, :street, :city, :state, :country, 
                :postal, :current)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $data['user_id']);
        $stmt->bindParam(":type", $data['address_type']);
        $stmt->bindParam(":street", $data['street_address']);
        $stmt->bindParam(":city", $data['city']);
        $stmt->bindParam(":state", $data['state']);
        $stmt->bindParam(":country", $data['country']);
        $stmt->bindParam(":postal", $data['postal_code']);
        $stmt->bindParam(":current", $data['is_current']);

        return $stmt->execute();
    }

    private function unsetCurrentAddresses($user_id) {
        $query = "UPDATE " . $this->table . "
                SET is_current = FALSE
                WHERE user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    public function getUserAddresses($user_id) {
        $query = "SELECT * FROM " . $this->table . "
                WHERE user_id = :user_id
                ORDER BY is_current DESC, address_type";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrentAddress($user_id) {
        $query = "SELECT * FROM " . $this->table . "
                WHERE user_id = :user_id AND is_current = TRUE
                LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 