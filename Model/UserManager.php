<?php

require_once('Cool/DBManager.php');

class UserManager {

    public function setPdo(){
        $dbm = DBManager::getInstance();
        $pdo = $dbm->getPdo();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
        }
    
    public function registerUser($firstname,$lastname,$email,$password){
        $pdo = self::setPdo();
        $stmt = $pdo->prepare('INSERT INTO users(id, creation, firstname, lastname, email, password) VALUES(NULL, :creation ,:firstname, :lastname, :email, :password)');
        $stmt->bindParam(':creation', date('Y-m-d H:i:s'));
        $stmt->bindParam(':firstname', $firstname);
        $stmt->bindParam(':lastname', $lastname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
    }
    public function loginUser($email,$password){

        $pdo = self::setPdo();
        $stmt = $pdo->prepare('SELECT * FROM `users` WHERE email = :email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $count = $stmt->rowCount();
        if($count != 1){
            $error = "Invalid username or password";
            return $error;
        } else {
            $result = $stmt->fetch();
            $hash = $result['password'];
            if (password_verify($password, $hash)) {
                $_SESSION['u_id'] = $result['id'];
                $_SESSION['u_first'] = $result['firstname'];
                $_SESSION['u_last'] = $result['lastname'];
                $_SESSION['u_email'] = $result['email'];
            } else {
                $error = "Invalid username or password";
                return $error;
            }
        }
    }
    
}