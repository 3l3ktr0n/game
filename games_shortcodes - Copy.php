<?php

if(!defined('e107_INIT'))
{
    exit;
}

class games_shortcodes extends e_shortcode
{

//games
    function sc_filter()
    {
        $filters_array = array('0-9' => '0-9', 'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'H' => 'H', 'I' => 'I', 'J' => 'J', 'K' => 'K', 'L' => 'L', 'M' => 'M', 'N' => 'N', 'O' => 'O', 'P' => 'P', 'Q' => 'Q', 'R' => 'R', 'S' => 'S', 'T' => 'T', 'U' => 'U', 'V' => 'V', 'W' => 'W', 'X' => 'X', 'Y' => 'Y', 'Z' => 'Z');
?><form action="" method="get">
        <div class="form-group">
        <select class="form-control" name="filter">
            <option selected disabled value="">All games</option>
            <!--<option value="numbers">#</option>-->
            <?php
            foreach($filters_array as $letter)  
            {
            ?>
            <option value="<?php echo $letter; ?>"><?php echo $letter; ?></option>
            <?php }  ?>
        </select>
        </div><?php
    }

    function sc_filter_platform()
    {
        $sql = e107::getDb();

        if($sql->select('games_platforms', '*'))
        {
            while($row = $sql->fetch())
            {
                $platforms_array[$row["platform_id"]] = $row["platform_name"];
            }
        }
        ?>
        <div class="form-group">
        <select class="form-control" name="platform">
            <option selected disabled value="">All platforms</option>
            <?php
            foreach($platforms_array as $platform_val)
            {
            ?>
            <option value="<?php echo $platform_val; ?>"><?php echo $platform_val; ?></option>
            <?php }  ?>
        </select>
        </div><?php
    }

    function sc_filter_genre()
    {
        $sql = e107::getDb();

        if($sql->select('games_genres', '*'))
        {
            while($row = $sql->fetch())
            {
                $genres_array[$row["genre_id"]] = $row["genre_name"];
            }
        }
        ?>
        <div class="form-group">
        <select class="form-control" name="genre">
            <option selected disabled value="">All genres</option>
            <?php 
            foreach($genres_array as $genre_val)
            {
            ?>
            <option value="<?php echo $genre_val; ?>"><?php echo $genre_val; ?></option>
            <?php }  ?>
        </select>
        </div><?php
    }

    function sc_filter_submit()
    {
        echo '<button type="submit" class="btn btn-primary btn-block">Filter</button></form>';
    }

    function sc_image()
    {
        $tp = e107::getParser();

        if($this->var['game_cover'] != "")
        {
            $tp->setThumbSize(150, 210, 'A');
            $url = $tp->thumbUrl($this->var['game_cover']);
            echo $tp->toImage($url);
        }
        else
        {
            $tp->setThumbSize(250, 150, $crop);
            $url = $tp->thumbUrl('https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg');
            echo $tp->toImage($url);
        }
    }

//game hub

    function sc_title($parm='')
    {
        if($parm == 'no_url') { return $this->var["game_title"]; }
        if($parm == 'sef') { return $this->var["game_sef"]; }

        $urlparms = array(
            'game_id'     => $this->var["game_id"],
            'game_sef'    => $this->var['game_sef'],
        );

        $url = e107::url('games', 'game', $urlparms);

        return '<a href="'.$url.'">'.$this->var["game_title"].'</a>';
    }

    function sc_game_title($parm)
    {
        return e107::getParser()->toHTML($this->var['game_title'], true, 'TITLE');
    }

    function sc_game_image($parm=null)
	{
        $tp = e107::getParser();
		/*$tp = e107::getParser();
		$srcPath = str_replace("{e_MEDIA_IMAGE}", "", $this->var['review_image']);

		if(is_string($parm))
		{
			$parm = array('type'=> $parm);
		}

		$class = (!empty($parm['class'])) ? $parm['class'] : "img-responsive img-fluid img-rounded rounded";
		$dimensions = null;
		$srcset = null;
		$src = '';

		if ($srcPath[0] === '{') // Always resize. Use {SETIMAGE: w=x&y=x&crop=0} PRIOR to calling shortcode to change.
		{
			$src = $tp->thumbUrl($srcPath);
			$dimensions = $tp->thumbDimensions();
			$srcset = $tp->thumbSrcSet($srcPath, array('size' => '2x'));
		}
		else
		{
			// We store SC path in DB now + BC
			$src = $srcPath[0] == '{' ? $tp->replaceConstants($srcPath, 'abs') : e_MEDIA_IMAGE . $srcPath;
		}

		if(isset($tmp['count']) && ($tmp['count'] > 1) && empty($parm['type'])) // link first image by default, but not others.
		{
			$parm['type'] = 'tag';
		}

		$imgParms = array(
			'class'         => $class,
			'alt'           => basename($srcPath),		
			'placeholder'   => varset($parm['placeholder']),
		);

		if(!empty($parm['loading']))
		{
		    $imgParms['loading'] = $parm['loading'];
        }

		$imgTag = $tp->toImage($srcPath, $imgParms);

		if(empty($imgTag))
		{
			return null;
		}

		switch(vartrue($parm['type']))
		{
			case 'src':
				return $src;
			break;

            case 'tag':
			default:
				return "<img class='{$class}' src='{$src}' alt='' style='{$dimensions} {$srcset}'/>";
			break;
		}*/

        if($this->var['game_cover'] != "")
        {
            $imagePath = str_replace("{e_MEDIA_IMAGE}", "", $this->var['game_cover']);
            $imageURL = SITEURL.e_MEDIA_IMAGE.$imagePath;
            $parms = array(); // if not width/height set, the default as set by {SETIMAGE} will be used.
            echo $tp->toImage($imageURL, $parms); 
        }
        else
        {
            $imageURL = "https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg";
        }

	}

    function sc_title_year($parm)
    {
        return e107::getDateConvert()->convert_date($this->var['game_release_date'], '%Y');
    }

    function sc_release_date($parm)
    {
        $release_date = $this->var['game_release_date'];
        $convert = str_replace(array('january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'),
                    array('jaanuar', 'veebruar', 'märts', 'aprill', 'mai', 'juuni', 'juuli', 'august', 'september', 'oktoober', 'november', 'detsember'),
                    strtolower(e107::getDateConvert()->convert_date($release_date, '%d %B %Y')));

        if($release_date < time())
        {
            return 'released on ' . $convert;
        }
        else
        {
            return 'releases on ' . $convert;
        }
    }

    function sc_game_developer()
    {
        return 'developed by ' . $this->var['game_developer'];
    }

    function sc_game_publisher()
    {
        return 'published by ' . $this->var['game_publisher'];
    }

    function sc_game_platform($parm)
    {
        $sql = e107::getDb();
        $tp = e107::getParser();
        
        if($this->var["game_platforms"] != "")
        {
            $platforms = explode(",", $this->var['game_platforms']);

            foreach($platforms as $platform)
            {
                $sql->select("games_platforms", "platform_name", "platform_id = {$platform}");
                $platform = $sql->fetch();
                echo $tp->toBadge($platform['platform_name']) . " ";
            }
        }
    }

    function sc_game_genre($parm)
    {
        $sql = e107::getDb();
        $tp = e107::getParser();

        if($this->var["game_genres"] != "")
        {
            $genres = explode(",", $this->var['game_genres']);

            foreach($genres as $genre)
            {
                $sql->select("games_genres", "genre_name", "genre_id = {$genre}");
                $genre = $sql->fetch();
                echo $tp->toBadge($genre['genre_name']) . " ";
            }
        }
    }

    function sc_own_status()
    {
        $sql = e107::getDb();
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

        $status1 = $sql->count("bookmarks", "(status)", "status_game_id = {$gameID} AND status = 1");
        $status2 = $sql->count("bookmarks", "(status)", "status_game_id = {$gameID} AND status = 2");
        $status3 = $sql->count("bookmarks", "(status)", "status_game_id = {$gameID} AND status = 3");
        //$status = $sql->fetch();
        
        echo '<div style="background: #eee"><div class="row"><span class="glyphicon glyphicon-list" aria-hidden="true"></span> LAN '.$status1.'</div><div class="row"><span class="glyphicon glyphicon-cd" aria-hidden="true"></span> LAN '.$status2.'</div><div class="row"><span class="glyphicon glyphicon-hdd" aria-hidden="true"></span> LAN '.$status3.'</div></div>';
    }

    function sc_completion_status()
    {
        $sql = e107::getDb();
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

        $status1 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 1");
        $status2 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 2");
        $status3 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 3");
        $status4 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 4");
        $status5 = $sql->count("bookmarks", "(status_completion)", "status_game_id = {$gameID} AND status_completion = 5");
        //$status = $sql->fetch();
        echo '<div style="background: #ccc"><div class="row"><span class="glyphicon glyphicon-user" aria-hidden="true"></span> LAN '.$status1.'</div><div class="row"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> LAN '.$status2.'</div><div class="row"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> LAN '.$status3.'</div><div class="row"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> LAN '.$status4.'</div><div class="row"><span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span> LAN '.$status5.'</div></div>';
    }

//images

    function sc_screenshots()
    {
        for($i=1; $i<=6; $i++)
        {
            if($this->var["game_screenshot$i"] != "")
            {
                $imageURL = "";
                $imagePath = "";
                $imagePath = str_replace("{e_MEDIA_IMAGE}", "", $this->var["game_screenshot$i"]);
                $imageURL = SITEURL.e_MEDIA_IMAGE.$imagePath;
                echo $imageURL;
            }
        }
    }

//reviews

    function sc_review_title($parm='')
    {
        if($parm == 'no_url') { return $this->var["review_title"]; }
        if($parm == 'sef') { return $this->var["review_sef"]; }

        $urlparms = array(
            'review_id'     => $this->var["review_id"],
            'review_sef'    => $this->var['review_sef'],
        );

        $url = e107::url('reviews', 'review', $urlparms);

        return '<a href="'.$url.'">'.$this->var["review_title"].'</a>';
    }

    /*function sc_review_title()
    {
        $review_title = $this->var["review_title"];
        return $review_title;
    }*/

    function sc_review_image()
    {                        
        $imagePath = str_replace("{e_MEDIA_IMAGE}", "", $this->var['review_image']);
        $imageURL = SITEURL.e_MEDIA_IMAGE.$imagePath;
        return $imageURL;
    }

        /*$currentReviewID = $this->var['review_id'];
        $reviewID_URL = SITEURL."review.php?id=".$currentReviewID;*/
        
    function sc_review_rating()
    {
        $reviewRating = $this->var['review_rating'];

        return $reviewRating;
    }

    function sc_review_summary()
    {
        $reviewDescription = $this->var['review_summary'];

        return $reviewDescription;
    }

//articles

    function sc_news_title()
    {
        if($parm == 'no_url') { return $this->var["news_title"]; }
        if($parm == 'sef') { return $this->var["news_sef"]; }

        $urlparms = array(
            'news_id'     => $this->var["news_id"],
            'news_sef'    => $this->var['news_sef'],
            'category_id'   => $this->var['category_id'],
            'category_sef'  => $this->var['category_sef'],
        );

        //$url = e107::url('reviews', 'review', $urlparms);

        //return '<a href="'.$url.'">'.$this->var["news_title"].'</a>';

        return "<a href='".e107::getUrl()->create('news/view/item', $urlparms)."'>".$this->var["news_title"]."</a>";
    }

    function sc_news_url()
    {
        //$sql = e107::getDb();
        //$row = $sql->gen("SELECT news_id, news_sef, news_category, category_id, category_sef FROM #news LEFT JOIN #news_category ON news_category = category_id");

        $urlparms = array(
            'news_id'       => $this->var['news_id'],
            'news_sef'      => $this->var['news_sef'],
            'news_category' => $this->var['news_category'],
            'category_id'   => $this->var['category_id'],
            'category_sef'  => $this->var['category_sef'],
        );
    }

    function sc_news_thumbnail()
    {
        $reviewDescription = $this->var['news_thumbnail'];

        return $reviewDescription;
    }

    function sc_newsthumbnail($parm = '') //TODO Add support {NEWSTHUMBNAIL: x=y} format 
	{
		$tmp = $this->handleMultiple($parm, 'all');
		$newsThumb = $tmp['file'];
		
		$class = 'news-thumbnail-'.$tmp['count'];
		$dimensions = null;
		$srcset = null;
		$tp = e107::getParser();
		
		if(!$newsThumb && $parm != 'placeholder')
		{
			return '';
		}
		
		if($vThumb = e107::getParser()->toVideo($newsThumb, array('thumb' => 'src')))
		{
			$src = $vThumb;
			$_src = '#';
			$dimensions = e107::getParser()->thumbDimensions();
		}
		else
		{
			$parms = eHelper::scDualParams($parm);
			
			if(empty($parms[2])) // get {SETIMAGE} values when no parm provided. 
			{
				$parms[2] = array('aw' => $tp->thumbWidth(), 'ah' => $tp->thumbHeight());
			}
			
			if(isset($parms[2]['legacy']) && $parms[2]['legacy'] == true) // Legacy mode - swap out thumbnails for actual images and update paths.  
			{
				if($newsThumb[0] != '{') // Fix old paths. 
				{
					$newsThumb = '{e_IMAGE}newspost_images/'.$newsThumb;	
				}
				
				$tmp = str_replace('newspost_images/thumb_', 'newspost_images/', $newsThumb); // swap out thumb for image. 
				
				if(is_readable(e_IMAGE.$tmp))
				{
					$newsThumb = $tmp;	
				}
				
				unset($parms[2]);
			}
			
			// We store SC path in DB now + BC
			$_src = $src = ($newsThumb[0] == '{' || $parms[1] == 'placeholder') ? e107::getParser()->replaceConstants($newsThumb, 'abs') : e_IMAGE_ABS."newspost_images/".$newsThumb;
		
			if(!empty($parms[2]) || $parms[1] == 'placeholder')
			{
				//  $srcset = "srcset='".$tp->thumbSrcSet($src,'all')."' size='100vw' ";
				$attr = !empty($parms[2]) ? $parms[2] : null;
				$src = e107::getParser()->thumbUrl($src, $attr);
				$dimensions = e107::getParser()->thumbDimensions();
			}
		}
		
		if(empty($parms[1]))
		{
			$parms = array(1 => null);
		}

		$style = !empty($this->param['thumbnail']) ? $this->param['thumbnail'] : '';

		switch($parms[1])
		{
			case 'src':
				return $src;
			break;

			case 'tag':
				return "<img class='news_image ".$class."' src='".$src."' alt='' style='".$style."' {$dimensions} {$srcset} />";
			break;

			case 'img':
				return "<a href='".$_src."' rel='external image'><img class='news_image ".$class."' src='".$src."' alt='' style='".$style."' {$dimensions} {$srcset} /></a>";
			break;

			default:
				return "<a href='".e107::getUrl()->create('news/view/item', $this->var)."'><img class='news_image img-responsive img-fluid img-rounded rounded ".$class."' src='".$src."' alt='' style='".$style."' {$dimensions} {$srcset} /></a>";
			break;
		}
	}

    function handleMultiple($parm, $type='image')
	{
		if(empty($this->var['news_thumbnail']))
		{
			return;	
		}			
		
		$tp = e107::getParser();
	
		$media = explode(",", $this->var['news_thumbnail']);
		$list = array();
		
		foreach($media as $file)
		{
			if($tp->isVideo($file))
			{
				$list['video'][] = $file;	
			}
			else
			{
				$list['image'][] = $file;		
			}	
			
			$list['all'][] = $file;	
		}
		

		if(is_string($parm) || empty($parm['item']))
		{
			$item = 0;	
			$parm = array('item' => 1);
		}
		else 
		{
			$item = ($parm['item'] -1);
		}			
				
			
		$file = varset($list[$type][$item]);
		$count = varset($parm['item'], 1);
		
		return array('file' => $file, 'count' => $count);		
		
	}

    function sc_news_datestamp($parm)
    {
        $news_datestamp = $this->var['news_datestamp'];

        return e107::getDateConvert()->convert_date($news_datestamp, 'relative');
    }

    function sc_news_summary()
    {
        $news_summary = $this->var['news_summary'];

        return $news_summary;
    }

//user reviews

    function sc_user_average()
    {
        if($rating != "")
        {
            echo "<BR>rating<BR>" . round($rating, 1) . "<BR><BR>";
        }
        else
        {
            echo "--<BR>";
        }
    }

    function sc_user_review_statistics_bar1()
    {
        return '
        <div class="row">
            <div class="col-sm-2">
                '. $i . "(" .$this->var. ")" .'
            </div>
            <div class="col-sm-10">
                <div class="" data-width="10" style="background-color: #ccc; width: '.$commentPercentage.'%;"> '.round($commentPercentage, 1).' </div>
            </div>
        </div>';
    }

    function sc_user_review_statistics_bar()
    {
        $sql = e107::getDb();

        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

        $rating = "";

        if($sql->gen("SELECT review_rating FROM #game_user_reviews WHERE review_game_id =" .$gameID))
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

            $commentsNum = $numberOfUsers_Array;
            $totalComments = $commentsNum;
            $totalComments = count($totalComments);

            if($totalComments != 0)
            {
                $numberCommentsNum = "";
                $commentPercentage = "";

                for($i=10; $i>=1; $i--)
                {
                    $numberCommentsNum = $sql->select('game_user_reviews', '*', 'review_rating = "'.$i.'" AND review_game_id = "'.$gameID.'"');

                    $commentPercentage = ((int)$numberCommentsNum > 0) ? (((int)$numberCommentsNum / (int)$totalComments)*100) : 0;
                            
                    echo '
                    
                            <tr>
                                <td>
                                    <span class="p-2">'. $i .'</span>
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

    function sc_user_review_count()
    {
        echo "<BR> average of " .$userReviewCount. " rating(s)<BR><BR>";
    }

    function sc_player_reviews()
    {
        echo "Player Reviews";
    }

    function sc_user_review_title()
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
        
        $data = array(
            'game_id'       => $gameID,
            'game_sef'      => $this->var['game_sef'], 
            'review_id'     => $this->var['review_id'], 
            'review_sef'    => eHelper::title2sef($this->var['review_title'], 'dashl')
        ); 
        $url = e107::url('games', 'review', $data);

        return "<a href='".$url."'>".$this->var['review_title']."</a>";
    }

    function sc_user_review_author($parm=null)
	{
        return "<a href='".e107::getUrl()->create('user/profile/view', array('id' => $this->var['user_id'], 'name' => $this->var['user_name']))."'>".$this->var['user_name']."</a>"; 
	}

    function sc_user_review_date()
    {
        if($this->var['review_edit_datestamp'] > $this->var['review_datestamp'])
        {
            $user_review_date = e107::getDateConvert()->convert_date($this->var['review_edit_datestamp'], 'long');
            return "edited on " . $user_review_date;
        }
        else
        {
            $user_review_date = e107::getDateConvert()->convert_date($this->var['review_datestamp'], 'long');
            return $user_review_date;         
        }
    }

    function sc_user_review_body()
    {
        $tp = e107::getParser();

        $data = array(
            'game_id'       => $this->var['game_id'],
            'game_sef'      => $this->var['game_sef'], 
            'review_id'     => $this->var['review_id'], 
            'review_sef'    => eHelper::title2sef($this->var['review_title'], 'dashl')
        ); 
        $url = e107::url('games', 'reviews', $data);

        $review = $tp->toHTML($this->var['review_body'], true); //strip_tags?

        if (strlen($review) > 500)
        {
            // truncate string
            $stringCut = substr($review, 0, 500);
            $endPoint = strrpos($stringCut, ' ');

            //if the string doesn't contain any space then it will cut without word basis.
            $review = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
            $review .= "... <a href='".$url."'>read more</a>";
        }

        return $review;
    }

    function sc_user_new_review()
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
        
        $data = array(
            'game_id'       => $gameID,
            'game_sef'      => $this->var['game_sef'],
            'new'           => $action = 'new'
        ); 
        $url = e107::url('games', 'newreview', $data);

        return "<a href='".$url."'>Tell us what you think!</a>";
    }

    function sc_user_edit_review()
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
        
        $data = array(
            'game_id'       => $gameID,
            'game_sef'      => $this->var['game_sef'],
            'review_id'     => $this->var['review_id'],
            'review_sef'    => eHelper::title2sef($this->var['review_title'], 'dashl'),
            'edit'          => $action = 'edit'
        );
        $url = e107::url('games', 'editreview', $data);

        /*$data = array(
            'game_id'       => $gameID,
            'game_sef'      => $this->var['game_sef'],
            'review_id'     => $this->var['review_id'],
            'review_sef'    => $this->var['review_sef'],
            'edit'          => $action = 'edit'
        );
        $url = e107::url('games', 'editreview', $data);*/

        if(USERID == $this->var['review_user_id'])
        {
            return "<a href='".$url."'>Edit review</a>";
        }
    }

    function sc_user_review_rating()
    {
        return $this->var['review_rating'];
    }

    function sc_user_review_platform()
    {
        return $this->var['review_platform'] = $this->var['platform_name'];
    }

//navigation

    function sc_navigation_title()
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

        $urlparms = array(
            'game_id'   => $gameID,
            'game_sef'  => $this->var['game_sef'],
        );

        $url = e107::url('games', 'game', $urlparms);

        return '<a href="'.$url.'">'.$this->var["game_title"].'</a>';
    }

    function sc_navigation_articles()
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

        $articles = array(
            'game_id'  => $gameID,
            'game_sef' => $this->var['game_sef'],
            'page'     => $page = 'articles', 
        );

        $url = e107::url('games', 'navigation', $articles);

        return '<a href="'.$url.'">News & Articles</a>';
    }

    function sc_navigation_reviews()
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

        $reviews = array(
            'game_id'  => $gameID,
            'game_sef' => $this->var['game_sef'],
            'page'     => $page = 'reviews', 
        );

        $url = e107::url('games', 'navigation', $reviews);

        return '<a href="'.$url.'">Reviews</a>';
    }

    function sc_navigation_guides()
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

        $guides = array(
            'game_id'  => $gameID,
            'game_sef' => $this->var['game_sef'],
            'page'     => $page = 'guides', 
        );

        $url = e107::url('games', 'navigation', $guides);

        return '<a href="'.$url.'">Guides</a>';
    }

    function sc_navigation_discussions()
    {
        
    }

    function sc_navigation_images()
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

        $images = array(
            'game_id'  => $gameID,
            'game_sef' => $this->var['game_sef'],
            'page'     => $page = 'images', 
        );

        $url = e107::url('games', 'navigation', $images);

        return '<a href="'.$url.'">Images</a>';
    }

    /*function sc_bookmark_game($parm = '')
    {
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;

        if(!USERID)
        {
            return; 
        }

        // check if recipe is already bookmarked by user
        $bookmarked = e107::getDb()->count("game_listings", "(*)", "WHERE listing_user_id =".USERID." AND listing_game_id = ".$gameID.""); 
   
        // Not bookmarked yet, display 'empty' bookmark icon
        if(!$bookmarked)
        {
            $value      = LAN_CB_ADDTOBOOKMARKS;             
        }
        // Already bookmarked, display 'full' bookmark icon
        else
        {
            $value      = LAN_CB_REMOVEFROMBOOKMARKS;
        }

        $text = $value;

        return '<span data-cookbook-action="bookmark" data-cookbook-gameid="'.$gameID.'">'.$text.'</span>';
    }*/

    function sc_bookmark_game($parm = '')
    {
        $sql = e107::getDb();
        $gameID = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(round($_GET['id'])) : 0;
        $userID = USERID;

        if(!USERID)
        {
            return; 
        }

        if(USER)
        {
            $sql->select('bookmarks', 'status', 'status_game_id = "'.$gameID.'" AND status_user_id = "'.$userID.'"');
            $result = $sql->fetch();
            if($result == 0)
            {
                $status = '<div id="ownitButton" class="btn-group">
                            <button type="button" class="btn btn-warning"><span class="btn-label"><i class="glyphicon glyphicon-bookmark"></i></span> Own it?</button>
                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" id="status">
                                <li><a value="1" game="'.$gameID.'" href="">Want it</a></li>
                                <li><a value="2" game="'.$gameID.'" href="">Never played</a></li>
                                <li><a value="3" game="'.$gameID.'" href="">Backlog</a></li>
                                <li><a value="4" game="'.$gameID.'" href="">Own digitally</a></li>
                                <li><a value="5" game="'.$gameID.'" href="">Own physically</a></li>
                            </ul>
                        </div>';
            }
            else
            {
                if($result['status'] == 1)
                {
                    $status = '<button id="removeButton" type="button" game="'.$gameID.'" class="btn btn-labeled btn-danger"><span class="btn-label"><i class="glyphicon glyphicon-remove"></i></span> Want it</button>';
                }
                elseif($result['status'] == 2)
                {
                    $status = '<button id="removeButton" type="button" game="'.$gameID.'" class="btn btn-labeled btn-danger"><span class="btn-label"><i class="glyphicon glyphicon-remove"></i></span> Never played</button>';
                }
                elseif($result['status'] == 3)
                {
                    $status =  '<button id="removeButton" type="button" game="'.$gameID.'" class="btn btn-labeled btn-danger"><span class="btn-label"><i class="glyphicon glyphicon-remove"></i></span> Backlog</button>';
                }
                elseif($result['status'] == 4)
                {
                    $status = '<button id="removeButton" type="button" game="'.$gameID.'" class="btn btn-labeled btn-danger"><span class="btn-label"><i class="glyphicon glyphicon-remove"></i></span> Own digitally</button>';
                }
                elseif($result['status'] == 5)
                {
                    $status = '<button id="removeButton" type="button" game="'.$gameID.'" class="btn btn-labeled btn-danger"><span class="btn-label"><i class="glyphicon glyphicon-remove"></i></span> Own physically</button>';
                }
            }
            return '<div id="buttonContainer">' . $status . '</div>';
        }

        /*if(USER)
        {
            return '<div id="buttonContainer">
                        <div id="ownitButton" class="btn-group" style="display: none;">
                            <button type="button" class="btn btn-warning"><span class="btn-label"><i class="glyphicon glyphicon-bookmark"></i></span> Own it?</button>
                            <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" id="status">
                                <li><a value="1" game="'.$gameID.'" href="#">Want it</a></li>
                                <li><a value="2" game="'.$gameID.'" href="#">Never played</a></li>
                                <li><a value="3" game="'.$gameID.'" href="#">Backlog</a></li>
                                <li><a value="4" game="'.$gameID.'" href="#">Own digitally</a></li>
                                <li><a value="5" game="'.$gameID.'" href="#">Own physically</a></li>
                            </ul>
                        </div>
                    </div>
                    <button id="removeButton" type="button" game="'.$gameID.'" class="btn btn-labeled btn-danger" style="display: none;"></button>';
        }*/
    }

}

?>