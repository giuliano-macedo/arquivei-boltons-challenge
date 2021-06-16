<?php

require_once dirname( __FILE__ )."/../models/Nfe.php";

final class Database
{

    function __construct()
    {
        $pass = $_ENV['MARIADB_ROOT_PASSWORD'];
        if (!$pass) {
            throw new ErrorException('Missing MARIADB_ROOT_PASSWORD environment variable');
        }
        $this->pdo = new PDO("mysql:host=mariadb;dbname=boltons", "root", $pass);
    }

    function getNfe(String $accessKey)
    {
        $sth = $this->pdo->prepare('SELECT access_key, total_value FROM nfe WHERE access_key=?');
        $sth->execute(array($accessKey));
        $nfe = $sth->fetch();
        if (!$nfe) return null;
        return new Nfe($nfe["access_key"], $nfe["total_value"]);
    }

    function replaceNfes(array $nfes)
    {
        $this->pdo->beginTransaction();
        $sth = $this->pdo->prepare('REPLACE nfe (access_key, total_value) VALUES (?, ?)');
        foreach ($nfes as $nfe) {
            $sth->execute(array($nfe->getAccessKey(), $nfe->getTotalValue()));
        }
        $this->pdo->commit();
    }
}
