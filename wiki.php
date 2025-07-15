<?php
if(!defined('e107_INIT'))
{
    require_once("../../class2.php");
}

require_once(HEADERF);

$sql = e107::getDb();
$tp  = e107::getParser();
$frm = e107::getForm();

$gameID = intval(vartrue($_GET['id'], 0));
$wikiID = intval(vartrue($_GET['wiki'], 0));
$action = vartrue($_GET['action']);

if($wikiID && $action == 'edit' && USER)
{
    $sql->select('game_wiki', '*', 'wiki_id=' . $wikiID);
    $row = $sql->fetch();

    if(isset($_POST['save_wiki']))
    {
        $pending = array(
            'wiki_id'         => $wikiID,
            'pending_title'   => $_POST['wiki_title'],
            'pending_body'    => $_POST['wiki_body'],
            'pending_editor'  => USERID,
            'pending_datestamp'=> time()
        );
        $sql->insert('game_wiki_pending', $pending);
        echo '<div class="alert alert-info">Edit submitted for approval.</div>';
    }
    else
    {
        echo $frm->open('edit_wiki');
        echo $frm->text('wiki_title', $row['wiki_title']);
        echo $frm->bbarea('wiki_body', $row['wiki_body']);
        echo $frm->submit('save_wiki', 'Save');
        echo '<div class="alert alert-info">Your changes will require admin approval.</div>';
        echo $frm->close();
    }
}
elseif($wikiID)
{
    if($sql->select('game_wiki', '*', 'wiki_id=' . $wikiID))
    {
        $row = $sql->fetch();
        echo '<h2>' . $tp->toHTML($row['wiki_title'], true) . '</h2>';
        echo $tp->toHTML($row['wiki_body'], true, 'BODY');

        $editor = e107::user($row['wiki_editor']);
        $author = e107::user($row['wiki_author']);
        $date = e107::getDate();

        if($row['wiki_edit_datestamp'])
        {
            $edate = $date->convert_date($row['wiki_edit_datestamp'], 'short');
            $ename = vartrue($editor['user_name'], 'Unknown');
            echo '<div class="smalltext">Last edited: ' . $edate . ' by ' . $tp->toHTML($ename, true) . '</div>';
        }
        else
        {
            $cdate = $date->convert_date($row['wiki_datestamp'], 'short');
            $cname = vartrue($author['user_name'], 'Unknown');
            echo '<div class="smalltext">Created: ' . $cdate . ' by ' . $tp->toHTML($cname, true) . '</div>';
        }

        if(ADMIN)
        {
            if($sql->select('game_wiki_pending','pending_id','wiki_id='.$wikiID))
            {
                echo '<div class="alert alert-warning">There is a pending edit for this page.</div>';
            }
        }

        if(USER)
        {
            $link = e_PLUGIN . 'games/wiki.php?id=' . $gameID . '&wiki=' . $wikiID . '&action=edit';
            echo '<div><a href="' . $link . '">Suggest Edit</a></div>';
        }
    }
    else
    {
        echo '<div>Wiki page not found.</div>';
    }
}
else
{
    echo '<h2>Game Wiki</h2>';
    if($sql->select('game_wiki', '*', 'wiki_game_id=' . $gameID))
    {
        while($row = $sql->fetch())
        {
            $link = e_PLUGIN . 'games/wiki.php?id=' . $gameID . '&wiki=' . $row['wiki_id'];
            echo '<div><a href="' . $link . '">' . $tp->toHTML($row['wiki_title'], true) . '</a></div>';
        }
    }
    else
    {
        echo '<div>No wiki pages yet.</div>';
    }
}

require_once(FOOTERF);
exit;
?>
