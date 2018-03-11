<?php

require_once 'Cool/BaseController.php';
require_once 'Model/UserManager.php';
require_once 'Model/FileManager.php';

class MainController extends BaseController
{
    public function homeAction()
    {
        $data = [];
        session_start();
        if (isset($_SESSION['u_id'])) {
            $data['session'] = $_SESSION;
            $manager = new FileManager();
            $result = $manager->showFiles();
            $data['files'] = $result;
        }
        return $this->render('home.html.twig', $data);
    }

    public function registerAction()
    {
        $data = [];
        session_start();
        if (isset($_SESSION['u_id'])) {
            $this->redirectToRoute('home');
        }
        if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['password'])) {
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $manager = new UserManager();
            $users = $manager->registerUser($firstname, $lastname, $email, $password);
            if($users === true){
            error_log("[". date('Y-m-d H:i:s') . "] ".$email." viens de s'inscrire", 3, "log/access.log");  
            } else {
                $data['errors'] = "Something Bad Happend, Please Try later !";
                error_log("[". date('Y-m-d H:i:s') . "] "."l'inscription de ". $email . " a echouer", 3, "log/security.log");
                return $this->render('register.html.twig', $data);
            }
            $this->redirectToRoute('home');
        }
        return $this->render('register.html.twig', $data);
    }

    public function loginAction()
    {
        $data = [];
        session_start();
        if (isset($_SESSION['u_id'])) {
            $this->redirectToRoute('home');
            error_log("[". date('Y-m-d H:i:s') . "] "."l'utilisateur ". $_SESSION['u_email'] . " a tenter d'aller sur un lieu interdit", 3, "log/security.log");
        }
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $manager = new UserManager();
            $loginUser = $manager->loginUser($email, $password);
            if ($loginUser === false) {
                $data['errors'] = "Something Bad Happend, Please Try later !";
                
                error_log("[". date('Y-m-d H:i:s') . "] "."l'utilisateur ". $email . " a echouer la connexion", 3, "log/security.log");
            } else {
                error_log("[". date('Y-m-d H:i:s') . "] "."l'utilisateur ". $_SESSION['u_email'] . " s'est connecter", 3, "log/access.log");
                $this->redirectToRoute('home');
            }
        }
        return $this->render('login.html.twig', $data);
    }

    public function logoutAction()
    {
        session_start();
        error_log("[". date('Y-m-d H:i:s') . "] "."l'utilisateur ". $_SESSION['u_email'] . " s'est deconnecter", 3, "log/access.log");
        session_unset();
        session_destroy();
        $this->redirectToRoute('home');
    }

    public function uploadAction()
    {
        $data = [];
        session_start(); //lance la session
        if (isset($_SESSION['u_id'])) {
            $data['session'] = $_SESSION;
            if (isset($_FILES['userfile'])) { // si l'input file nomme userfile est défini ou pas
                $manager = new FileManager();
                $uploaded = $manager->uploadFile($_FILES['userfile'],$_POST['name']);
                if ($uploaded === true){
                    error_log("[". date('Y-m-d H:i:s') . "] "."l'utilisateur ". $_SESSION['u_email'] . " a upload ".$_FILES['userfile']['name'], 3, "log/access.log");
                    $this->redirectToRoute('home');
                }
                else{
                    $data['errors'] = "Something Bad Happend, Please Try later !"; 
                    error_log("[". date('Y-m-d H:i:s') . "] "."l'utilisateur ". $_SESSION['u_email'] . " a echouer l'upload ", 3, "log/security.log");
                    return $this->render('upload.html.twig', $data);
                }
            } else {
                return $this->render('upload.html.twig', $data);
            }
        } else {
            error_log("[". date('Y-m-d H:i:s') . "] "."un utilisateur a tenter d'acceder a cette page sans compte ".$_FILES['userfile']['name'], 3, "log/security.log");
            $this->redirectToRoute('home');
        }

    }
    public function editAction()
    {
        session_start();
        if (isset($_SESSION['u_id'])) {
            $selectedFile = str_replace('/', '', $_POST['secretName']);
            $path = $_SESSION['path'] . $_POST['secretName'];
            if (isset($_POST['downloadButton'])) {
                $manager = new FileManager();
               $downloaded = $manager->downloadFile($path);
               if ($downloaded === false) {
                $data['errors'] = "Something Bad Happend, Please Try later !";
                error_log("[". date('Y-m-d H:i:s') . "] "."l'utilisateur ". $_SESSION['u_email'] . " a echouer le download", 3, "log/security.log");
                return $this->render('edit.html.twig', $data);
                }
            } else if (isset($_POST['deleteButton'])) {
                $manager = new FileManager();
                $deleted = $manager->deleteFile($_POST['secretName'],$_POST['secretToken']);
                if ($deleted === false) {
                    $data['errors'] = "Something Bad Happend, Please Try later !";
                    error_log("[". date('Y-m-d H:i:s') . "] "."l'utilisateur ". $_SESSION['u_email'] . " a echouer le delete de ".$_POST['secretName'], 3, "log/security.log");
                    return $this->render('edit.html.twig', $data);
                    }
            } else if (isset($_POST['renameButton'])) {
                $manager = new FileManager();
                $renamed = $manager->renameFile($_POST['newName'],$_POST['secretName'],$_POST['secretToken']);
                if ($renamed === false) {
                    $data['errors'] = "Something Bad Happend, Please Try later !";
                    error_log("[". date('Y-m-d H:i:s') . "] "."l'utilisateur ". $_SESSION['u_email'] . " a echouer le rename de ".$_POST['secretName'], 3, "log/security.log");
                    return $this->render('edit.html.twig', $data);
                    }
            } else {
                $this->redirectToRoute('home');
            }
        } else {
            $this->redirectToRoute('home');
        }
        $this->redirectToRoute('home');
    }
}
