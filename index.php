<?php
/**
 * Created by PhpStorm.
 * User: jamesskywalker
 * Date: 28/01/2017
 * Time: 21:04
 */
$teams = [];
$msg = '';

include 'globals.php';
$conn = new connection();
$conn = $conn->connect();

$teams = getTeams();
$team_one = $teams[0];
$team_two = $teams[1];


if(isset($_POST['team_one_submit'])){

    $q = $conn->prepare("SELECT changed FROM team WHERE id = 1");
    $q->execute();
    $changed = $q->fetchColumn();

    if($changed>0){
        $q = $conn->prepare("SELECT * FROM team WHERE id = 2");
        $q->execute();
        $row = $q->fetch();
        $pens = $row->shotcounter;
        $loosers = $row->teamname;
        $new = $pens+1;

        $q = $conn->prepare("UPADTE team SET shotcounter = :new where id = 2");
        $q->bindValue(':new',$new,PDO::PARAM_INT);
        $q->execute();
        header('location:teams.php?msg=1&abusers='.$loosers.'&teamname='.$loosers);

    }else{
        $name = clean($_POST['team_name_one']);
        $changed = 1;
        $q = $conn->prepare("UPDATE team set teamname = :name changed = :changed where id = 1");
        $q->bindValue(':name',$name,PDO::PARAM_STR);
        $q->bindValue(':changed',$changed,PDO::PARAM_INT);
        $q->execute();
        header('location: teams.php?teamname='.$name);

    }
}

if(isset($_POST['team_two_submit'])){
    $q = $conn->prepare("SELECT changed FROM team WHERE id = 2");
    $q->execute();
    $changed = $q->fetchColumn();

    if($changed>0) {
        $qry = "SELECT * FROM team WHERE id = 1";
        $q->execute();
        $row = $q->fetch();
        $pens = $row->shotcounter;
        $loosers = $row->teamname;
        $new = $pens+1;

        $qry = "UPDATE team SET shotcounter = :new where id = 1";
        $q->bindParam(':new',$new,PDO::PARAM_INT);
        $q->execute();
        header('location:teams.php?msg=1&abusers='.$loosers.'&teamname='.$loosers);

    } else {
        $name = clean($_POST['team_name_two']);
        $changed = 1;
        $qry = "UPDATE team SET teamname = :name, changed = :changed where id = 2";
        $q->bindValue(':name',$name,PDO::PARAM_STR);
        $q->bindValue(':changed',$changed,PDO::PARAM_INT);
        $q->execute();
        header('location: teams.php?teamname='.$name);

    }

}


if(isset($_GET['msg'])){
    $msg = $_GET['msg'];
    switch($msg){
        case 1:
            $abusers = $_GET['abusers'];
            $msg = 'Busted dog-face; stop trying to change your opponents name, penalty shots for you later!<br><small>we know who you are,</small> <h1> team '.$abusers.'</h1>';
            break;
    }

}

?>

<!DOCTYPE html>
<html lang="en">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link href="http://localhost/bootstrap-3.3.4/dist/css/bootstrap.min.css" rel="stylesheet"/>
</html>
<style>

.jumbotron{
    background-color: transparent;
}
    .footer{
        height:200px;
        width: 100%;
        background: repeating-linear-gradient(to right, blue, blue 40px, white 40px, white 80px);
    }

    .logo{
        height: 100%;
        width: 100%;
        font-weight: bolder;
        text-align: center;
        font-size: 4em;
        color: red;

    }
    #banner{
        font-size: 2em;
        padding-top: 50px;
    }

@media screen and (max-width: 768px){
    .logo{
        font-size: 2em;
    }

}
</style>

<div  class="footer">

    <div class="logo"><h2 id="banner">BDAY BASICS</h2>

    </div>
</div>
<div class="jumbotron">

    <div class="container">
        <form action="index.php" method="post" class="col-md-6 col-sm-6 col-lg-6 col-xs-12">

    <div class="form-group">
        <label>Team 1:</label>
        <input name="team_name_one" type="text" class="form-control" placeholder="Team 1: Choose Your Name">


    </div>
            <button name="team_one_submit" >Submit</button>
    <div class="form-group">
        <label>Team 2:</label>
        <input name="team_name_two" type="text" class="form-control" placeholder="Team 2: Choose Your Name">

    </div>
            <button name="team_two_submit" >Submit</button>
            <div class="container message">

                <h3><?echo$msg?></h3>
            </div>

</form>

        <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
            <div class="container">
                <h2>Team Progress </h2>
                <table class="table">
                    <tr>
                        <td>  </td>
                        <td>
                            N 52° 04.769' W 003° 07.585'</td>
                        <td>N 52° 03.919' W 003° 05.714'</td>
                        <td>
                            N 52° 03.327' W 003° 06.067' </td>
                        <td>
                            N 52° 02.798' W 003° 06.709'</td>
                        <td>Penalties</td>
                    </tr>
                    <tr>
                        <td><a href="teams.php?teamname=<?echo urlencode($team_one->teamname)?>"><?;echo $team_one->teamname?></a></td>
                        <?progress('team_one_found','team_one_time');?>
                        <td><?shots( 1)?></td>
                    </tr>

                    <tr>
                        <td><a href="teams.php?teamname=<?echo urlencode($team_two->teamname)?>"><?;echo $team_two->teamname?></a></td>
                        <?progress('team_two_found','team_two_time');?>
                        <td><?shots( 2)?></td>

                    </tr>

                </table>


            </div>
        </div>

    </div>




    <div  class="footer">

        <div class="logo">
                            <h3>Same quality B-Day at affordable everyday prices</h3>
        </div>
    </div>


</div>
<script>

    alert("unlike most websites this pages DOES NOT USE COOKIES.....because we couldn't afford them");

</script>


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>