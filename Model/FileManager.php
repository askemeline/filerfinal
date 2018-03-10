<?php
require_once ('Model/BaseManager.php');

class FileManager extends BaseManager{

    public function uploadFile($path,$id_user){

        $pdo = $this->setPdo();
        $stmt = $pdo->prepare('INSERT INTO files(id, token, path, id_user, date_ajout) VALUES(NULL, :token ,:path, :id_user, :date_ajout)');
        $stmt->bindParam(':token', bin2hex(openssl_random_pseudo_bytes(8)));
        $stmt->bindParam(':path', $path);
        $stmt->bindParam(':id_user', $id_user);
        $stmt->bindParam(':date_ajout', date('Y-m-d H:i:s'));
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
}