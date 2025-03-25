<?php

include_once('../../class2.php');
require_once(HEADERF);

$gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
$reviewID = (isset($_GET['review']) && is_numeric($_GET['review'])) ? abs(round($_GET['review'])) : 0;
$action = (isset($_GET['action']) && $_GET['action'] == 'new' || $_GET['action'] == 'edit');
$frm = e107::getForm();
$sql = e107::getDb();
$tp = e107::getParser();
$userID = USERID;
$sql->gen("SELECT game.game_sef, review.review_id, review.review_title, review.review_user_id FROM #game_user_reviews AS review LEFT JOIN #games AS game ON review_game_id = {$gameID} WHERE game_id = {$gameID} AND review_id = {$reviewID}");
$row = $sql->fetch();

$reviews = array(
    'game_id'  => $gameID,
    'game_sef' => $row['game_sef'],
    'page'     => $page = 'reviews'
);
$rurl = e107::url('games', 'navigation', $reviews);
$review = array(
    'game_id'       => $gameID,
    'game_sef'      => $row['game_sef'], 
    'review_id'     => $row['review_id'], 
    'review_sef'    => eHelper::title2sef($row['review_title'], 'dashl')
);
$reviewurl = e107::url('games', 'review', $review);

$review_id = isset($row['review_id']);

/*$query = array(
	'PREPARE' => 'SELECT user_id FROM ' . MPREFIX . 'game_comment WHERE game_id=:game_id AND user_id=:user_id',
	'EXECUTE' => array(
		'game_id' => $gameID,
		'user_id' => $userID
	)
);
$test = $sql->db_Query($query, null, 'db_Select');*/

$edit = $sql->gen("SELECT review_user_id FROM #game_user_reviews WHERE review_game_id = {$gameID} AND review_id = {$reviewID} AND review_user_id = {$userID}");

if($gameID && $review_id && $action != 'edit')
{
    echo 'display display display';
    displayReview($gameID, $reviewID);
}
elseif(USER && $gameID && !$sql->gen("SELECT * FROM #game_user_reviews WHERE review_game_id = {$gameID} AND review_user_id = {$userID}") && $action == 'new')
{
    echo 'new new new';
    newReview($gameID, $userID);
}
elseif(USER && $edit && $gameID && $review_id && $action == 'edit')
{
    echo 'edit edit edit';
    editReview($gameID, $userID);
}
else
{
    //e107::redirect();
    //exit;
    echo "<div class='alert alert-danger text-center'>what</div>";
}

function displayReview($gameID, $reviewID)
{
    $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
    $reviewID = (isset($_GET['review']) && is_numeric($_GET['review'])) ? abs(round($_GET['review'])) : 0;
    $sql = e107::getDb();
    $userID = USERID;
    $tp = e107::getParser();

    if($sql->select('game_user_reviews', '*', 'review_game_id = :review_game_id AND review_id = :review_id', array('review_game_id' => $gameID, 'review_id'=> $reviewID)))
    {  
        while($row = $sql->fetch())
        {
            echo "<div style='background-color: #efefef; border-radius: 5px; padding: 10px;'>";
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
                echo "<a href='#'>Edit review</a>";
            }
            echo "</div><BR>";
        }
    }
}

function editReview($gameID, $userID)
{
    $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
    $reviewID = (isset($_GET['review']) && is_numeric($_GET['review'])) ? abs(round($_GET['review'])) : 0;
    $sql = e107::getDb();
    $frm = e107::getForm();
    $userID = USERID;
    $rating_array = array(1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
//print_a($rating_array);
        $query = "SELECT * FROM #game_user_reviews WHERE review_game_id = {$gameID} AND review_id = {$reviewID} AND review_user_id = {$userID}";
        $sql->gen($query);
        $row = $sql->fetch();
        $rating = $row['review_rating'];
        //print_a($rating);
        echo $frm->open('edit_review');
        echo $frm->select('review_rating', $rating_array, $rating, $options);

        $sql->select('games', '*', 'game_id ='.$gameID);
        while($rating = $sql->fetch())
        {
            $platform_array[] = $rating["game_platforms"];
        }
        if($platform_array != "")
        {
            $platform_array = explode(",", $platform_array[0]);

            $sql->select("games_platforms", "platform_name", "platform_id = {$row['review_platform']}");
            $review_rating = $sql->fetch();

            foreach($platform_array as $platform)
            {
                $sql->select("games_platforms", "platform_id, platform_name", "platform_id = {$platform}");
                $platform = $sql->fetch();
                $platforms_array[$platform['platform_id']] = $platform['platform_name'];
            }
        }

        print_a($platforms_array);
        echo $frm->select('review_platform', $platforms_array, $row['review_platform'], $options);
        echo $frm->text('review_title', $row['review_title'], 100, array('size' => 'large')); // returns <input class="tbox input-large" id="my-field" maxlength="100" name="my-field" type="text" value="current_value"></input>
        echo $frm->bbarea('review_body', $row['review_body'], 'games', $mediaCat, 'small');
        //echo $frm->button('delete', 'Delete', 'delete');
        echo $frm->submit('edit', 'Edit');
        //echo $frm->button($name, 'test', 'dropdown', $label = '', $options = array('class' => 'danger', 'align' => 'right'));
        echo $frm->close();

}

function newReview($gameID, $userID)
{
    $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
    $reviewID = (isset($_GET['review']) && is_numeric($_GET['review'])) ? abs(round($_GET['review'])) : 0;
    $sql = e107::getDb();
    $userID = USERID;
    $frm = e107::getForm();
?>
<div>
<h2>Write your review <?php echo $gameID; ?></h2>
<form action="" method="post">
<select name="review_rating" required>
    <option hidden selected disabled value="">Rating</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
    <option value="6">6</option>
    <option value="7">7</option>
    <option value="8">8</option>
    <option value="9">9</option>
    <option value="10">10</option>
</select>
<?php
$sql->select('games', '*', 'game_id ='.$gameID);
while($row = $sql->fetch())
{
    $platform_array[] = $row["game_platforms"];
}
if($platform_array != "")
{
	$platform_array = explode(",", $platform_array[0]);	
?>	
<select name="review_platform" required>
	<option hidden selected disabled value="">Select platform</option>
	<?php 
	foreach($platform_array as $platform)
	{
        $sql->select("games_platforms", "platform_id, platform_name", "platform_id = {$platform}");
        $platform = $sql->fetch();
	?>
	<option value="<?php echo $platform['platform_id']; ?>"><?php echo $platform['platform_name']; ?></option>
	<?php }  ?>
</select>		
<?php } ?>
</select><BR><BR>
<input type="text" name="review_title" required placeholder="Enter title" size="120"/><br><br>
<?php echo $frm->bbarea('review_body', $value, 'forum', $mediaCat, 'small', array('wysiwyg' => 'simplemde')); ?>
<!--<textarea name="review" required placeholder="Write your review..." rows="15" cols="120"/></textarea><br><br>-->
<input type="submit" value="Create review" name="submit"/><br />
</form>
</div>
<?php
}

/*function ewReview($gameID, $userID)
{
    $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
    $reviewID = (isset($_GET['review']) && is_numeric($_GET['review'])) ? abs(round($_GET['review'])) : 0;
    $sql = e107::getDb();
    $userID = USERID;

    $frm = e107::getForm();
    $frm->open('newreview'); 
    $numbers = range(1, 10);
    $selected1 = $defaultBlank1;
    $defaultBlank1 = 'Score';
    echo $frm->select('rating', $numbers, $selected1, $options, $defaultBlank1);

    $sql->select('games', '*', 'id ='.$gameID);
    while($row_cat = $sql->fetch())
    {
        $platform_array[] = $row_cat["platformSelected"];
    }
    if($platform_array != "")
    {
        $platform_array = explode(",", $platform_array[0]);		
    }
    $selected2 = 'Choose platform';
    $defaultBlank2 = 'Choose platform';
    echo $frm->select('platform', $platform_array, $selected2, $options, $defaultBlank2);
    echo $frm->text('title', 'Enter title...', 120, 120, array('size' => 'large')); // returns <input class="tbox input-large" id="my-field" maxlength="100" name="my-field" type="text" value="current_value"></input>
    echo $frm->bbarea('review', $value, 'forum', $mediaCat, 'small', array('wysiwyg' => 'simplemde'));
    echo $frm->button('submit', $value, 'submit', 'Submit', $options);
    echo $frm->button('delete', $value, 'delete', 'Delete', $options);
    $frm->close();
}*/

if(USER)
{
    if(isset($_POST["submit"]))
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
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
                    'review_body' => $review,
                    'review_datestamp' => $datestamp,
                    'review_user_id' => $userID
                )
            );
            $sql->insert('game_user_reviews', $insert);

$sql->gen("SELECT game.game_sef, review.review_id, review.title FROM #game_user_reviews AS review LEFT JOIN #games AS game ON review_game_id = {$gameID} WHERE game_id = {$gameID} AND review_user_id = {$userID}");
$row = $sql->fetch();
$review = array(
    'game_id'       => $gameID,
    'game_sef'      => $row['game_sef'], 
    'review_id'     => $row['review_id'], 
    'review_sef'    => eHelper::title2sef($row['review_title'], 'dashl')
);            print_a($review);
$reviewurl = e107::url('games', 'review', $review);
            $mes = e107::getMessage();
            $mes->addSuccess('You did it!');
            echo $mes->render();
            print_a($reviewurl);
            e107::redirect($reviewurl);
            exit;
        }
    }

    if(isset($_POST["edit"]))
    {

        if($_SERVER['REQUEST_METHOD'] == 'POST')
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
                    'review_body' => $review,
                    'review_edit_datestamp' => $datestamp
                ),
                'WHERE' => 'review_game_id = "'.$gameID.'" AND review_id = "'.$reviewID.'" AND review_user_id = "'.$userID.'"'
            );
            $sql->update('game_user_reviews', $update);
            print_a($update);

print_a($query);
            print_a($data);
            print_a($url);
            $mes = e107::getMessage();
            $mes->addSuccess('You did it!');
            echo $mes->render();
            e107::redirect($reviewurl);
            exit;
            //header('location:'.$url);     
        }
    }

    if(isset($_POST["delete"]))
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $sql->delete('game_user_reviews', 'review_game_id = "'.$gameID.'" AND review_id = "'.$reviewID.'" AND review_user_id = "'.$userID.'"');

            $mes = e107::getMessage();
            $mes->addSuccess('You did it!');
            echo $mes->render();
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