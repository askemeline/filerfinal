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
            $this->redirectToRoute('home');
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
        session_start();
        if (isset($_FILES['userfile'])) {
            if (isset($_SESSION['u_id'])) {
                if (!is_dir("uploads/" . $_SESSION['u_id'] . "/")) {
                    mkdir("uploads/" . $_SESSION['u_id'] . "/", 0777, true) or die("couldn't mkdir");
                    echo "created a dir";
                }
                if (is_dir("uploads/" . $_SESSION['u_id'] . "/")) {
                    $uploaddir = 'uploads/' . $_SESSION['u_id'] . '/';
                    $tempName = $_FILES['userfile']['name'];
                    $uploadfile = $uploaddir . $tempName;

                    if (file_exists($uploadfile)) {
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
                    var_dump($_FILES['userfile']);
                    var_dump($_FILES['userfile']['errors']);
                    var_dump($_FILES['userfile']['error']);
                    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
                        var_dump($_FILES['userfile']['tmp_name']);
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
}