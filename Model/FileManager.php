<?php
require_once 'Model/BaseManager.php';

class FileManager extends BaseManager
{

    public function uploadFile($file,$name)
    {
        $manager = new UserManager();
        $dir = $manager->verifyDir($_SESSION['u_id']);
        if ($dir === true) {
            $uploaddir = $_SESSION['path']; //verifie si le dossier existe
            if(strlen($name) > 1){
                $tempName = $name;   
            } else {
                $tempName = $_FILES['userfile']['name'];
            }
            $tempName = str_replace('/', '', htmlentities($tempName));
            $tempName = str_replace('..', '', $tempName);
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
                self::insertFile($_FILES['userfile'], $name, $uploadfile);
                return true;
            } else {
                echo "failed";
            }
        } else {
            return false;
        }
    }
    public function insertFile($file, $name, $path)
    {
        $pdo = $this->setPdo();
        $stmt = $pdo->prepare('INSERT INTO files(id, name, extension, type, size, token, path, id_user, date_ajout) VALUES(NULL, :name, :extension, :type, :size, :token, :path, :id_user, :date_ajout)');
        $token = bin2hex(openssl_random_pseudo_bytes(8));
        $time = date('Y-m-d H:i:s');
        $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':extension', $fileExt);
        $stmt->bindParam(':type', $file['type']);
        $stmt->bindParam(':size', $file['size']);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':path', $path);
        $stmt->bindParam(':id_user', $_SESSION['u_id']);
        $stmt->bindParam(':date_ajout', $time);
        $stmt->execute();
        
    }
    public function downloadFile($selectedFile)
    {
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
    public function showFiles()
    {
        $pdo = $this->setPdo();
        $stmt = $pdo->prepare('SELECT * FROM `files` WHERE  id_user = :id');
        $stmt->bindParam(':id', $_SESSION['u_id']);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    public function deleteFile($selectedFile, $selectedToken)
    {
        $pdo = $this->setPdo();
        $selectStmt = $pdo->prepare('SELECT * FROM `files` WHERE  name = :name AND id_user = :id AND token = :token');
        $selectStmt->bindParam(':id', $_SESSION['u_id']);
        $selectStmt->bindParam(':name', $selectedFile);
        $selectStmt->bindParam(':token', $selectedToken);
        $selectStmt->execute();
        $result = $selectStmt->fetch();
        unlink($result['path']);
        $deleteStmt = $pdo->prepare('DELETE FROM `files` WHERE  name = :name AND id_user = :id AND token = :token');
        $deleteStmt->bindParam(':id', $_SESSION['u_id']);
        $deleteStmt->bindParam(':name', $selectedFile);
        $deleteStmt->bindParam(':token', $selectedToken);
        $deleteStmt->execute();
    }
    public function renameFile($newName,$selectedFile,$selectedToken)
    {
        if (htmlentities($newName) !== "" && strlen(htmlentities($newName)) <= 30) {
            $tempName = str_replace('/', '', htmlentities($newName));
            $tempName = str_replace('..', '', $tempName);
            $oldPath = $_SESSION['path'] . $selectedFile;
            $newPath = $_SESSION['path'] . $tempName;
            if (file_exists($newPath)) {
                return false;
            } else {
                $pdo = $this->setPdo();
                $deleteStmt = $pdo->prepare('UPDATE `files` SET name = :newfileName, path = :newPath WHERE  name = :name AND id_user = :id AND token = :token');
                $deleteStmt->bindParam(':newfileName', $tempName);
                $deleteStmt->bindParam(':newPath', $newPath);
                $deleteStmt->bindParam(':id', $_SESSION['u_id']);
                $deleteStmt->bindParam(':name', $selectedFile);
                $deleteStmt->bindParam(':token', $selectedToken);
                $deleteStmt->execute();
                rename($oldPath, $newPath);
            }
        }
    }
}
