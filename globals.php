<?php
/**
 * Created by PhpStorm.
 * User: jamesskywalker
 * Date: 03/02/2017
 * Time: 10:45
// */

class connection {
    private $dbName;
    private $dbHost;
    private $dbUsername;
    private $dbPass;


    function connect() {
        $this->setConnectionVars();
        try {
            $only = new PDO('mysql:host='.$this->dbHost.';dbname='.$this->dbName, $this->dbUsername, $this->dbPass);
            $only->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            return $only;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo "Internal error, please contact someone in charge";
            die();
        }
    }

    private function setConnectionVars() {
        if (isset($_SERVER['HTTP_HOST']) && !$_SERVER['HTTP_HOST'] == 'localhost') $dbDetail = (object)parse_ini_file("localIni.ini", true)["PDO_DETAILS"];
        else  $dbDetail = (object)parse_ini_file('bday.ini');
        ["PDO_DETAILS"];

        $this->dbName = $dbDetail->dbName;
        $this->dbHost = $dbDetail->dbHost;
        $this->dbUsername = $dbDetail->dbUsername;
        $this->dbPass = $dbDetail->dbPass;

    }

}

function clean($value){
    $value = nl2br("$value");
    $value = str_replace(['<div>',"\r","\n"],'<br>',$value);
    $value = strip_tags($value,'<br>');
    $value = str_replace(['src','&lt;','&gt;'],'',$value);
    return $value;

}

function getTeams() {
    global $conn;
    $q = $conn->prepare("SELECT * FROM team");
    $q->execute();
    return $q->fetchAll();

}

function progress($x, $y){
    global $conn;
    $q = $conn->prepare("select * from unleash");
    $q->execute();
    $result = $q->fetchAll();
    foreach($result as $row ){
        if($row->$x == 1){
            $timeStamp =$row->$y;
            $step ='<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>';
            echo '<td>'. $step." found @ ".$timeStamp.'</td>';
        }else{
            $step ='<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>';
            echo '<td>'. $step." still waiting dog breath ".'</td>';
        }

    }

}

function shots($teamID)
{
    global $conn;
    $q = $conn->prepare("select shotcounter from team where id = :teamId");
    $q->bindValue(":teamId",clean($teamID),PDO::PARAM_INT);
    $q->execute();
    $shots = $q->fetchColumn();
    echo $shots;
}