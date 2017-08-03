<?php

class User
{
    private $id = -1;
    private $username;
    private $email;
    private $hashPass;


    public function __construct()
    {
        $this->username = '';
        $this->email = '';
        $this->hashPass = '';
    }

    public static function loadAllUsers(PDO $conn)

    {
        $sql = "SELECT * FROM Users ORDER BY id ASC";

        $result = $conn->query($sql);
        $users = [];

        if ($result && $result->rowCount() > 0) {
            foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $userData) {
                $user = new User();

                $user->id = $userData['id'];
                $user->username = $userData['username'];
                $user->email = $userData['email'];
                $user->hashPass = $userData['hash_pass'];

                $users[] = $user;
            }
        }

        return $users;
    }

    public static function loadUserById(PDO $conn, int $id)

    {
        $sql = "SELECT * FROM Users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute(['id' => $id]);

        if ($result && $stmt->rowCount() == 1) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            $user = new User();

            $user->id = $userData['id'];
            $user->username = $userData['username'];
            $user->email = $userData['email'];
            $user->hashPass = $userData['hash_pass'];

            return $user;
        } else {
            return  null;
        }
    }

    public function saveToDB(PDO $conn)
    {
        if ($this->id == -1) { //return -1 == $this->>id;
            //Nowy obiekt robimy insert
            $sql = "INSERT INTO Users (username, email, hash_pass)
                VALUES (:username, :email, :hash_pass)";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                'username' => $this->username,
                'email' => $this->email,
                'hash_pass' => $this->hashPass,
            ]);


            //jesli zapis sie udal
            $this->id = $conn->lastInsertId();

            return true;

        } else {
            $sql = "UPDATE Users SET username = :username, email = :email, hash_pass = :hash_pass WHERE id = :id";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                ':username' => $this->username,
                ':email' => $this->email,
                ':hash_pass' => $this->hashPass,
                ':id' => $this->id
            ]);
        }
    }

    public function isNew()
    {
        return -1 == $this->id;
    }


    public function delete(PDO $conn)

    {
        if (!$this->isNew()) {
            $sql = "DELETE FROM Users WHERE id = :id";
            $stmt = $conn->prepare($sql);

            if ($stmt->execute(['id' => $this->id])) {
                $this->id = -1;

                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function setPassword(string $password)

    {
        $this->hashPass = password_hash($password, PASSWORD_BCRYPT);
    }

}