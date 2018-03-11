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
        }
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $manager = new UserManager();
            $loginUser = $manager->loginUser($email, $password);
            if ($loginUser !== null) {
                $data = [
                    'data' => $loginUser,
                ];
            } else {
                $this->redirectToRoute('home');
            }
        }
        return $this->render('login.html.twig', $data);
    }

    public function logoutAction()
    {
        session_start();
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
                $uploaded = $manager->uploadFile($_FILES['userfile']);
                if ($uploaded === true){
                    $this->redirectToRoute('home');
                }
            } else {
                return $this->render('upload.html.twig', $data);
            }
        } else {
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
                $manager->downloadFile($path);
            } else if (isset($_POST['deleteButton'])) {
                $manager = new FileManager();
                $manager->deleteFile($_POST['secretName'],$_POST['secretToken']);
            } else if (isset($_POST['renameButton'])) {
                $manager = new FileManager();
                $manager->renameFile($path);
            } else {
                $this->redirectToRoute('home');
            }
        } else {
            $this->redirectToRoute('home');
        }
    }
}
