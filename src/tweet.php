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

    public function saveToDB(PDO $conn)
    {
        if ($this->id != -1) {
            //Nowy obiekt robimy insert
            $sql = "INSERT INTO Users (username, email, hash_pass)
                VALUES (:username, :email. :hash_pass)";

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
            ////obiekt juz sitnieje robimy update
        }
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

    public function setPassword($string, $password)

    {
        $this->hashPass = password_hash($password, PASSWORD_BCRYPT);
    }

}