<?php
require_once ('Model/BaseManager.php');

class FileManager extends BaseManager{

    public function uploadFile($session,$file){
        $manager = new UserManager();
        $dir = $manager->verifyDir($_SESSION['u_id']);
        if ($dir === true) {
            $uploaddir = $_SESSION['path']; //verifie si le dossier existe
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
                self::insertFile($_FILES['userfile'], $name, $uploadfile, $_SESSION['u_id']);
                return true;
            } else {
                echo "failed";
            }
        } else {
            return false;
        }
    }
    public function insertFile($file,$name,$path,$id_user){

        $pdo = $this->setPdo();
        $stmt = $pdo->prepare('INSERT INTO files(id, name, extension, type, size, token, path, id_user, date_ajout) VALUES(NULL, :name, :extension, :type, :size, :token, :path, :id_user, :date_ajout)');
        $token = bin2hex(openssl_random_pseudo_bytes(8));
        $time = date('Y-m-d H:i:s');
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
        $stmt->bindParam(':name',$name);
        $stmt->bindParam(':extension', $fileExt);
        $stmt->bindParam(':type', $file['type']);
        $stmt->bindParam(':size',$file['size']);
        $stmt->bindParam(':token',$token);
        $stmt->bindParam(':path', $path);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':date_ajout', $time);
        $stmt->execute();
    }
    public function downloadFile($selectedFile){
        if (file_exists($selectedFile)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($selectedFile) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($selectedFile));
            readfile($selectedFile);
            exit;
        } else {
            return false;
        }
    }
    public function showFiles(){
        $pdo = $this->setPdo();
        $stmt = $pdo->prepare('SELECT * FROM `files` WHERE  id_user = :id');
        $stmt->bindParam(':id', $_SESSION['u_id']);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
}