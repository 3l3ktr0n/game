<?php

if(!defined('e107_INIT'))
{
	require_once("../../class2.php");
}

$sql = e107::getDb();
$gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

function getOverview($gameID)
{
    $sql = e107::getDb();
    $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

    $status1 = $sql->count("bookmarks", "(status)", "status_game_id = {$gameID} AND status = 1");
    $status2 = $sql->count("bookmarks", "(status)", "status_game_id = {$gameID} AND status = 4");
    $status3 = $sql->count("bookmarks", "(status)", "status_game_id = {$gameID} AND status = 5"); 
     
    echo '<div class="container-fluid" style="background-color: #efefef; border-radius: 5px;"><div class="row"><span class="glyphicon glyphicon-list" aria-hidden="true"></span> '.$status1.' users want this game</div><div class="row"><span class="glyphicon glyphicon-hdd" aria-hidden="true"></span> '.$status2.' users own a digital copy of this game</div><div class="row"><span class="glyphicon glyphicon-cd" aria-hidden="true"></span> '.$status3.' users own a physical copy of this game</div></div>';
        
    $completion1 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 1");
    $completion2 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 2");
    $completion3 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 3");
    $completion4 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 4");
    $completion5 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 5");

    echo '<div class="container-fluid" style="background-color: #ececec; border-radius: 5px;"><div class="row"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '.$completion1.' users dropped this game</div><div class="row"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> '.$completion2.' users have the game waiting in their backlog</div><div class="row"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> '.$completion3.' users are currently playing the game</div><div class="row"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> '.$completion4.' users have finished the game</div><div class="row"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> '.$completion5.' users have 100% completed the game</div></div>';
}

if(e_PAGE === 'game.php')
{
    getOverview($gameID);
}

?>