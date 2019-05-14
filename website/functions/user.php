<?php
class User
{
    private $db;

    function __construct($conn) {
        //Use construct so we can use the current connection easily.
        $this->db = $conn;
    }

    public function register($firstname, $middlename, $lastname, $email, $phonenumber, $address, $city, $postalcode, $password){
        try {
            //Use options that denotes the algorithmic cost, salt is Deprecated in PHP 7.0 so don't use that!
            $options = [
                'cost' => 16
            ];
            //Hash het wachtwoord met PHP hash functie.
            $hashed_pass = password_hash($password, PASSWORD_BCRYPT, $options);

            //Voeg gebruiker toe aan de database.
            $stmt = $this->db->prepare("INSERT INTO customer(firstname, middlename, lastname, email, phonenumber, address, city, postalcode, password) 
                                                       VALUES(:firstname, :middlename, :lastname, :email, :phonenumber, :address, :city, :postalcode, :password)");

            $stmt->bindparam(":firstname", $firstname);
            $stmt->bindparam(":middlename", $middlename);
            $stmt->bindparam(":lastname", $lastname);
            $stmt->bindparam(":email", $email);
            $stmt->bindparam(":phonenumber", $phonenumber);
            $stmt->bindparam(":address", $address);
            $stmt->bindparam(":city", $city);
            $stmt->bindparam(":postalcode", $postalcode);
            $stmt->bindparam(":password", $hashed_pass);
            $stmt->execute();

            return $stmt;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function login($email, $password) {
        try {
            //Get Login details by email.
            $stmt = $this->db->prepare("SELECT * FROM customer WHERE email = :email LIMIT 1");
            $stmt->execute(array(':email' => $email));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

            //Check if query returns minimal 1 row
            if($stmt->rowCount() > 0) {
                //Check if hashed password is true, otherwise return false.
                if(password_verify($password, $userRow['password'])) {
                    $_SESSION['user_session'] = $userRow['ID'];
                    return true;
                } else {
                    return false;
                }
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function is_loggedin() {
        //Check if there is a SESSION is set.
        if(isset($_SESSION['user_session'])) {
            return true;
        }
    }

    public function redirect($url) {
        //Redirect functie.
        header("Location: $url");
    }

    public function logout() {
        //Verwijder alle sessies en haal usersessie weg.
        session_destroy();
        unset($_SESSION['user_session']);
        return true;
    }

    public function has_role($role) {
        //Check if user is logged in and get User ID from session.
        //Get the user role of current user, so function can be used by a parameter with the user role that should have access to a page etc.
        if($this->is_loggedin()) {
            $userID = $_SESSION['user_session'];
            $stmt = $this->db->prepare("SELECT c.*, r.name AS role FROM customer AS c INNER JOIN roles AS r ON r.ID = c.RoleID WHERE c.ID = :userid LIMIT 1");
            $stmt->execute(array(':userid' => $userID));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() > 0) {
                if($userRow['role'] === $role) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}
