<?php
class User
{

    private $id = -1;
    private $username;
    private $email;
    private $hashPasswd;

    public static function loginUser(PDO $conn, $login, $passwd) {
        $query = "SELECT * FROM Users WHERE email = :login";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            ':login' => $login
        ]);
        if ($result && $stmt->rowCount() == 1) {
            $loggedUser = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($passwd, $loggedUser['hash_pass'])) {
                $user = new User();
                $user->id = $loggedUser['id'];
                $user->username = $loggedUser['username'];
                $user->email = $loggedUser['email'];
                $_SESSION['loggedUser'] = $user;
                header("Location:index.php");
            } else {
                $_SESSION['msg'] = 'Podane hasło jest niepoprawne';
                header("Location:index.php");
            }
        } else {
            $_SESSION['msg'] = "W bazie danych nie ma użytkownika $login!";
            header("Location:index.php");
        }
    }

    public static function loadUserById(PDO $conn, int $id) {
        $query = "SELECT * FROM Users WHERE id = :id";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([':id' => $id]);
        if ($result && $stmt->rowCount() == 1) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            $user = new User();
            $user->id = $userData['id'];
            $user->username = $userData['username'];
            $user->email = $userData['email'];
            $user->hassPasswd = $userData['hash_pass'];
            return $user;
        } else {
            return NULL;
        }
    }

    public static function loadUserByEmail(PDO $conn, $email) {
        $query = "SELECT * FROM Users WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() == 1) {
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            $user = new User();
            $user->id = $userData['id'];
            $user->username = $userData['username'];
            $user->email = $userData['email'];
            $user->hashPasswd = $userData['hash_pass'];
            return $user;
        } else {
            return NULL;
        }
    }

    public static function loadAllUsers(PDO $conn) {
        $query = "SELECT * FROM Users ORDER BY Register_date ASC";
        $result= $conn->query($query);
        $users = [];
        if ($result && $result->rowCount() > 0) {
            foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $userData) {
                $user = new User;
                $user->id = $userData['id'];
                $user->username = $userData['username'];
                $user->email = $userData['email'];
                $user->hashPasswd = $userData['hash_pass'];
                $users [] = $user;
            }
        }
        return $users;
    }

    public function __construct()
    {
        $this->username = '';
        $this->email = '';
        $this->hashPasswd = '';
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
    public function getUsername()
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

    /**
     * @param string $hassPasswd
     */
    public function setHashPasswd(string $hassPasswd)
    {
        $this->hashPasswd = password_hash($hassPasswd, PASSWORD_BCRYPT);
    }

    public function saveToDB(PDO $conn)
    {
        if ($this->id == -1 ) {
            //Nowy biekt - INSERT
            $query = "INSERT INTO Users(username, email, hash_pass, Register_date)
                          VALUES (:username, :email, :hash_pass, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':username' => $this->username,
                ':email' => $this->email,
                ':hash_pass' => $this->hashPasswd,
            ]);
            if ($stmt) {
                $this->id = $conn->lastInsertId();
            }
            return true;
        } else {
            //Obiekt już istnieje - UPDATE
            $query = "UPDATE Users SET username = :username, email = :email, hash_pass = :has_pass WHERE id = :id";
            $stmt = $conn->prepare($query);
            return $stmt->execute([
                ':username' => $this->username,
                ':email' => $this->email,
                ':hash_pass' => $this->hassPasswd,
                ':id' => $this->id
            ]);
        }
    }

    public function delete(PDO $conn) {
        if($this->id != -1) {
            $query = "DELETE FROM Users WHERE id = :id";
            $stmt= $conn->prepare($query);
            if($stmt->execute([':id' => $this->id])) {
                $this->id = -1;
                return true;
            }
        }
    }

    public function updateData(PDO $conn, User $newUser) {
        $query = "UPDATE Users SET username=:username WHERE email=:email";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            ':username' => $newUser->username,
            ':email' => $newUser->email
        ]);
        if ($result) {
            echo 'Dane zostały zaktualizowane';
        }
    }

    public function updatePassword(PDO $conn, User $newUser) {
        $query = "UPDATE Users SET hash_pass=:password WHERE id=:id";
        $stmt = $conn->prepare($query);
        $result =$stmt->execute([
            ':password' => $newUser->hashPasswd,
            ':id' => $newUser->id
        ]);
        if ($result) {
            echo 'Hasło zostało zmienione';
        }
    }
}