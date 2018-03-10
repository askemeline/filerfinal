<?php
require_once('Cool/DBManager.php');

class BaseManager
{
    protected function setPdo(){
        $dbm = DBManager::getInstance();
        $pdo = $dbm->getPdo();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
        }

}
