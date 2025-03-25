<?php

if(!defined('e107_INIT'))
{
	require_once("../../class2.php");
}

require_once(HEADERF);

$gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
$reviewID = (isset($_GET['review']) && is_numeric($_GET['review'])) ? abs(round($_GET['review'])) : 0;
$action = (isset($_GET['action']) && ($_GET['action'] == 'new' || $_GET['action'] == 'edit'));
$frm = e107::getForm();
$sql = e107::getDb();
$tp = e107::getParser();
$userID = USERID;

$sql->gen("SELECT game.game_sef, review.review_id, review.review_title, review.review_sef, review.review_user_id FROM #game_user_reviews AS review LEFT JOIN #games AS game ON review_game_id = {$gameID} WHERE game_id = {$gameID} AND review_id = {$reviewID}");
$row = $sql->fetch();
print_a($row);

$reviews = array(
    'game_id' => $gameID,
    'game_sef' => $row['game_sef'],
    'page' => $page = 'reviews'
);
$rurl = e107::url('games', 'navigation', $reviews);

$review = array(
    'game_id' => $gameID,
    'game_sef' => $row['game_sef'],
    'review_id' => $row['review_id'],
    'review_sef' => $row['review_sef']
);
$reviewurl = e107::url('games', 'review', $review);

/*print_a($review);
print_a($reviewurl);*/

if ($action == 'edit' && $reviewID) {
    // Handle the AJAX request to fetch the edit form
    editReview($gameID, $userID, $reviewID);
    exit;
}

$review_id = isset($row['review_id']);

$edit = $sql->gen("SELECT review_user_id FROM #game_user_reviews WHERE review_game_id = {$gameID} AND review_id = {$reviewID} AND review_user_id = {$userID}");
$new = !$sql->gen("SELECT * FROM #game_user_reviews WHERE review_game_id = {$gameID} AND review_user_id = {$userID}");

if($gameID && $review_id && $action != 'edit')
{
    echo 'display display display';
    displayReview($gameID, $reviewID);
}
elseif(USER && $gameID && $new && $action == 'new')
{
    echo 'new new new';
    newReview($gameID, $userID);
}
elseif(USER && $edit && $gameID && $review_id && $action == 'edit')
{
    echo 'edit edit edit';
    editReview($gameID, $userID, $reviewID);
}
else
{
    echo 'what what what';
}

function displayReview($gameID, $reviewID)
{
    global $sql, $frm, $tp, $userID;

    if($sql->gen("SELECT * FROM #game_user_reviews WHERE review_game_id = {$gameID} AND review_id = {$reviewID}"))
    {  
        while($row = $sql->fetch())
        {
            echo "<div class='review-container' style='background-color: #efefef; border-radius: 5px; padding: 10px;'>";
            echo "id: " . $row['review_id'] . "<BR>";
            echo "game id: " . $row['review_game_id'] . "<BR>";
            echo "rating: " . $row['review_rating'] . "<BR>";
            echo "platform: " . $row['review_platform'] . "<BR>";
            echo "title: " . $row['review_title'] . "<BR>";
            echo "review: " . $tp->toHTML($row['review_body'], true) . "<BR>";
            echo "datestamp: " . e107::getDate()->convert_date($row['review_datestamp'], 'short') . "<BR>";
            echo "edit: " . e107::getDate()->convert_date($row['review_edit_datestamp'], 'short') . "<BR>";
            echo "user id: " . $row['review_user_id'] . "<BR>";
            if($userID == $row['review_user_id'])
            {
                echo "<a href='#' class='edit-review-link' data-review-id='{$row['review_id']}'>Edit review</a><br>";
                echo $frm->like('game_user_reviews', $row['review_id']);
            }
            echo "</div><BR>";
        }
    }
}

function editReview($gameID, $userID, $reviewID)
{
    global $sql, $frm;

    $rating = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10);
    $query = "SELECT * FROM #game_user_reviews WHERE review_game_id = {$gameID} AND review_id = {$reviewID} AND review_user_id = {$userID}";
    $sql->gen($query);
    $row = $sql->fetch();

    ?>
        <h2>Edit your review <?php echo $gameID; ?></h2>
        <div class="edit-review-form-container">
    <?php

        //echo $frm->open('edit_review');
        echo $frm->open('edit_review', null, array('class' => 'edit-review-form'));
        echo $frm->select('review_rating', $rating, $row['review_rating']);
        $platforms = $sql->retrieve("SELECT game_platforms FROM #games WHERE game_id = " . $gameID);

        if(!empty($platforms))
        {
            $platform = explode(",", $platforms);
            $platform_array = array_combine($platform, $platform);
            $sql->select("games_platforms", "platform_name", "platform_id = {$row['review_platform']}");
            $review_rating = $sql->fetch();
            $sql->select("games_platforms", "*", "platform_id IN (" . implode(",", $platform) . ")");
            $platforms = array();
            while($test = $sql->fetch())
            {
                $platforms[$test['platform_id']] = $test['platform_name'];
            }
            $test = array_column($test, 'platform_name');  
            echo $frm->select('review_platform', $platforms, $row['review_platform']);
            //print_a($platform_array);
        }

        echo $frm->text('review_title', $row['review_title'], $row['review_title']);
        echo $frm->textarea('review_body', $row['review_body'], $row['review_body'], 'small');
        echo $frm->submit('edit', 'Edit review');
        echo $frm->button('delete', 'Delete');
        echo $frm->close();
        ?>
        </div><?php
}

function newReview($gameID, $userID)
{
    global $sql, $frm;

    $rating = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10);
    ?>
        <h2>Write your review <?php echo $gameID; ?></h2>

            <?php
            echo $frm->open('new_review');
            echo $frm->select('review_rating', $rating);
            $platforms = $sql->retrieve("SELECT game_platforms FROM #games WHERE game_id = " . $gameID);

            if(!empty($platforms))
            {
                $platform = explode(",", $platforms);
                $platform_array = array_combine($platform, $platform);
                $sql->select("games_platforms", "platform_name", "platform_id = {$row['review_platform']}");
                $review_rating = $sql->fetch();
                $sql->select("games_platforms", "*", "platform_id IN (" . implode(",", $platform) . ")");
                $platforms = array();
                while($test = $sql->fetch())
                {
                    $platforms[$test['platform_id']] = $test['platform_name'];
                }
                $test = array_column($test, 'platform_name');  
                echo $frm->select('review_platform', $platforms);
            }

            echo $frm->text('review_title', 'Review Title');
            echo $frm->textarea('review_body', 'Review Body', '', 'small');
            echo $frm->submit('submit', 'Submit review');
            echo $frm->close();
}
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Handle the edit review link click
    $('.edit-review-link').on('click', function(e) {
        e.preventDefault();
        var reviewId = $(this).data('review-id');
        var gameId = <?php echo $gameID; ?>;
        console.log(gameId);
        console.log(reviewId);
        // Perform AJAX request to fetch and display the review form
        $.ajax({
            url: window.location.href.split('?')[0] + '?id=' + gameId + '&review=' + reviewId + '&action=edit',
            method: 'GET',
            success: function(response) {
                $('.edit-review-form-container').remove(); // Remove any existing form
                $('.review-container').append(response); // Append the fetched form
            },
            error: function(response) {
                // Handle error (e.g., show an error message)
                alert('An error occurred while fetching the form. Please try again.');
            }
        });
    });
});
</script>
<?php
if(USER)
{
    if($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if(isset($_POST["submit"]))
        {
            $rating = $tp->toDB($_POST['review_rating']);
            $platform = $tp->toDB($_POST['review_platform']);
            $datestamp = time();
            $title = $tp->toDB($_POST['review_title']);
            $review = $tp->toDB($_POST['review_body']);

            $insert = array(
                'data' => array(
                    'review_game_id' => $gameID,
                    'review_rating' => $rating,
                    'review_platform' => $platform,
                    'review_title' => $title,
                    'review_sef' => eHelper::title2sef($title, 'dashl'),
                    'review_body' => $review,
                    'review_datestamp' => $datestamp,
                    'review_user_id' => $userID
                )
            );
            $sql->insert('game_user_reviews', $insert);

            $sql->gen("SELECT game.game_sef, review.review_id, review.review_title, review.review_sef FROM #game_user_reviews AS review LEFT JOIN #games AS game ON review_game_id = {$gameID} WHERE game_id = {$gameID} AND review_user_id = {$userID} ORDER BY review_id DESC LIMIT 1");
            $row = $sql->fetch();

            $redirect = array(
                'game_id' => $gameID,
                'game_sef' => $row['game_sef'],
                'review_id' => $row['review_id'],
                'review_sef' => $row['review_sef']
            );
            $redirectUrl = e107::url('games', 'review', $redirect);
            e107::redirect($redirectUrl);
            exit;
        }

        if(isset($_POST["edit"]))
        {
            $rating = $tp->toDB($_POST['review_rating']);
            $platform = $tp->toDB($_POST['review_platform']);
            $datestamp = time();
            $title = $tp->toDB($_POST['review_title']);
            $review = $tp->toDB($_POST['review_body']);

            $update = array(
                'data' => array(
                    'review_rating' => $rating,
                    'review_platform' => $platform,
                    'review_title' => $title,
                    'review_sef' => eHelper::title2sef($title, 'dashl'),
                    'review_body' => $review,
                    'review_edit_datestamp' => $datestamp
                ),
                'WHERE' => 'review_game_id = "'.$gameID.'" AND review_id = "'.$reviewID.'" AND review_user_id = "'.$userID.'"'
            );
            $sql->update('game_user_reviews', $update);

            //$sql->gen("SELECT game.game_sef, review.review_id, review.review_title review.review_sef FROM #game_user_reviews AS review LEFT JOIN #games AS game ON review_game_id = {$gameID} WHERE game_id = {$gameID} AND review_user_id = {$userID}");
            //$row = $sql->fetch();

            e107::redirect($reviewurl);
            exit;    
        }

        if(isset($_POST["delete"]))
        {
            $sql->delete('game_user_reviews', 'review_game_id = "'.$gameID.'" AND review_id = "'.$reviewID.'" AND review_user_id = "'.$userID.'"');

            e107::redirect($rurl);
            exit;  
        }
    }
}

require_once(FOOTERF);

?>
<!--
#ff0000
#fe4400
#f86600
#ee8200
#e09b00
#ceb200
#b7c700
#9bdb00
#74ed00
#20ff00
-->