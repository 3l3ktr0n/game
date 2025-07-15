<?php
if(!defined('e107_INIT'))
{
    require_once("../../class2.php");
}

require_once(HEADERF);

if(!USER)
{
    echo '<div class="alert alert-danger">You must be logged in to add games.</div>';
    require_once(FOOTERF);
    exit;
}

$sql = e107::getDb();
$tp  = e107::getParser();
$frm = e107::getForm();

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_game']))
{
    $title      = $tp->toDB($_POST['game_title']);
    $developer  = $tp->toDB(vartrue($_POST['game_developer']));
    $genres     = isset($_POST['game_genres']) ? implode(',', array_map('intval', (array)$_POST['game_genres'])) : '';
    $platforms  = isset($_POST['game_platforms']) ? implode(',', array_map('intval', (array)$_POST['game_platforms'])) : '';
    $sef        = eHelper::title2sef($title, 'dashl');

    $insertData = [
        'game_title'     => $title,
        'game_sef'       => $sef,
        'game_developer' => $developer,
        'game_genres'    => $genres,
        'game_platforms' => $platforms,
        'game_datestamp' => time()
    ];

    if($sql->insert('pending_games', $insertData))
    {
        echo '<div class="alert alert-success">Game submitted for review and will be published once approved.</div>';
    }
    else
    {
        echo '<div class="alert alert-danger">Failed to add game.</div>';
    }
}

$genres    = $sql->retrieve('games_genres', 'genre_id AS value, genre_name AS label', '', true);
$platforms = $sql->retrieve('games_platforms', 'platform_id AS value, platform_name AS label', '', true);

echo '<h2>Add New Game</h2>';
echo $frm->open('add_game','post');
echo $frm->text('game_title', '', 255, ['placeholder'=>'Title','required'=>1,'class'=>'form-control']);
echo $frm->text('game_developer', '', 255, ['placeholder'=>'Developer','class'=>'form-control']);
echo $frm->select('game_genres[]', $genres, '', ['multiple'=>1,'class'=>'form-control']);
echo $frm->select('game_platforms[]', $platforms, '', ['multiple'=>1,'class'=>'form-control']);
echo $frm->button('submit_game','Submit','submit',['class'=>'btn btn-primary mt-3']);
echo $frm->close();

require_once(FOOTERF);
?>
