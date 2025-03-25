<?php

if(!defined('e107_INIT'))
{
	require_once("../../class2.php");
}

function run()
{
  $sql = e107::getDb(); 				// mysql class object
  $tp = e107::getParser(); 				// parser for converting to HTML and parsing templates etc.
  $frm = e107::getForm(); 				// Form element class.
  $ns = e107::getRender();				// render in theme box.

  $text = '';

  $userID = USERID;
  $completion = (isset($_GET['completion']) && is_string($_GET['completion']));

  switch($_GET['completion'])
  {
    case 'all':
      getCompletion($userID, $status);
    break;
    case 'dropped':
      echo 'dropped';
      getCompletion($userID, 1);
    break;
    case 'backlog':
      echo 'backlog';
      getCompletion($userID, 2);
    break;
    case 'playing':
      echo 'playing';
      getCompletion($userID, 3);
    break;
    case 'finished':
      echo 'finished';
      getCompletion($userID, 4);
    break;
    case 'completed':
      echo 'completed';
      getCompletion($userID, 5);
    break;
    default:
      echo 'dropped ';
      userCompletion($userID, 1);
      echo 'backlog ';
      userCompletion($userID, 2);
      echo 'playing ';
      userCompletion($userID, 3);
      echo 'finished ';
      userCompletion($userID, 4);
      echo 'completed ';
      userCompletion($userID, 5);
      echo '<BR>';
      userCompletionStatus($userID);
  }

}

function userCompletion($userID, $status)
{
  $sql = e107::getDb();

  $row = $sql->count("bookmarks", "(status_completion)", "status_user_id = {$userID} AND status_completion = {$status}");
  //$row = $sql->fetch();
  echo $row;
}

function userCompletionStatus($userID)
{
    $sql = e107::getDb();

    $rating = "";

    if($sql->gen("SELECT status_completion FROM #bookmarks WHERE status_user_id = {$userID}"))
    {
        $ratingResult = "";

        while($row_rating = $sql->fetch())
        {
            if($row_rating["status_completion"] != 0)
            {
                $ratingResult = $row_rating["status_completion"];							    	
                $sum_of_rating += $ratingResult;
                $sum_of_rating_multi5 = $sum_of_rating*5;
                $numberOfUsers_Array[] = $ratingResult;
            }
        }

        $numberOfUsers_Array_Count = array_count_values($numberOfUsers_Array);
        $numberOfUsers_Array_Count = array_sum($numberOfUsers_Array_Count);
        $numberOfUsers = $numberOfUsers_Array_Count*5;
        $rating = $sum_of_rating_multi5/$numberOfUsers;

        $userReviewCount = $numberOfUsers_Array_Count;

        $commentsNum = $numberOfUsers_Array;
        $totalComments = $commentsNum;
        $totalComments = count($totalComments);

        if($totalComments != 0)
        {
            $numberCommentsNum = "";
            $commentPercentage = "";

            for($i=5; $i>=1; $i--)
            {
              switch($i)
              {
                case 1:
                  $status = 'dropped';
                break;
                case 2:
                  $status = 'backlog';
                break;
                case 3:
                  $status = 'playing';
                break;
                case 4:
                  $status = 'finished';
                break;
                case 5:
                  $status = 'completed';
                break;
              }
                $numberCommentsNum = $sql->select('bookmarks', '*', 'status_completion = "'.$i.'" AND status_user_id = "'.$userID.'"');

                $commentPercentage = ((int)$numberCommentsNum > 0) ? (((int)$numberCommentsNum / (int)$totalComments)*100) : 0;
                        
                echo '
                
                        <tr>
                            <td>
                                <span class="p-2">'. $status .'</span>
                            </td>
                            <td class="w-100">
                                <div class="progress" style="height:10px;">
                                    <div class="progress-bar bg-success" role="progressbar" aria-valuemin="0" aria-valuemax="10" style="width: '.$commentPercentage.'%;"></div>
                                </div>
                            </td>
                            <td>
                                <span class="p-2">'. "(" .$numberCommentsNum. ")" .'</span>
                            </td>
                        </tr>

                ';
            }
        }
    }
}

function getCompletion($userID, $status = null)
{
    $sql = e107::getDb();
    $frm = e107::getForm();

    // Add condition for specific status if needed
    $statusClause = is_null($status) ? '' : ' AND b.status_completion = ' . intval($status);

    // Modify the SQL query to get the game details and completion times separately
    $query = "
        SELECT 
            b.status_game_id, 
            b.status_completion,
            b.status_platform_id, 
            b.status_notes, 
            g.game_title,
            MAX(CASE WHEN ct.completion = 4 THEN ct.completion_time END) AS finished_time,
            MAX(CASE WHEN ct.completion = 5 THEN ct.completion_time END) AS completed_time
        FROM 
            #bookmarks AS b
        LEFT JOIN 
            #games AS g ON b.status_game_id = g.game_id
        LEFT JOIN 
            #completion_times AS ct ON b.status_user_id = ct.user_id AND b.status_game_id = ct.game_id
        WHERE 
            b.status_user_id = {$userID} {$statusClause}
        GROUP BY 
            b.status_game_id, g.game_title, b.status_completion, b.status_platform_id, b.status_notes
    ";

    if ($results = $sql->retrieve($query, true)) 
    {
        // Output the list of games as HTML
        echo '<table class="table table-striped table-bordered"><thead><tr><th>Game Title</th><th>Status</th><th>Platform</th><th>Notes</th><th>Finished Time</th><th>Completed Time</th><th>Actions</th></tr></thead><tbody>';
        foreach ($results as $result) 
        {
            $finishedTime = ($result['finished_time']) ? gmdate("H:i:s", $result['finished_time']) : 'N/A';
            $completedTime = ($result['completed_time']) ? gmdate("H:i:s", $result['completed_time']) : 'N/A';

            echo '<tr>
                    <td>' . htmlspecialchars($result['game_title']) . '</td>
                    <td>' . htmlspecialchars($result['status_completion']) . '</td>
                    <td>' . htmlspecialchars($result['status_platform_id']) . '</td>
                    <td>' . htmlspecialchars($result['status_notes']) . '</td>
                    <td>' . $finishedTime . '</td>
                    <td>' . $completedTime . '</td>
                    <td>
                        <a href="#" data-toggle="modal" data-target="#game-' . intval($result['status_game_id']) . '">
                            <span class="glyphicon glyphicon-plus-sign" style="color: green" aria-hidden="true">+</span>
                        </a>
                        <span class="glyphicon glyphicon-remove-sign" style="color: red" aria-hidden="true">-</span>
                    </td>
                </tr>';
        }
        echo '</tbody></table>';

        // Generate modals for each game for editing
        foreach ($results as $result) 
        {
            $finished_time_seconds = $result['finished_time'] ?? 0;  // Default to 0 if not available
            $completed_time_seconds = $result['completed_time'] ?? 0; // Default to 0 if not available

            // Convert the finished time from seconds to hours, minutes, and seconds
            $finished_hours = floor($finished_time_seconds / 3600);
            $finished_remaining_seconds = $finished_time_seconds % 3600;
            $finished_minutes = floor($finished_remaining_seconds / 60);
            $finished_seconds = $finished_remaining_seconds % 60;

            // Convert the completed time from seconds to hours, minutes, and seconds
            $completed_hours = floor($completed_time_seconds / 3600);
            $completed_remaining_seconds = $completed_time_seconds % 3600;
            $completed_minutes = floor($completed_remaining_seconds / 60);
            $completed_seconds = $completed_remaining_seconds % 60;

            echo ' 
            <div class="modal fade" id="game-' . intval($result['status_game_id']) . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">' . htmlspecialchars($result['game_title']) . '</h4>
                        </div>
                        <div class="modal-body">
                            <form id="game-form-' . intval($result['status_game_id']) . '" method="post">
                                <input type="hidden" id="game_id" name="game_id" value="' . intval($result['status_game_id']) . '">
                                <div class="form-group">
                                    <label for="review_platform">Platform</label>';
                                    $sql->select("games", "*", "game_id = {$result['status_game_id']}");
                                    while ($row = $sql->fetch()) 
                                    {
                                        $platform_array[] = $row["game_platforms"];
                                    }
                                    if (!empty($platform_array)) 
                                    {
                                        $platform_array = explode(",", $platform_array[0]);
                                        $platform_array = array_combine($platform_array, $platform_array);
                                        foreach ($platform_array as $platform) 
                                        {
                                            $sql->select("games_platforms", "platform_id, platform_name", "platform_id = {$platform}");
                                            while ($test = $sql->fetch()) 
                                            {
                                                $platforms[$test['platform_id']] = $test['platform_name'];
                                            }
                                        }
                                    }
                                    echo $frm->select('review_platform', $platforms, $result['status_platform_id']);
                                    echo '
                                </div>
                                <div class="form-group">
                                    <label for="completion">Completion Status</label>';
                                    $completion = array(1 => 'Dropped it', 2 => 'Backlog', 3 => 'Currently playing', 4 => 'Finished it', 5 => 'Completed it 100%');
                                    echo $frm->select('completion', $completion, $result['status_completion'], array('class' => 'completion-status'));
                                    echo '
                                </div>
                                <div id="completion-time-fields">
                                    <div id="finished-fields" class="form-inline">
                                        <h5>Finished Time</h5>
                                        <input type="number" name="finished_hours" class="form-control" min="0" max="999" value="' . intval($finished_hours) . '" placeholder="Hours">
                                        <input type="number" name="finished_minutes" class="form-control" min="0" max="59" value="' . intval($finished_minutes) . '" placeholder="Minutes">
                                        <input type="number" name="finished_seconds" class="form-control" min="0" max="59" value="' . intval($finished_seconds) . '" placeholder="Seconds">
                                    </div>
                                    <div id="completed-fields" class="form-inline" style="display: none;">
                                        <h5>Completed Time</h5>
                                        <input type="number" name="completed_hours" class="form-control" min="0" max="999" value="' . intval($completed_hours) . '" placeholder="Hours">
                                        <input type="number" name="completed_minutes" class="form-control" min="0" max="59" value="' . intval($completed_minutes) . '" placeholder="Minutes">
                                        <input type="number" name="completed_seconds" class="form-control" min="0" max="59" value="' . intval($completed_seconds) . '" placeholder="Seconds">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="review_body">Notes</label>';
                                    echo $frm->textarea('review_body', htmlspecialchars($result['status_notes']), 'games', null, 'small');
                                    echo '
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" form="game-form-' . intval($result['status_game_id']) . '">Save changes</button>
                                    <button type="submit" class="btn btn-danger" name="delete">Remove game</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>';
        }
    } 
    else 
    {
        echo '<div class="panel panel-default">No games found</div>';
    }

    // Add jQuery script for dynamic show/hide of time fields based on the completion status
    echo '
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $(".completion-status").change(function() {
                var status = $(this).val();
                var modalId = $(this).closest(".modal").attr("id");

                if (status == 4) {
                    $("#" + modalId + " #finished-fields").show();
                    $("#" + modalId + " #completed-fields").hide();
                } else if (status == 5) {
                    $("#" + modalId + " #finished-fields").hide();
                    $("#" + modalId + " #completed-fields").show();
                } else {
                    $("#" + modalId + " #finished-fields").hide();
                    $("#" + modalId + " #completed-fields").hide();
                }
            });

            $(".completion-status").trigger("change"); // Trigger change event on page load to set correct visibility
        });
    </script>';
}

$gameID = $tp->toDB($_POST['game_id']);
$status_platform_id = $tp->toDB($_POST['review_platform']);
$status_completion = $tp->toDB($_POST['completion']);
$status_notes = $tp->toDB($_POST['review_body']);

// Initialize completion time variables
$completion_hours = $completion_minutes = $completion_seconds = 0;

if ($status_completion == 4)
{
    $completion_hours = isset($_POST['finished_hours']) ? intval($_POST['finished_hours']) : 0;
    $completion_minutes = isset($_POST['finished_minutes']) ? intval($_POST['finished_minutes']) : 0;
    $completion_seconds = isset($_POST['finished_seconds']) ? intval($_POST['finished_seconds']) : 0;
}
elseif
($status_completion == 5)
{
    $completion_hours = isset($_POST['completed_hours']) ? intval($_POST['completed_hours']) : 0;
    $completion_minutes = isset($_POST['completed_minutes']) ? intval($_POST['completed_minutes']) : 0;
    $completion_seconds = isset($_POST['completed_seconds']) ? intval($_POST['completed_seconds']) : 0;
}

$completion_time_seconds = ($completion_hours * 3600) + ($completion_minutes * 60) + $completion_seconds;

if(!empty($gameID))
{
    if(USER)
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $isDeleted = false;

            if(isset($_POST['delete']))
            {
                    // Delete from bookmarks table
                    $deleteBookmarkWhere = 'status_user_id = ' . intval(USERID) . ' AND status_game_id = ' . intval($gameID);
                    $deleteBookmark = $sql->delete('bookmarks', $deleteBookmarkWhere);

                    // Debugging - Log bookmark deletion
                    print_a("Deleting bookmark for game ID: " . $gameID);
                    print_a("Bookmark Deletion Success: " . ($deleteBookmark ? "Yes" : "No"));
                    print_a($sql);
                    // Delete from completion_times table (if exists)
                    $deleteCompletionWhere = 'user_id = ' . intval(USERID) . ' AND game_id = ' . intval($gameID);
                    $deleteCompletion = $sql->delete('completion_times', $deleteCompletionWhere);

                    // Set flag to indicate deletion happened
                    $isDeleted = true;

                    // Debugging - Log completion times deletion
                    print_a("Deleting completion times for game ID: " . $gameID);
                    print_a("Completion Times Deletion Success: " . ($deleteCompletion ? "Yes" : "No"));
                    print_a($sql);
            }

            if(!$isDeleted)
            {
                // Step 1: Update the bookmark record (general information)
                $whereClause = "status_user_id = " . intval(USERID) . " AND status_game_id = " . intval($gameID);

                // Debugging: Explicit select before update
                $resultBeforeUpdate = $sql->retrieve('bookmarks', '*', $whereClause);
                print_a("Rows before update for game ID: " . $gameID);
                print_a($resultBeforeUpdate);

                // Update should be scoped specifically to the user and game ID
                $updateBookmarkData = [
                    'data' => [
                        'status_platform_id' => $status_platform_id,
                        'status_completion' => $status_completion,
                        'status_notes' => $status_notes
                    ],
                    'WHERE' => 'status_user_id = ' . USERID . ' AND status_game_id = ' . $gameID
                ];
                
                $sql->update('bookmarks', $updateBookmarkData);

                if(!empty($resultBeforeUpdate))
                {
                    $updateBookmark = $sql->update('bookmarks', $updateBookmarkData);

                    // Debugging - Log bookmark update
                    print_a("Updating bookmark record for game ID: " . $gameID);
                    print_a("Bookmark Update Data: ");
                    print_a($updateBookmarkData);
                    print_a("WHERE Clause: " . $whereClause);
                    print_a("Bookmark Update Success: " . ($updateBookmark ? "Yes" : "No"));
                }
                else
                {
                    print_a("No rows found for the given WHERE clause before update for game ID: " . $gameID);
                }
                print_a($sql);

                // Step 2: Handle updating or inserting the completion time record based on the status_completion value
                if($status_completion == 4)
                {
                    // Handle "finished" status (4)
                    $existsFinished = $sql->retrieve('completion_times', '*', "user_id = " . intval(USERID) . " AND game_id = " . intval($gameID) . " AND completion = 4");

                    if($existsFinished)
                    {
                        // Update the "finished" completion record
                        $updateCompletionData = [
                            'data' => [
                                'completion_time' => $completion_time_seconds
                            ],
                            'WHERE' => 'user_id = ' . USERID . ' AND game_id = ' . $gameID . ' AND completion = ' . 4
                        ];
                        $sql->update('completion_times', $updateCompletionData);

                        // Debugging - Log "finished" update
                        print_a("Updating 'finished' completion time for game ID: " . $gameID);
                        print_a("Completion Update Data: ");
                        print_a($updateCompletionData);
                        print_a("Finished Update Success: " . ($updateFinished ? "Yes" : "No"));
                    }
                    else
                    {
                        // Insert a new "finished" completion record
                        $insertCompletionData = [
                            'user_id' => USERID,
                            'game_id' => $gameID,
                            'completion' => 4,
                            'completion_time' => $completion_time_seconds
                        ];
                        $insertFinished = $sql->insert('completion_times', $insertCompletionData);

                        // Debugging - Log "finished" insertion
                        print_a("Inserting new 'finished' completion time for game ID: " . $gameID);
                        print_a("Completion Insert Data: ");
                        print_a($insertCompletionData);
                        print_a("Finished Insert Success: " . ($insertFinished ? "Yes" : "No"));
                    }
                }
                elseif($status_completion == 5)
                {
                    // Handle "completed" status (5)
                    $existsCompleted = $sql->retrieve('completion_times', '*', "user_id = " . intval(USERID) . " AND game_id = " . intval($gameID) . " AND completion = 5");

                    if($existsCompleted)
                    {
                        // Update the "completed" completion record
                        $updateCompletionData = [
                            'data' => [
                                'completion_time' => $completion_time_seconds
                            ],
                            'WHERE' => 'user_id = ' . USERID . ' AND game_id = ' . $gameID . ' AND completion = ' . 5
                        ];
                        $sql->update('completion_times', $updateCompletionData);

                        // Debugging - Log "completed" update
                        print_a("Updating 'completed' completion time for game ID: " . $gameID);
                        print_a("Completion Update Data: ");
                        print_a($updateCompletionData);
                        print_a("Completed Update Success: " . ($updateCompleted ? "Yes" : "No"));
                    }
                    else
                    {
                        // Insert a new "completed" completion record
                        $insertCompletionData = [
                            'user_id' => USERID,
                            'game_id' => $gameID,
                            'completion' => 5,
                            'completion_time' => $completion_time_seconds
                        ];
                        $insertCompleted = $sql->insert('completion_times', $insertCompletionData);

                        // Debugging - Log "completed" insertion
                        print_a("Inserting new 'completed' completion time for game ID: " . $gameID);
                        print_a("Completion Insert Data: ");
                        print_a($insertCompletionData);
                        print_a("Completed Insert Success: " . ($insertCompleted ? "Yes" : "No"));
                    }
                }

                // Final Debug - Completion summary
                print_a("Status Completion: " . $status_completion);
                print_a("Completion Time Seconds: " . $completion_time_seconds);
                print_a($sql);

                
                    // Delete functionality
                    // Delete from bookmarks table
                    /*$deleteBookmarkWhere = 'status_user_id = ' . $userID . ' AND status_game_id = ' . $gameID;
                    $sql->delete('bookmarks', $deleteBookmarkWhere);
        
                    // Delete from completion_times table (if exists)
                    $deleteCompletionWhere = 'user_id = ' . $userID . ' AND game_id = ' . $gameID;
                    $sql->delete('completion_times', $deleteCompletionWhere);*/
                

                // New function to remove a game from the list and delete completion times if present
                /*function removeGame($gameID, $userID)
                {
                    $sql = e107::getDb();

                    if (USER)
                    {
                        // Delete from bookmarks table
                        $deleteBookmarkWhere = 'status_user_id = ' . intval($userID) . ' AND status_game_id = ' . intval($gameID);
                        $deleteBookmark = $sql->delete('bookmarks', $deleteBookmarkWhere);

                        // Debugging - Log bookmark deletion
                        print_a("Deleting bookmark for game ID: " . $gameID);
                        print_a("Bookmark Deletion Success: " . ($deleteBookmark ? "Yes" : "No"));

                        // Delete from completion_times table (if exists)
                        $deleteCompletionWhere = 'user_id = ' . intval($userID) . ' AND game_id = ' . intval($gameID);
                        $deleteCompletion = $sql->delete('completion_times', $deleteCompletionWhere);

                        // Debugging - Log completion times deletion
                        print_a("Deleting completion times for game ID: " . $gameID);
                        print_a("Completion Times Deletion Success: " . ($deleteCompletion ? "Yes" : "No"));
                    }
                }

                if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['game_id']))
                {
                    $gameID = intval($_GET['game_id']);
                    removeGame($gameID, USERID);
                }*/ 
            }
        }
    }
}

require_once(HEADERF);
run();
require_once(FOOTERF);

?>