<?php
/**
 * Created by PhpStorm.
 * User: jamesskywalker
 * Date: 28/01/2017
 * Time: 21:04
 */
include 'globals.php';
$conn = new connection();
$conn = $conn->connect();

$msg = "";
if(isset($_GET['msg'])){
    $msg = $_GET['msg'];
    switch($msg){
        case 1:
            $abusers = $_GET['abusers'];
            $msg = 'Busted Dog-face; stop trying to change your opponents name, penalty shots for you later!<br><small>we know who you are, team '.$abusers.'</small>';
            break;
        default:
            $msg = "";
    }

}

$teams = [];
$cache = [];

$teams = getTeams();
$team_one = $teams[0];
$team_two = $teams[1];

$q = $conn->prepare("SELECT cache FROM unleash");
$q->execute();
$cache = $q->fetchAll();


if(isset($_GET['teamname'])){
    $team = $_GET['teamname'];
}else{
    $team = '';
}
$phrases = [];


function spell($id, $teamname){
    global $conn;
    $q = $conn->prepare("SELECT * FROM team WHERE teamname = :teamName");
    $q->bindValue(':teamName',$teamname,PDO::PARAM_STR);
    $q->execute();
    $row = $q->fetch();
    $result = $row->id;

    if($result == 1){
        $progcheck = 'team_one_found';
    }else{
        $progcheck = 'team_two_found';
    }



    $q = $conn->prepare("SELECT * FROM unleash WHERE id = :id ");
    $q->bindValue(':id',$id,PDO::PARAM_INT);
    $q->execute();
    $row = $q->fetch();

        $found = $row->$progcheck;

        if($found ==1){
            echo $row->code;
        }else{
            echo'?????';
        }

}

if(isset($_POST['code'])){

    $code = clean($_POST['geo_code']);
    $team = clean($_GET['teamname']);

    $q = $conn->prepare("SELECT * FROM unleash WHERE cache = :code ");
    $q->bindValue(':code',$code,PDO::PARAM_STR);
    $q->execute();
    $result = $q->fetch();

   if($result){
    $row = $result->code;
    $t_o_found = $result->team_one_found;
    $t_t_found = $result->team_two_found;
    $time = time();
    $timeStamp = gmdate("H:i:s", $time);

    switch($team){
        case $team == $team_one:
            $updateQry = 'team_one_found';
            $updateTime = 'team_one_time';
            $id=2;

            break;
         case $team == $team_two:
             $updateQry = 'team_two_found';
             $updateTime = 'team_two_time';
             $id=1;

            break;

    }



   $q = $conn->prepare("UPDATE unleash SET " . $updateQry  ." = 1, " . $updateTime . " = :time WHERE cache = :code");
   $q->bindValue(':time',$timeStamp,PDO::PARAM_STR);
   $q->bindValue(':code',$code,PDO::PARAM_STR);
   $q->execute();



    $q = $conn->prepare("SELECT * FROM unleash WHERE cache = :cache");
    $q->bindValue(':cache',$cache,PDO::PARAM_STR);
    $result = $q->fetch();
    $row = $result->code;
    $t_o_found = $result->team_one_found;
    $t_t_found = $result->team_two_found;

    if(!$t_o_found==$t_t_found) {


        $q = $conn->prepare("SELECT shotcounter FROM team WHERE id = :id ");
        $q->bindValue(':id',$id,PDO::PARAM_INT);
        $q->execute();
        $counter = (int)($q->fetchColumn())+1;

        $qry = "UPDATE team SET shotcounter = :counter WHERE id= :id " ;
        $q->bindValue(':counter',$counter,PDO::PARAM_INT);
        $q->bindValue(':id',$id,PDO::PARAM_INT);
        $q->execute();

    }
} else {


       $q = $conn->prepare("SELECT shotcounter FROM team WHERE teamname = :team ");
       $q->bindValue(':team',$team,PDO::PARAM_STR);
       $q->execute();
       $counter = (int)($q->fetchColumn())+1;

       $q = $conn->prepare("UPDATE team SET shotcounter = :counter WHERE teamname = :team ");
       $q->bindValue(':counter',$counter,PDO::PARAM_INT);
       $q->bindValue(':team',$team,PDO::PARAM_STR);
       $q->execute();
       $msg = "that code was incorrect dufus";

   }


} else{
    $code = 'Cum On It!';
}

?>

<!DOCTYPE html>
<html lang="en">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link href="http://localhost/bootstrap-3.3.4/dist/css/bootstrap.min.css" rel="stylesheet"/>
</html>
<style>
    .hangman{
        border-bottom: 1px solid black;
        margin-top 10px;
    }
    .buff{
        margin-top: 30px;
    }
.spell{
    margin-top: 25px;
    margin-right: 2px;
    border-bottom: 5px solid black;
    color: blue;
    text-align: center;
}



    .footer{ height:200px;

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
    .jumbotron{
        background-color: transparent;
    }

    @media screen and (max-width: 768px){
        .logo{
            font-size: 2em;
        }

    }
</style>
<div class="footer">

    <div class="logo"><h2 id="banner">BASICS QUEST</h2></div>
</div>
<div class="jumbotron">
            <div class="row">
    <div class="container col-md-6 col-sm-6 col-lg-6 col-xs-12">
       <div class="container">
                    <form action="teams.php?teamname=<?echo $team?>" method="post" >

                        <div class="form-group">
                            <label>Geo Cache Code</label>
                            <input name="geo_code" type="text" class="form-control" placeholder="enter your cache code here">


                        </div>
                        <button name="code" >Submit</button>



                    </form>

           <div class="container message">

               <h3><?echo$msg?></h3>
           </div>

       </div>


        <div class="container buff">
            <div class="col-md-3 col-lg-3 col-xs-3 col-sm-3 hangman">

                <?echo $code;?>
            </div>

        </div>
        </div>

        <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12">
            <div class="container">
                <h2>Team Progress <small><a href="info.html" target="_blank">learn a little more</a></small> </h2>
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
                        <td><?echo $team_one->teamname?></td>
                        <?progress('team_one_found','team_one_time');?>
                        <td><?shots(1)?></td>
                    </tr>

                    <tr>
                        <td><?echo $team_two->teamname?></td>
                        <?progress('team_two_found','team_two_time');?>
                        <td><?shots( 2)?></td>

                    </tr>

                </table>


            </div>
        </div>
</div>


</div>


<div class="row">
    <div class="container">

        <div class="col-md-3 col-sm-3 col-lg-3 col-xs-3 ">
            <div class="col-md-10 spell">
                <?spell(1,$team);?>
            </div>

        </div>

        <div class="col-md-3 col-sm-3 col-lg-3 col-xs-3 ">
            <div class="col-md-10 spell">
                <? spell(2,$team);?>
            </div>

        </div>

        <div class="col-md-3 col-sm-3 col-lg-3 col-xs-3 ">
            <div class="col-md-10 spell">
                <? spell(3,$team);?>
            </div>

        </div>

        <div class="col-md-3 col-sm-3 col-lg-3 col-xs-3 ">

            <div class="col-md-10 spell">
                <? spell(4,$team);?>
            </div>
        </div>


    </div>

</div>





    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>