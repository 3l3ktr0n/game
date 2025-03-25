<?php

if (!defined('e107_INIT')) { exit; }

require_once(HEADERF);

e107::lan('game_list_manager', false, true); // Load language file.
require_once(e_PLUGIN . "games/templates/game_list_template.php");
require_once(e_PLUGIN . "games/game_list_shortcodes.php");

class GameListManager
{
    private $sql;
    private $tp;
    private $frm;

    public function __construct()
    {
        $this->sql = e107::getDb();
        $this->tp = e107::getParser();
        $this->frm = e107::getForm();
    }

    public function run()
    {
        $userID = USERID;

        if (!$userID) {
            echo '<div class="alert alert-danger">You need to be logged in to use this feature.</div>';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['create_list'])) {
                $this->createList($userID);
            } elseif (isset($_POST['add_game'])) {
                $this->addGameToList($userID, $_POST['list_id']);
            }
        }

        if (isset($_GET['view_list'])) {
            $this->viewGamesInList($_GET['view_list'], $userID);
        } else {
            $this->viewUserLists($userID);
            $this->createListForm();
        }
    }

    private function createList($userID)
    {
        $listName = $this->tp->toDB($_POST['list_name']);
        $listDescription = $this->tp->toDB($_POST['list_description']);

        $insertData = [
            'user_id' => $userID,
            'list_name' => $listName,
            'list_description' => $listDescription,
        ];

        if ($this->sql->insert('user_game_lists', $insertData)) {
            echo '<div class="alert alert-success">List successfully created!</div>';
        } else {
            echo '<div class="alert alert-danger">Failed to create list.</div>';
        }
    }

    private function createListForm()
    {
        echo '<h3>Create New Game List</h3>';
        echo $this->frm->open('create_game_list', 'post');
        echo $this->frm->text('list_name', '', 255, ['placeholder' => 'List Name', 'required' => 1, 'class' => 'form-control']);
        echo $this->frm->textarea('list_description', '', ['placeholder' => 'Description', 'class' => 'form-control']);
        echo $this->frm->button('create_list', 'Create List', 'submit', ['class' => 'btn btn-primary mt-3']);
        echo $this->frm->close();
    }

    private function viewUserLists($userID)
    {
        $query = "SELECT * FROM user_game_lists WHERE user_id = {$userID}";
        if ($this->sql->gen($query)) {
            $sc = e107::getScBatch('game_list_manager', TRUE);

            echo '<h3>Your Game Lists</h3>';
            while ($row = $this->sql->fetch()) {
                $sc->setVars($row);
                echo e107::getParser()->parseTemplate("{GAME_LIST_NAME} - {GAME_LIST_DESCRIPTION}", true, $sc);
            }
        } else {
            echo '<div class="alert alert-warning">No lists found. Create a new list!</div>';
        }
    }

    private function viewGamesInList($listID, $userID)
    {
        $listOwnerQuery = "SELECT user_id FROM user_game_lists WHERE list_id = {$listID}";
        $listOwner = $this->sql->retrieve($listOwnerQuery);
        if ($listOwner['user_id'] != $userID) {
            echo '<div class="alert alert-danger">You do not have permission to view this list.</div>';
            return;
        }

        $query = "
            SELECT g.game_title, e.rank, e.notes, e.entry_id 
            FROM user_game_list_entries AS e
            LEFT JOIN games AS g ON e.game_id = g.game_id
            WHERE e.list_id = {$listID}";

        if ($this->sql->gen($query)) {
            $sc = e107::getScBatch('game_list_manager', TRUE);
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>Game Title</th><th>Rank</th><th>Notes</th><th>Actions</th></tr></thead><tbody>';

            while ($row = $this->sql->fetch()) {
                $sc->setVars($row);
                echo e107::getParser()->parseTemplate("{GAME_LIST_ENTRY_GAME_TITLE} {GAME_LIST_ENTRY_RANK} {GAME_LIST_ENTRY_NOTES} {GAME_LIST_ENTRY_ACTIONS}", true, $sc);
            }

            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-warning">No games found in this list.</div>';
        }
    }
}

$gameListManager = new GameListManager();
$gameListManager->run();

require_once(FOOTERF);

?>