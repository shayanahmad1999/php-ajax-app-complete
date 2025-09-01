<?php

require_once __DIR__ . '/../config/database.php';

class User
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $phone;
    public $created_at;
    public $updated_at;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Create user
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (name, email, phone) 
              VALUES (:name, :email, :phone) 
              RETURNING id, created_at";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);

        if ($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $this->id = $row['id'];
                $this->created_at = $row['created_at'];
            }
            return true;
        }

        return false;
    }

    // Read all users
    public function readAll()
    {
        $query = "SELECT id, name, email, phone, created_at 
                  FROM " . $this->table_name . " 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Search users
    public function search($search_term)
    {
        $query = "SELECT id, name, email, phone, created_at 
                  FROM " . $this->table_name . " 
                  WHERE name ILIKE :search OR email ILIKE :search 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $search_term = "%{$search_term}%";
        $stmt->bindParam(":search", $search_term);
        $stmt->execute();

        return $stmt;
    }

    // Read single user
    public function readOne()
    {
        $query = "SELECT id, name, email, phone, created_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->email = $row['email'];
            $this->phone = $row['phone'];
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }

    // Update user
    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, email=:email, phone=:phone, updated_at=CURRENT_TIMESTAMP 
                  WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind values
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Delete user
    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Check if email exists
    public function emailExists()
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }
}
