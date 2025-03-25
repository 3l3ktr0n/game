<?php

include_once('../../class2.php');

$sql = e107::getDb();
$userID = USERID;
$gameID = $_POST['game_id'];
$status_platform_id = $_POST['review_platform'];
$status_completion = $_POST['completion'];
$status_notes = $_POST['review_body'];

if(isset($gameID))
{
    if(USER)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $update = array(
                'data'  => array(
                    'status_platform_id' => $status_platform_id,
                    'status_completion' => $status_completion,
                    'status_notes' => $status_notes
                ),
                'WHERE' => 'status_user_id = '.$userID.' AND status_game_id = '.$gameID.''
            );
            $sql->update('bookmarks', $update);

            echo 'success';
        }
    }  
}

?>