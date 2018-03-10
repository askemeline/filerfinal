<?php

require_once('Cool/BaseController.php');
require_once('Model/UserManager.php');

class MainController extends BaseController
{
    public function homeAction()
    {
        $data = [];
        session_start();
        if (isset($_SESSION['u_id'])) {
            $data = [
                'session' => $_SESSION
            ];
        }
        return $this->render('home.html.twig',$data);
    }

    public function registerAction()
    {
        if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['password'])) {
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $manager = new UserManager();
            $users = $manager->registerUser($firstname, $lastname, $email, $password);
            $this->redirectToRoute('home');
        }
        return $this->render('register.html.twig');
    }

    public function loginAction()
    {
        session_start();
        if (isset($_SESSION['u_id'])) {
            var_dump($_SESSION);
        }
        $data = [];
        if (isset($_POST['email']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $manager = new UserManager();
            $loginUser = $manager->loginUser($email, $password);
            if ($loginUser !== NULL) {
                $data = [
                    'data' => $loginUser
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
        session_start(); //lance la session
        if (isset($_FILES['userfile'])) { // si l'input file nomme userfile est défini ou pas
            if (isset($_SESSION['u_id'])) {
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
                        $this->redirectToRoute('home');
                    } else {
                        echo "failed";
                    }
                }
            }
        } else {
            return $this->render('upload.html.twig');
        }

    }
    public function downloadAction(){
        session_start();
        if (isset($_POST['download'])) {
            $uploaddir = 'uploads/' . $_SESSION['u_id'] . '/'; //cree le debut du chemin du fichier
            $file = $_POST['download']; //recuperer le fichier de l'input download
            $uploadfile = $uploaddir . $file;
            if (file_exists($uploadfile)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($uploadfile) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($uploadfile));
                readfile($uploadfile);
                exit;
            }
        }
        return $this->render('download.html.twig');
    }
}