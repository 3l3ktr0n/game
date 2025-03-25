<?php

if(!defined('e107_INIT'))
{
	require_once("../../class2.php");
}

class games_front
{

	public function run()
	{
        $frm = e107::getForm();
        $sql = e107::getDb();
        $tp = e107::getParser();
        $ns = e107::getRender();

        $sc = e107::getScBatch('games', true);
        $template = e107::getTemplate('games', 'games_list');

        $filters_array = array('0-9' => '0-9', 'A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'F' => 'F', 'G' => 'G', 'H' => 'H', 'I' => 'I', 'J' => 'J', 'K' => 'K', 'L' => 'L', 'M' => 'M', 'N' => 'N', 'O' => 'O', 'P' => 'P', 'Q' => 'Q', 'R' => 'R', 'S' => 'S', 'T' => 'T', 'U' => 'U', 'V' => 'V', 'W' => 'W', 'X' => 'X', 'Y' => 'Y', 'Z' => 'Z');

        $count = $sql->count('games');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 5;
        $from = ($page - 1) * $perPage;
        //$total = ceil($count / $perPage); 
        $options = array('type' => 'page');

            if($sql->select('games_platforms', '*'))
            {
                while($row = $sql->fetch())
                {
                    $platforms_array[$row["platform_id"]] = $row["platform_name"];
                }
            }
        /*
        ?>
        <BR>
        <div class="container">
        <form action="" method="get">
        <div class="row">
        <div class="col-sm-1">
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
        </div>
        </div>
        <div class="col-sm-5">  
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
        </div>
        </div>
        <?php
            if($sql->select('games_genres', '*'))
            {
                while($row = $sql->fetch())
                {
                    $genres_array[$row["genre_id"]] = $row["genre_name"];
                }
            }
        ?>
        <div class="col-sm-5">
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
        </div>
        </div>
        <div class="col-sm-1">
        <button type="submit" class="btn btn-primary btn-block">Filter</button>
        </div>
        </form>
        </div>
        </div>
        <?php
        */
        $platform = isset($_GET['platform']) ? array_search($_GET['platform'], $platforms_array) : null;

        $genre = isset($_GET['genre']) ? array_search($_GET['genre'], $genres_array) : null;

        $filter = isset($_GET['filter']) ? array_search($_GET['filter'], $filters_array, true) : $filter;

        if($filter == '0-9')
        {
            if(isset($platform))
            {
                if(isset($genre))
                {
                    $query = "SELECT * FROM #games WHERE game_title REGEXP '^[0-9]+' AND game_platforms LIKE '%{$platform}%' AND game_genres LIKE '%{$genre}%' LIMIT {$from}, {$perPage}";
                    $count = "SELECT * FROM #games WHERE game_title REGEXP '^[0-9]+' AND game_platforms LIKE '%{$platform}%' AND game_genres LIKE '%{$genre}%'";
                } else {
                    $query = "SELECT * FROM #games WHERE game_title REGEXP '^[0-9]+' AND game_platforms LIKE '%{$platform}%' LIMIT {$from}, {$perPage}";
                    $count = "SELECT * FROM #games WHERE game_title REGEXP '^[0-9]+' AND game_platforms LIKE '%{$platform}%'";
                }
            }

            if($platform === NULL)
            {
                if(isset($genre))
                {
                    $query = "SELECT * FROM #games WHERE game_title REGEXP '^[0-9]+' AND game_genres LIKE '%{$genre}%' LIMIT {$from}, {$perPage}";
                    $count = "SELECT * FROM #games WHERE game_title REGEXP '^[0-9]+' AND game_genres LIKE '%{$genre}%'";
                } 
                else 
                {
                    $query = "SELECT * FROM #games WHERE game_title REGEXP '^[0-9]+' LIMIT {$from}, {$perPage}";
                    $count = "SELECT * FROM #games WHERE game_title REGEXP '^[0-9]+'";
                }
            }
        }
        elseif(isset($filter))
        {
            if(isset($platform))
            {
                if(isset($genre))
                {
                    $query = "SELECT * FROM #games WHERE game_title LIKE '{$filter}%' AND game_platforms LIKE '%{$platform}%' AND game_genres LIKE '%{$genre}%' LIMIT {$from}, {$perPage}";
                    $count = "SELECT * FROM #games WHERE game_title LIKE '{$filter}%' AND game_platforms LIKE '%{$platform}%' AND game_genres LIKE '%{$genre}%'";
                }
                else
                {
                    $query = "SELECT * FROM #games WHERE game_title LIKE '{$filter}%' AND game_platforms LIKE '%{$platform}%' LIMIT {$from}, {$perPage}";
                    $count = "SELECT * FROM #games WHERE game_title LIKE '{$filter}%' AND game_platforms LIKE '%{$platform}%'";
                }
            }

            if($platform === NULL)
            {
                if(isset($genre))
                {
                    $query = "SELECT * FROM #games WHERE game_title LIKE '{$filter}%' AND game_genres LIKE '%{$genre}%' LIMIT {$from}, {$perPage}";
                    $count = "SELECT * FROM #games WHERE game_title LIKE '{$filter}%' AND game_genres LIKE '%{$genre}%'";
                }
                else
                {
                    $query = "SELECT * FROM #games WHERE game_title LIKE '{$filter}%' LIMIT {$from}, {$perPage}";
                    $count = "SELECT * FROM #games WHERE game_title LIKE '{$filter}%'";
                }
            }
        } 
        else 
        {
            if(isset($platform) && isset($genre))
            {
                $query = "SELECT * FROM #games WHERE game_platforms LIKE '%{$platform}%' AND game_genres LIKE '%{$genre}%' LIMIT {$from}, {$perPage}";
                $count = "SELECT * FROM #games WHERE game_platforms LIKE '%{$platform}%' AND game_genres LIKE '%{$genre}%'";
            }
            if(isset($platform) && $genre === NULL)
            {
                $query = "SELECT * FROM #games WHERE game_platforms LIKE '%{$platform}%' LIMIT {$from}, {$perPage}";
                $count = "SELECT * FROM #games WHERE game_platforms LIKE '%{$platform}%'";
            }
            if($platform === NULL && isset($genre))
            {
                $query = "SELECT * FROM #games WHERE game_genres LIKE '%{$genre}%' LIMIT {$from}, {$perPage}";
                $count = "SELECT * FROM #games WHERE game_genres LIKE '%{$genre}%'";
            }
            if($platform === NULL && $genre === NULL)
            {
                $query = "SELECT * FROM #games LIMIT {$from}, {$perPage}";
                $count = "SELECT * FROM #games";
            }
        }

        $result = $sql->gen($query);
        $count = $sql->gen($count);
        $total = ceil($count / $perPage);

        if($count > $perPage)
        {
            if(isset($filter) || isset($platform) || isset($genre))
            {
                if(isset($filter))
                {
                    $urls = e_REQUEST_SELF.'?filter='.$filter.'&page=--FROM--';
                }
                elseif(isset($platform))
                {
                    $urls = e_REQUEST_SELF.'?platform='.$platform.'&page=--FROM--';
                }
                elseif(isset($genre))
                {
                    $urls = e_REQUEST_SELF.'?genre='.$genre.'&page=--FROM--';
                }

                if(isset($filter) && isset($platform))
                {
                    $urls = e_REQUEST_SELF.'?filter='.$filter.'&platform='.$platform.'&page=--FROM--';
                }
                elseif(isset($filter) && isset($genre))
                {
                    $urls = e_REQUEST_SELF.'?filter='.$filter.'&genre='.$genre.'&page=--FROM--';
                }
                elseif(isset($platform) && isset($genre))
                {
                    $urls = e_REQUEST_SELF.'?platform='.$platform.'&genre='.$genre.'&page=--FROM--';
                }
                
                if(isset($filter) && isset($platform) && isset($genre))
                {
                    $urls = e_REQUEST_SELF.'?filter='.$filter.'&platform='.$platform.'&genre='.$genre.'&page=--FROM--';
                }
            }
            else
            {
                $urls = e_REQUEST_SELF.'?page=--FROM--';
            }
        }

        if($result > 0)  
        {
            $sql->gen($query);
            while($games = $sql->fetch())
            {
                $sc->setVars($games);
                echo $tp->parseTemplate($template['main'], true, $sc);
            }
            echo $frm->pagination($urls, $total, $page, $perPage, $options);
        }  
        else  
        {  
            echo 'Data not Found';
        }

        echo "<div class='center'>" . e107::getForm()->pagination(e107::url('games', 'games'), LAN_BACK) . "</div>";
    }
}

$gamesFront = new games_front;

require_once(HEADERF);
$gamesFront->run();
require_once(FOOTERF);

exit;

?>