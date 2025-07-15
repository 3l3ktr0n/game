<?php
require_once('../../class2.php');
if (!getperms('P'))
{
    e107::redirect('admin');
    exit;
}

$sql = e107::getDb();
$tp  = e107::getParser();
$ns  = e107::getRender();

if(vartrue($_GET['pending']))
{
    if(isset($_GET['approve']))
    {
        $pid = intval($_GET['approve']);
        if($sql->select('game_wiki_pending','*','pending_id='.$pid))
        {
            $row = $sql->fetch();
            $update = array(
                'wiki_title'         => $row['pending_title'],
                'wiki_body'          => $row['pending_body'],
                'wiki_edit_datestamp'=> $row['pending_datestamp'],
                'wiki_editor'        => $row['pending_editor']
            );
            $sql->update('game_wiki',$update,'WHERE wiki_id='.$row['wiki_id']);
            $sql->delete('game_wiki_pending','pending_id='.$pid);
        }
        e107::redirect(e_SELF.'?pending=1');
        exit;
    }

    if(isset($_GET['reject']))
    {
        $pid = intval($_GET['reject']);
        $sql->delete('game_wiki_pending','pending_id='.$pid);
        e107::redirect(e_SELF.'?pending=1');
        exit;
    }

    if(isset($_GET['view']))
    {
        $pid = intval($_GET['view']);
        if($sql->select('game_wiki_pending','*','pending_id='.$pid))
        {
            $row = $sql->fetch();
            $old = [];
            if($sql->select('game_wiki','*','wiki_id='.$row['wiki_id']))
            {
                $old = $sql->fetch();
            }
            $text  = "<h3>Changes for Wiki ID {$row['wiki_id']}</h3>"; 
            $text .= "<table class='table adminlist'>";
            $text .= "<tr><th>Field</th><th>Current</th><th>Proposed</th></tr>";
            $text .= "<tr><td>Title</td><td>".$tp->toHTML(vartrue($old['wiki_title'],'-'),true)."</td><td>".$tp->toHTML($row['pending_title'],true)."</td></tr>";
            $text .= "<tr><td>Body</td><td>".$tp->toHTML(vartrue($old['wiki_body'],'-'),true,'BODY')."</td><td>".$tp->toHTML($row['pending_body'],true,'BODY')."</td></tr>";
            $approve = e_SELF.'?pending=1&amp;approve='.$row['pending_id'];
            $reject  = e_SELF.'?pending=1&amp;reject='.$row['pending_id'];
            $text .= "<tr><td colspan='3'><a class='btn btn-success' href='{$approve}'>Approve</a> <a class='btn btn-danger' href='{$reject}'>Reject</a></td></tr>";
            $text .= "</table>";
            $ns->tablerender('Review Edit',$text);
            require_once(e_ADMIN.'footer.php');
            exit;
        }
    }

    $text  = "<table class='table adminlist'>";
    $text .= "<tr><th>ID</th><th>Wiki ID</th><th>Title</th><th>Editor</th><th>Date</th><th>Options</th></tr>";
    if($sql->select('game_wiki_pending','*','ORDER BY pending_datestamp DESC'))
    {
        while($row = $sql->fetch())
        {
            $u = e107::user($row['pending_editor']);
            $name = $tp->toHTML(vartrue($u['user_name'],'Unknown'),true);
            $date = e107::getDate()->convert_date($row['pending_datestamp'],'short');
            $view    = e_SELF.'?pending=1&amp;view='.$row['pending_id'];
            $approve = e_SELF.'?pending=1&amp;approve='.$row['pending_id'];
            $reject  = e_SELF.'?pending=1&amp;reject='.$row['pending_id'];
            $text .= "<tr>";
            $text .= "<td>{$row['pending_id']}</td>";
            $text .= "<td>{$row['wiki_id']}</td>";
            $text .= "<td>".$tp->toHTML($row['pending_title'],true)."</td>";
            $text .= "<td>{$name}</td>";
            $text .= "<td>{$date}</td>";
            $text .= "<td><a href='{$view}'>View</a> | <a href='{$approve}'>Approve</a> | <a href='{$reject}'>Reject</a></td>";
            $text .= "</tr>";
        }
    }
    else
    {
        $text .= "<tr><td colspan='6'>No pending edits.</td></tr>";
    }
    $text .= "</table>";
    $ns->tablerender('Pending Wiki Edits',$text);
    require_once(e_ADMIN.'footer.php');
    exit;
}

class wiki_adminArea extends e_admin_dispatcher
{
    protected $modes = array(
        'main' => array(
            'controller' => 'wiki_ui',
            'path'       => null,
            'ui'         => 'wiki_form_ui',
            'uipath'     => null
        )
    );

    protected $adminMenu = array(
        'main/list'   => array('caption'=> LAN_MANAGE, 'perm' => 'P'),
        'main/create' => array('caption'=> LAN_CREATE, 'perm' => 'P')
    );

    protected $adminMenuAliases = array(
        'main/edit' => 'main/list'
    );

    protected $menuTitle = 'Game Wiki';
}

class wiki_ui extends e_admin_ui
{
    protected $pluginTitle = 'Games';
    protected $pluginName  = 'games';
    protected $table       = 'game_wiki';
    protected $pid         = 'wiki_id';
    protected $perPage     = 10;
    protected $listOrder   = 'wiki_id DESC';

    protected $fields = array(
        'checkboxes' => array(
            'title' => '',
            'type'  => null,
            'data'  => null,
            'width' => '5%',
            'thclass' => 'center',
            'class'   => 'center',
            'forced'  => true
        ),
        'wiki_id' => array(
            'title' => LAN_ID,
            'type'  => 'number',
            'data'  => 'int',
            'width' => '5%',
            'thclass' => 'left',
            'class'   => 'left'
        ),
        'wiki_game_id' => array(
            'title' => 'Game ID',
            'type'  => 'number',
            'data'  => 'int',
            'width' => '5%',
            'thclass' => 'left',
            'class'   => 'left'
        ),
        'wiki_title' => array(
            'title' => 'Title',
            'type'  => 'text',
            'data'  => 'str',
            'width' => 'auto',
            'thclass' => 'left',
            'class'   => 'left'
        ),
        'wiki_body' => array(
            'title' => 'Body',
            'type'  => 'bbarea',
            'data'  => 'str',
            'width' => 'auto',
            'nolist'=> true,
            'thclass' => 'left',
            'class'   => 'left'
        ),
        'wiki_author' => array(
            'title' => 'Author',
            'type'  => 'number',
            'data'  => 'int',
            'width' => 'auto',
            'thclass' => 'left',
            'class'   => 'left'
        ),
        'wiki_editor' => array(
            'title' => 'Edited By',
            'type'  => 'number',
            'data'  => 'int',
            'width' => 'auto',
            'thclass' => 'left',
            'class'   => 'left'
        ),
        'wiki_datestamp' => array(
            'title' => 'Created',
            'type'  => 'datestamp',
            'data'  => 'int',
            'width' => 'auto',
            'thclass' => 'left',
            'class'   => 'left'
        ),
        'wiki_edit_datestamp' => array(
            'title' => 'Updated',
            'type'  => 'datestamp',
            'data'  => 'int',
            'width' => 'auto',
            'nolist'=> true,
            'thclass' => 'left',
            'class'   => 'left'
        ),
        'options' => array(
            'title' => LAN_OPTIONS,
            'type'  => null,
            'data'  => null,
            'width' => '10%',
            'thclass' => 'center last',
            'class'   => 'center last',
            'forced'  => true
        )
    );

    protected $fieldpref = array('wiki_id','wiki_game_id','wiki_title','wiki_author','wiki_editor','wiki_datestamp','wiki_edit_datestamp');

    public function beforeCreate($new, $old)
    {
        $new['wiki_datestamp'] = time();
        $new['wiki_author'] = USERID;
        $new['wiki_edit_datestamp'] = time();
        $new['wiki_editor'] = USERID;
        return $new;
    }

    public function beforeUpdate($new, $old, $id)
    {
        $new['wiki_edit_datestamp'] = time();
        $new['wiki_editor'] = USERID;
        return $new;
    }
}

class wiki_form_ui extends e_admin_form_ui
{
}

new wiki_adminArea();
require_once(e_ADMIN.'auth.php');
e107::getAdminUI()->runPage();
require_once(e_ADMIN.'footer.php');
exit;

?>
