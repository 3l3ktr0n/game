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

        echo '<select class="form-control" name="filter">
                <option selected disabled value="">All games</option>
                <!--<option value="numbers">#</option>-->
                <?php
                foreach($filters_array as $letter)  
                {
                ?>
                <option value="<?php echo $letter; ?>"><?php echo $letter; ?></option>
                <?php }  ?>
            </select>';
    }

    function sc_filter_platform()
    {
        if($sql->select('games_platforms', '*'))
        {
            while($row = $sql->fetch())
            {
                $platforms_array[$row["platform_id"]] = $row["platform_name"];
            }
        }
        echo '<select class="form-control" name="platform">
                <option selected disabled value="">All platforms</option>
                <?php
                foreach($platforms_array as $platform_val)
                {
                ?>
                <option value="<?php echo $platform_val; ?>"><?php echo $platform_val; ?></option>
                <?php }  ?>
            </select>';
    }

    function sc_filter_genre()
    {
        if($sql->select('games_genres', '*'))
        {
            while($row = $sql->fetch())
            {
                $genres_array[$row["genre_id"]] = $row["genre_name"];
            }
        }
        echo '<select class="form-control" name="genre">
                <option selected disabled value="">All genres</option>
                <?php 
                foreach($genres_array as $genre_val)
                {
                ?>
                <option value="<?php echo $genre_val; ?>"><?php echo $genre_val; ?></option>
                <?php }  ?>
            </select>';
    }

    function sc_filter_submit()
    {
        echo '<button type="submit" class="btn btn-primary btn-block">Filter</button>';
    }

//game hub

    function sc_title($parm='')
    {
        if($parm == 'no_url') { return $this->var["game_title"]; }
        if($parm == 'sef') { return $this->var["game_sef"]; }

        $urlparms = array(
            'review_id'     => $this->var["game_id"],
            'review_sef'    => $this->var['game_sef'],
        );

        $url = e107::url('reviews', 'review', $urlparms);

        return '<a href="'.$url.'">'.$this->var["game_title"].'</a>';
    }

    function sc_game_title($parm)
    {
        return e107::getParser()->toHTML($this->var['game_title'], true, 'TITLE');
    }

    function sc_game_image($parm=null)
	{
        $tp = e107::getParser();

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

}

?>