<?php

require_once('Cool/BaseController.php');
require_once('Model/UserManager.php');

class MainController extends BaseController
{
    public function homeAction()
    {
        return $this->render('home.html.twig');
    }
    public function registerAction()
    {
    
        if(isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])){
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $manager = new UserManager();
            $users = $manager->getUser($firstname,$lastname,$username,$email,$password);
            $this->redirectToRoute('home');

        }            
            return $this->render('register.html.twig');
      
    }

}


