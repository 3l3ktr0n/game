<?php

if(!defined('e107_INIT'))
{
	require_once("../../class2.php");
}

class game_front
{

	public function run()
	{

        e107::js('games','js/ownit.js', 'jquery');
        e107::js('games','js/lightbo_.js', 'jquery');
        e107::css('games', 'css/lightbox_.css');

		$sql = e107::getDb(); 					// mysql class object
		$tp = e107::getParser(); 				// parser for converting to HTML and parsing templates etc.
		$frm = e107::getForm(); 				// Form element class.
		$ns = e107::getRender();				// render in theme box.

		$text = '';

        $sc = e107::getScBatch('games', true);
        $template = e107::getTemplate('games');

        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
        $userID = USERID;
        $page = (isset($_GET['page']) && is_string($_GET['page']));
       
                if($row = $sql->retrieve("games", "*", "WHERE game_id = {$gameID}"))
        {  
            echo $tp->parseTemplate($template['start'], true, $sc);

            $sc->setVars($row); // send the value of the $row to the class.
            echo $tp->parseTemplate($template['item'], false, $sc); // parse the 'item'

            switch($_GET['page'])
            {
                case 'articles':
                    $this->getNews($gameID);
                    break;
                case 'reviews':
                    $this->getReviews($gameID);
                    $this->getUserReviewStatistics($gameID);
                    $this->getUserReviews($gameID);
                    break;
                case 'images':
                    $this->getImages($gameID);
                    break;
                default:
                    $this->getOverview($gameID);
                    $this->getLatestArticles($gameID);
            }

            echo $tp->parseTemplate($template['end'], true, $sc);
     	}
        else
        {
	        echo "Game not found " . $gameID;
	        exit;
        }

    }

    public function getOverview($gameID)
    {
        $sql = e107::getDb();
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
        
        $status1 = $sql->count("bookmarks", "(status)", "status_game_id = {$gameID} AND status = 1");
        $status2 = $sql->count("bookmarks", "(status)", "status_game_id = {$gameID} AND status = 4");
        $status3 = $sql->count("bookmarks", "(status)", "status_game_id = {$gameID} AND status = 5"); 
     
        echo '<div class="container-fluid" style="background-color: #efefef; border-radius: 5px;"><div class="row"><span class="glyphicon glyphicon-list" aria-hidden="true"></span> '.$status1.' users want this game</div><div class="row"><span class="glyphicon glyphicon-hdd" aria-hidden="true"></span> '.$status2.' users own a digital copy of this game</div><div class="row"><span class="glyphicon glyphicon-cd" aria-hidden="true"></span> '.$status3.' users own a physical copy of this game</div></div>';
        
        $completion1 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 1");
        $completion2 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 2");
        $completion3 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 3");
        $completion4 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 4");
        $completion5 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 5");

        echo '<div class="container-fluid" style="background-color: #ececec; border-radius: 5px;"><div class="row"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> '.$completion1.' users dropped this game</div><div class="row"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> '.$completion2.' users have the game waiting in their backlog</div><div class="row"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> '.$completion3.' users are currently playing the game</div><div class="row"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> '.$completion4.' users have finished the game</div><div class="row"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> '.$completion5.' users have 100% completed the game</div></div>';

        echo '<br><div class="container-fluid" style="background-color: #efefef; border-radius: 5px;">
        <div class="col-sm-10">
        <div class="row">
          Developer: Nintendo
        </div>
        <div class="row">
          <div class="">Publisher: Nintendo</div>
        </div>
          <div class="row">
          <div class="">Platform: NS, PS4, XBX</div>
        </div>
        <div class="row">
          <div class="">Genre: platformer</div>
        </div>
          <div class="row">
          <div class="">Number of players: 1</div>
        </div>
        <div class="row">
          <div class="">Initial release: 01 January 2000</div>
        </div>
       </div>
        <div class="col-sm-2">
          <div class="align-self-center"><img class="img-responsive" src="https://pegi.info/sites/default/files/styles/medium/public/2017-03/pegi7.png"></div>
        </div>
      </div>';
    }

    public function getLatestArticles($gameID)
    {
        $sql = e107::getDb();
        $tp = e107::getParser();

		$sc = e107::getScBatch('games', true);
        $template = e107::getTemplate('games');

        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
 
        if($row_article_array = $sql->retrieve("games", "game_keywords", "game_id = {$gameID}"))
        { 
            $row_article_array = explode(",", $row_article_array);     
                
            foreach($row_article_array as $row_article_val)
            {   
                $article_id_array = $sql->select('news', 'news_id', 'news_meta_keywords LIKE "%'.$row_article_val.'%" ORDER BY news_id DESC LIMIT 5');
                    
                if(!empty($row_article_val) && $article_id_array)
                {    
                    while($article_id_val = $sql->fetch())
                    {
                        $article_id_val_array[] = $article_id_val['news_id'];
                    }     
                }      
            }

            $article_id_val_array = array_unique($article_id_val_array);

            $article_tag_id_array = $article_id_val_array;

            foreach($article_tag_id_array as $articleIds)
            {
                $sql->gen("SELECT * FROM #news LEFT JOIN #news_category ON news_category = category_id WHERE news_id = {$articleIds}");

                while($article = $sql->fetch())
                {
                    $sc->setVars($article);
			        echo $tp->parseTemplate($template['article'], true, $sc);
                }
            }
        }
        else
        {
            echo '<div class="alert alert-warning text-center">No news found for this game.</div>';
        }
    }

    public function getReviews($gameID)
	{
        $sql = e107::getDb();
        $tp = e107::getParser();

		$sc = e107::getScBatch('games', true);
        $template = e107::getTemplate('games');

        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

        //$reviewGameQuery = 'WHERE FIND_IN_SET("'.$gameID.'", review_game)';

        if($sql->gen('SELECT * FROM #reviews WHERE FIND_IN_SET("'.$gameID.'", review_game)'))//gen('SELECT * FROM #reviews ' .$reviewGameQuery)
        {
            while($review = $sql->fetch())
            {
			    $sc->setVars($review);
			    echo $tp->parseTemplate($template['review'], true, $sc);
		    }
	    }
        else
        { 
            echo "<div class='alert alert-warning text-center'>We have no review for this game yet. If you've played it, write a review and tell us what you think!</div>"; 
        }
    }

    public function getUserReviewStatistics($gameID)
    {
        $sql = e107::getDb();
        $tp = e107::getParser();

        $sc = e107::getScBatch('games', true);
        $template = e107::getTemplate('games');

        $rating = "";

        if($sql->gen("SELECT review_rating FROM #game_user_reviews WHERE review_game_id = {$gameID}"))
        {
		    $ratingResult = "";

			while($row_rating = $sql->fetch())
            {
				if($row_rating["review_rating"] != 0)
                {
					$ratingResult = $row_rating["review_rating"];							    	
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

            if($rating != "")
            {
                echo "<BR>rating<BR>" . round($rating, 1) . "<BR><BR>";
            }
            else
            {
                echo "--<BR>";
            }
        }

        echo '<div class="container">
                <div class="d-flex row p-2 bg-light rounded-top">
                    <div class="green-tab p-2 px-3 mx-2">
                        <p class="sm-text mb-0">USER AVERAGE RATING</p>
                        <h4>'.round($rating, 1).'</h4>
                    </div>
                    <div class="white-tab p-2 mx-2 text-muted">
                        <p class="sm-text mb-0">USER REVIEWS</p>
                        <h4>'.$numberOfUsers_Array_Count.'</h4>
                    </div>
                </div>
            </div>
                <div class="container"><table class="row rounded" style="background-color: #efefef;"><tbody class="w-100">';
        echo $tp->parseTemplate($template['userreviewstatistics'], true, $sc);
        echo '</tbody></table></div>';
    }

    public function getNews($gameID)
    {
        $sql = e107::getDb();
        $tp = e107::getParser();

		$sc = e107::getScBatch('games', true);
        $template = e107::getTemplate('games');

        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
 
        if($row_news_array = $sql->retrieve('games', 'game_keywords', 'game_id =' .$gameID))
        {  
            $row_news_array = explode(",", $row_news_array);     
                
            foreach($row_news_array as $row_news_val)
            {   
                $news_id_array = $sql->select('news', 'news_id', 'news_meta_keywords LIKE "%'.$row_news_val.'%"');
                    
                if(!empty($row_news_val) && $news_id_array)
                {    
                    while($news_id_val = $sql->fetch())
                    {
                        $news_id_val_array[] = $news_id_val['news_id'];
                    }     
                }      
            }

            $news_id_val_array = array_unique($news_id_val_array);

            $news_tag_id_array = $news_id_val_array;

            foreach($news_tag_id_array as $NewsIds)
            {
                $sql->gen("SELECT * FROM #news LEFT JOIN #news_category ON news_category = category_id WHERE news_id = {$NewsIds}");

                while($article = $sql->fetch())
                {
                    $sc->setVars($article);
			        echo $tp->parseTemplate($template['article'], true, $sc);
                }

            }
        }
        else
        {
            echo '<div class="alert alert-warning text-center">No news found for this game.</div>';
        }
    }

    function getUserReviews($gameID)
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
        $reviewID = (isset($_GET['review']) && is_numeric($_GET['review'])) ? abs(round($_GET['review'])) : 0;
        $userID = USERID;
        $sql = e107::getDb();
        $tp = e107::getParser();

		$sc = e107::getScBatch('games', true);
        $template = e107::getTemplate('games');

        $sql->select("games", "game_sef", "game_id = {$gameID}");
        $row = $sql->fetch();
        $data = array(
            'game_id'   => $gameID,
            'game_sef'  => $row['game_sef'], 
            'new'       => $action = 'new'
        ); 
        $url = e107::url('games', 'newreview', $data);

        if($sql->gen("SELECT * FROM #game_user_reviews WHERE review_game_id = {$gameID} AND review_user_id = {$userID}"))
        {
            echo "<div class='alert alert-success text-center'>You have already submitted a review for this game.</div>";
        }
        else
        {
            echo "<div class='alert alert-success text-center'>Weird. There's no review yet. <a href='".$url."'>Tell us what you think!</a></div>";
        }

        if($sql->gen("SELECT review_id, review_game_id, review_rating, review_platform, review_title, review_sef, review_body, review_datestamp, review_edit_datestamp, review_user_id, game_id, game_title, game_sef, user_id, user_name, platform_id, platform_name FROM #game_user_reviews AS review LEFT JOIN #games AS game ON review.review_game_id = game.game_id LEFT JOIN #user AS user ON review.review_user_id = user.user_id LEFT JOIN #games_platforms AS platforms ON review.review_platform = platforms.platform_id WHERE review_game_id = {$gameID} ORDER BY review_datestamp DESC"))
        {  
            while($row = $sql->fetch())
            {
                $sc->setVars($row);
			    echo $tp->parseTemplate($template['user_review'], true, $sc);
                /*if(USERID == $row['user_id'])
                {
                    echo "<a href='".$eurl."'>Edit review</a>";
                }*/
            }
        }
    }

    function getImages($gameID)
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
        $sql = e107::getDb();
        $tp = e107::getParser();

        $tp->setThumbSize(500, 300, 1);
        //$tp->thumbWidth();
        //$tp->thumbHeight();
        //$tp->thumbCrop(1);
        $att = array('w' => 1024, 'h' => 768, 'x' => 1, 'crop' => 0);
        //$att2 = array('w' => 200, 'h' => 120, 'x' => 1, 'crop' => 1);

        echo '<div class="row">';
        for($i=1; $i<=10; $i++)
        {
            $sql->select("game_screenshots", "game_screenshot{$i}, screenshot{$i}_description", "game_id = {$gameID}");
            while($row = $sql->fetch())
            {
                if($row["game_screenshot{$i}"] != '')
                {
                    $url = $tp->replaceConstants($row["game_screenshot{$i}"], 'abs');
                    $tp->toImage($row["game_screenshot{$i}"], $att);
                    $image = $tp->thumbUrl($row["game_screenshot{$i}"]);
                    //echo $i . ' ' . $row["game_screenshot{$i}"] . '<br>';
                    //echo "<img src='".$image."'>";col-sm-6 col-md-4
                    echo '<div class="col-sm-6 col-md-4"><div class="thumbnail" data-toggle="lightbox" data-title="'.$row["screenshot{$i}_description"].'" data-gallery="example-gallery" href="'.$url.'"><img class="card-img-top img-fluid img-responsive mx-auto" id="myImg" src="'.$image.'"></div></div>';
                }
            }
        }
        echo '</div>';
    }

}

$gameFront = new game_front;

require_once(HEADERF);
$gameFront->run();
require_once(FOOTERF);

exit;

?>