<?php
/*

if (!defined('e107_INIT')) { exit; }

class game_list_manager
{
    private $sql;
    private $tp;
    private $frm;

    function __construct()
    {
        $this->sql = e107::getDb();
        $this->tp = e107::getParser();
        $this->frm = e107::getForm();
    }

    public function run()
    {
        $userID = USERID;

        // Check if user is logged in
        if (!$userID) {
            echo '<div class="alert alert-danger">You need to be logged in to use this feature.</div>';
            return;
        }

        // Handle different actions like create, edit, add game, etc.
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
            echo '<h3>Your Game Lists</h3>';
            while ($row = $this->sql->fetch()) {
                echo '<div class="card mb-3">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['list_name']) . '</h5>';
                echo '<p class="card-text">' . htmlspecialchars($row['list_description']) . '</p>';
                echo '<a href="?view_list=' . $row['list_id'] . '" class="btn btn-primary">View Games</a>';
                echo '<a href="?delete_list=' . $row['list_id'] . '" class="btn btn-danger">Delete List</a>';
                echo '</div></div>';
            }
        } else {
            echo '<div class="alert alert-warning">No lists found. Create a new list!</div>';
        }
    }

    /*private function addGameToList($userID, $listID)
    {
        $gameID = $this->tp->toDB($_POST['game_id']);
        $rank = intval($_POST['rank']);
        $notes = $this->tp->toDB($_POST['notes']);

        $insertData = [
            'list_id' => $listID,
            'game_id' => $gameID,
            'rank' => $rank,
            'notes' => $notes,
        ];

        if ($this->sql->insert('user_game_list_entries', $insertData)) {
            echo '<div class="alert alert-success">Game successfully added to list!</div>';
        } else {
            echo '<div class="alert alert-danger">Failed to add game to list.</div>';
        }
    }*/
/*
    function viewGamesInList($listID, $userID)
    {
        $sql = e107::getDb();
        $frm = e107::getForm();

        // Check if the user owns this list to prevent unauthorized access
        $listOwnerQuery = "SELECT user_id FROM user_game_lists WHERE list_id = {$listID}";
        $listOwner = $sql->retrieve($listOwnerQuery);
        if ($listOwner['user_id'] != $userID) {
            echo '<div class="alert alert-danger">You do not have permission to view this list.</div>';
            return;
        }

        // Fetch games in the list
        $query = "
            SELECT g.game_title, e.rank, e.notes, e.entry_id 
            FROM user_game_list_entries AS e
            LEFT JOIN games AS g ON e.game_id = g.game_id
            WHERE e.list_id = {$listID}";

        if ($sql->gen($query)) {
            echo '<h3>Games in List</h3>';
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>Game Title</th><th>Rank</th><th>Notes</th><th>Actions</th></tr></thead><tbody>';
            while ($row = $sql->fetch()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['game_title']) . '</td>';
                echo '<td contenteditable="true" data-entry-id="' . $row['entry_id'] . '" class="editable-rank">' . htmlspecialchars($row['rank']) . '</td>';
                echo '<td contenteditable="true" data-entry-id="' . $row['entry_id'] . '" class="editable-notes">' . htmlspecialchars($row['notes']) . '</td>';
                echo '<td>';
                echo '<a href="#" class="btn btn-danger delete-entry" data-entry-id="' . $row['entry_id'] . '">Delete</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-warning">No games found in this list.</div>';
        }

        // Include AJAX JavaScript for updating entries
        echo '
        <script>
            $(document).on("blur", ".editable-rank, .editable-notes", function() {
                var entryId = $(this).data("entry-id");
                var value = $(this).text();
                var field = $(this).hasClass("editable-rank") ? "rank" : "notes";

                $.ajax({
                    url: "update_game_entry.php",
                    type: "POST",
                    data: {
                        entry_id: entryId,
                        field: field,
                        value: value
                    },
                    success: function(response) {
                        console.log(response);
                    }
                });
            });

            $(document).on("click", ".delete-entry", function(e) {
                e.preventDefault();
                var entryId = $(this).data("entry-id");
                
                if (confirm("Are you sure you want to delete this entry?")) {
                    $.ajax({
                        url: "delete_game_entry.php",
                        type: "POST",
                        data: {
                            entry_id: entryId
                        },
                        success: function(response) {
                            location.reload();
                        }
                    });
                }
            });
        </script>';
    }

    function addGameToList($userID, $listID)
    {
        $sql = e107::getDb();
        $frm = e107::getForm();
        
        // Fetch all games to populate dropdown
        $gamesQuery = "SELECT game_id, game_title FROM games";
        $sql->gen($gamesQuery);
        $games = [];
        while ($row = $sql->fetch()) {
            $games[$row['game_id']] = $row['game_title'];
        }

        echo '<h3>Add Game to List</h3>';
        echo $frm->open('add_game_to_list', 'post');
        echo $frm->select('game_id', $games, '', ['required' => 1, 'class' => 'form-control select2', 'placeholder' => 'Select a game']); // Uses select2 for searchability
        echo $frm->text('rank', '', 10, ['placeholder' => 'Rank', 'class' => 'form-control']);
        echo $frm->textarea('notes', '', ['placeholder' => 'Notes', 'class' => 'form-control']);
        echo $frm->button('add_game', 'Add Game', 'submit', ['class' => 'btn btn-primary mt-3']);
        echo $frm->close();

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_game'])) {
            $gameID = $tp->toDB($_POST['game_id']);
            $rank = intval($_POST['rank']);
            $notes = $tp->toDB($_POST['notes']);

            $insertData = [
                'list_id' => $listID,
                'game_id' => $gameID,
                'rank' => $rank,
                'notes' => $notes,
            ];

            $sql->insert('user_game_list_entries', $insertData);
            echo '<div class="alert alert-success">Game successfully added to list!</div>';
        }
    }
}

require_once(HEADERF);

$gameListManager = new game_list_manager();
$gameListManager->run();

require_once(FOOTERF);

*/

if (!defined('e107_INIT')) { exit; }

require_once(HEADERF);
require_once(e_PLUGIN . "game_list_manager/templates/list_view_template.php");

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
            echo '<h3>Your Game Lists</h3>';
            while ($row = $this->sql->fetch()) {
                echo '<div class="card mb-3">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['list_name']) . '</h5>';
                echo '<p class="card-text">' . htmlspecialchars($row['list_description']) . '</p>';
                echo '<a href="?view_list=' . $row['list_id'] . '" class="btn btn-primary">View Games</a>';
                echo '<a href="?delete_list=' . $row['list_id'] . '" class="btn btn-danger">Delete List</a>';
                echo '</div></div>';
            }
        } else {
            echo '<div class="alert alert-warning">No lists found. Create a new list!</div>';
        }
    }

    private function addGameToList($userID, $listID)
    {
        $gameID = $this->tp->toDB($_POST['game_id']);
        $rank = intval($_POST['rank']);
        $notes = $this->tp->toDB($_POST['notes']);

        $insertData = [
            'list_id' => $listID,
            'game_id' => $gameID,
            'rank' => $rank,
            'notes' => $notes,
        ];

        if ($this->sql->insert('user_game_list_entries', $insertData)) {
            echo '<div class="alert alert-success">Game successfully added to list!</div>';
        } else {
            echo '<div class="alert alert-danger">Failed to add game to list.</div>';
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
            include(e_PLUGIN . "game_list_manager/templates/list_view_template.php");
        } else {
            echo '<div class="alert alert-warning">No games found in this list.</div>';
        }
    }
}

$gameListManager = new GameListManager();
$gameListManager->run();

require_once(FOOTERF);

?>