<?php

namespace App\Model;

use PDO;

class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function emailExists(string $email): bool
    {
        $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    public function createUser(string $pseudo, string $email, string $password): bool
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO users (pseudo, email, password_hash, credits, role_id)
            VALUES (:pseudo, :email, :password_hash, :credits, :role_id)
        ";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':pseudo'        => $pseudo,
            ':email'         => $email,
            ':password_hash' => $hash,
            ':credits'       => 20,   
            ':role_id'       => 1,    
        ]);
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findById(int $id): ?array
    {
        $sql = "
            SELECT id, pseudo, email, photo, rating, credits
            FROM users
            WHERE id = :id
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}
