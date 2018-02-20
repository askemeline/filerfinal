<?php

require_once('Cool/DBManager.php');

class UserManager {
   public function getUser($firstname,$lastname,$username,$email,$password){

    $dbm = DBManager::getInstance();
    $pdo = $dbm->getPdo();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $time =  date('Y-m-d H:i:s');
        $stmt = $pdo->prepare('INSERT INTO users(id, creation, firstname, lastname, username, email, password) VALUES(NULL, :creation ,:firstname, :lastname, :username, :email, :password');
        //$req->execute(array(
            //':creation' => $time,
            //':firstname' => $firstname,
            //':lastname' => $lastname,
            //':username' => $username,
            //':email' => $email,
            //':password' => $password));
            $stmt->bindParam(':creation', $time);
            $stmt->bindParam(':firstname', $firstname);
            $stmt->bindParam(':lastname', $lastname);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
          
            var_dump($stmt->execute());
    }
    
}