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
                if (!is_dir("uploads/" . $_SESSION['u_id'] . "/")) {
                    mkdir("uploads/" . $_SESSION['u_id'] . "/", 0777, true) or die("couldn't mkdir");
                }
                if (is_dir("uploads/" . $_SESSION['u_id'] . "/")) { // si le dossier existe
                    $uploaddir = 'uploads/' . $_SESSION['u_id'] . '/'; //verifie si le dossier existe
                    $tempName = $_FILES['userfile']['name'];
                    $uploadfile = $uploaddir . $tempName;
                    if (file_exists($uploadfile)) { // si le fichier existe deja
                        $i = 1;
                        while (file_exists($uploadfile)) {
                            $name = "(" . $i . ")" . $tempName;
                            $uploadfile = $uploaddir . $name;
                            $i++;
                        }
                    } else {
                        $name = $tempName;
                        $uploadfile = $uploaddir . $name;
                    }
                    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                        $manager = new FileManager();
                        $manager->uploadFile($uploadfile,$_SESSION['u_id']);
                        $this->redirectToRoute('home');
                    } else {
                        echo "failed";
                    }
                }
            } else{
                return $this->render('upload.html.twig', $data);
            }  
        } else {
            $this->redirectToRoute('home');
        }

    }
    public function downloadAction()
    {
        session_start();
        if (isset($_SESSION['u_id'])) {
            $data['session'] = $_SESSION;
            if (isset($_POST['download'])) {
                $path = $_SESSION['path'].$_POST['download'];
                $manager = new FileManager();
                $manager->downloadFile($path);
                $this->redirectToRoute('home');
            }
            return $this->render('download.html.twig', $data);
        } else {
            $this->redirectToRoute('home');
        }
    }
}
