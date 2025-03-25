<?php

if(!defined('e107_INIT'))
{
	require_once("../../class2.php");
}

require_once(HEADERF);

$gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
$reviewID = (isset($_GET['review']) && is_numeric($_GET['review'])) ? abs(round($_GET['review'])) : 0;
$action = (isset($_GET['action']) && $_GET['action'] == 'new' || $_GET['action'] == 'edit');
$frm = e107::getForm();
$sql = e107::getDb();
$tp = e107::getParser();
$userID = USERID;

$sql->gen("SELECT game.game_sef, review.review_id, review.review_title, review.review_sef, review.review_user_id FROM #game_user_reviews AS review LEFT JOIN #games AS game ON review_game_id = {$gameID} WHERE game_id = {$gameID} AND review_id = {$reviewID}");
$row = $sql->fetch();
print_a($row);
var_dump($row);

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
    'review_sef'    => $row['review_sef']
);
$reviewurl = e107::url('games', 'review', $review);
print_a($review);
print_a($reviewurl);

$review_id = isset($row['review_id']);
var_dump($review_id);
/*$query = array(
	'PREPARE' => 'SELECT user_id FROM ' . MPREFIX . 'game_comment WHERE game_id=:game_id AND user_id=:user_id',
	'EXECUTE' => array(
		'game_id' => $gameID,
		'user_id' => $userID
	)
);
$test = $sql->db_Query($query, null, 'db_Select');*/

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
    editReview($gameID, $userID);
}
else
{
    //e107::redirect();
    //exit;
    //echo "<div class='alert alert-success text-center'>...</div>";
    echo 'what what what';
}

function displayReview($gameID, $reviewID)
{
    $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
    $reviewID = (isset($_GET['review']) && is_numeric($_GET['review'])) ? abs(round($_GET['review'])) : 0;
    $sql = e107::getDb();
    $userID = USERID;
    $tp = e107::getParser();

    if($sql->gen("SELECT * FROM #game_user_reviews WHERE review_game_id = {$gameID} AND review_id = {$reviewID}"))
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
            echo $wat = eHelper::title2sef('test rtrASAS d', 'dashl');
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

        $query = "SELECT * FROM #game_user_reviews WHERE review_game_id = {$gameID} AND review_id = {$reviewID} AND review_user_id = {$userID}";
        $sql->gen($query);
        $row = $sql->fetch();

        ?>
        <div>
        <h2>Edit your review <?php echo $gameID; ?></h2>
        <form action="" method="post">
        <select name="review_rating" required>
            <option selected value="<?php echo $row['review_rating']; ?>">[<?php echo $row['review_rating']; ?>]</option>
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
        while($rating = $sql->fetch())
        {
            $platform_array[] = $rating["game_platforms"];
        }
        if($platform_array != "")
        {
            $platform_array = explode(",", $platform_array[0]);

            $sql->select("games_platforms", "platform_name", "platform_id = {$row['review_platform']}");
            $review_rating = $sql->fetch();
        ?>	
        <select name="review_platform" required>
            <option selected value="<?php echo $row['review_platform']; ?>">[<?php echo $review_rating['platform_name']; ?>]</option>
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
        <input type="text" name="review_title" required value="<?php echo $row['review_title']; ?>"/><br><br>
        <?php echo $frm->bbarea('review_body', $row['review_body'], 'games', $mediaCat, 'small'); ?>
        <!--<textarea name="review_body" required style="height:200px"/><?php echo $row['review_body']; ?></textarea><br><br>-->
        <input type="submit" value="Edit review" name="edit"/><br />
        </form>
        <form action="" method="post">
        <input type="submit" value="Delete" name="delete" onclick="return confirm('Are you sure you want to commit delete and go back?')"/><br />
        </form>
        </div>
        <?php
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

if(USER)
{

    if(isset($_POST["submit"]))
    {

        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $sql->gen("SELECT game.game_sef, review.review_id, review.title FROM #game_user_reviews AS review LEFT JOIN #games AS game ON review_game_id = {$gameID} WHERE game_id = {$gameID} AND review_user_id = {$userID}");
            $row = $sql->fetch();

            /*$review = array(
                'game_id'       => $gameID,
                'game_sef'      => $row['game_sef'], 
                'review_id'     => $row['review_id'], 
                'review_sef'    => eHelper::title2sef($row['review_title'], 'dashl')
            );
            print_a($review);
            $reviewurl = e107::url('games', 'review', $review);*/

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
                    'reivew_sef' => eHelper::title2sef($title, 'dashl'),
                    'review_body' => $review,
                    'review_datestamp' => $datestamp,
                    'review_user_id' => $userID
                )
            );
            $sql->insert('game_user_reviews', $insert);

            $sql->gen("SELECT game.game_sef, review.review_id, review.title FROM #game_user_reviews AS review LEFT JOIN #games AS game ON review_game_id = {$gameID} WHERE game_id = {$gameID} AND review_user_id = {$userID}");
            $row = $sql->fetch();

            /*$review = array(
                'game_id'       => $gameID,
                'game_sef'      => $row['game_sef'], 
                'review_id'     => $row['review_id'], 
                'review_sef'    => eHelper::title2sef($row['review_title'], 'dashl')
            );
            print_a($review);
            $reviewurl = e107::url('games', 'review', $review);*/

            print_a($reviewurl);
            e107::redirect($reviewurl);
            //header('location:'.$reviewurl); 
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
                    'reivew_sef' => eHelper::title2sef($title, 'dashl'),
                    'review_body' => $review,
                    'review_edit_datestamp' => $datestamp
                ),
                'WHERE' => 'review_game_id = "'.$gameID.'" AND review_id = "'.$reviewID.'" AND review_user_id = "'.$userID.'"'
            );
            $sql->update('game_user_reviews', $update);

            $sql->gen("SELECT game.game_sef, review.review_id, review.review_title review.review_sef FROM #game_user_reviews AS review LEFT JOIN #games AS game ON review_game_id = {$gameID} WHERE game_id = {$gameID} AND review_user_id = {$userID}");
            $row = $sql->fetch();

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