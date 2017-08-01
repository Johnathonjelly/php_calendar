<?php
//Author: Johnathon Southworth
//Class: CS296 PHP Jeff Miller
//Lab: 3 -- calendar
class Connection {
    protected $db;
    public function Connection() {

    $conn = NULL;
        try{
            $conn = new PDO("mysql:host=localhost;dbname=calendar", "root", "root");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e){
                echo 'ERROR: ' . $e->getMessage();
                }
            $this->db = $conn;
    }

    public function getConnection() {
        return $this->db;
    }
}
?>
