<?php

require_once('Cool/BaseController.php');
require_once('Model/UserManager.php');

class MainController extends BaseController
{
    public function homeAction()
    {
        session_start();
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
            $users = $manager->registerUser($firstname,$lastname,$email,$password);
            $this->redirectToRoute('home');

        }            
            return $this->render('register.html.twig');
      
    }
    public function loginAction()
    {
        session_start();
        if (isset($_SESSION['u_id'])){
            $this->redirectToRoute('home');
        }
        $data = [];
        if(isset($_POST['email']) && isset($_POST['password'])){
            $email = $_POST['email'];
            $password = $_POST['password'];
            $manager = new UserManager();
            $loginUser = $manager->loginUser($email,$password);
            if($loginUser !== NULL){
            $data = [
                'data' => $loginUser
            ];
            } else {
                $this->redirectToRoute('home');
            }
       }    
            return $this->render('login.html.twig',$data);
    }
    public function logoutAction()
    {
        session_start();
        session_unset();
        session_destroy();
        $this->redirectToRoute('home');
    }
}


