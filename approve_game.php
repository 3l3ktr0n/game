<?php
require_once('../../class2.php');

if(!getperms('P'))
{
    e107::redirect('admin');
    exit;
}

$sql = e107::getDb();
$tp  = e107::getParser();

$gameID = intval($_GET['id']);

if($gameID)
{
    if($row = $sql->retrieve('pending_games', '*', 'game_id=' . $gameID))
    {
        unset($row['game_id']);
        if($sql->insert('games', $row))
        {
            $sql->delete('pending_games', 'game_id=' . $gameID);
        }
    }
}

e107::getRedirect()->redirect('admin_config.php');

