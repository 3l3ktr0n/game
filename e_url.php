<?php
/*
 * e107 Bootstrap CMS
 *
 * Copyright (C) 2008-2015 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 * 
 * IMPORTANT: Make sure the redirect script uses the following code to load class2.php: 
 * 
 * 	if (!defined('e107_INIT'))
 * 	{
 * 		require_once("../../class2.php");
 * 	}
 * 
 */
 
if(!defined('e107_INIT'))
{
    exit;
}

// v2.x Standard  - Simple mod-rewrite module. 

class games_url // plugin-folder + '_url'
{

	function config() 
	{
		$config = array();

		$config['games'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/?([\?].*)?\/?$',
			'sef'			=> '{alias}',
			'redirect'		=> '{e_PLUGIN}games/games.php$1'			
		);

		/*$config['gamespage'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?page=([0-9]+)',
			'sef'			=> '{alias}/?page={page}',
			'redirect'		=> '{e_PLUGIN}games/games.php?page=$1'			
		);
                
        $config['gamesfilter'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?filter=([a-zA-Z]+)',
			'sef'			=> '{alias}/?filter={filter}',
			'redirect'		=> '{e_PLUGIN}games/games.php?filter=$1'			
		);

        $config['gamesfilterpage'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?filter=([a-zA-Z]+)&page=([0-9]+)',
			'sef'			=> '{alias}/?filter={filter}&page={page}',
			'redirect'		=> '{e_PLUGIN}games/games.php?filter=$1&page=$2'			
		);

        $config['gamesplatform'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?platform=([a-zA-Z]+)',
			'sef'			=> '{alias}/?platform={platform}',
			'redirect'		=> '{e_PLUGIN}games/games.php?platform=$1'			
		);

        $config['gamesplatformpage'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?platform=([a-zA-Z]+)&page=([0-9]+)',
			'sef'			=> '{alias}/?platform={platform}&page={page}',
			'redirect'		=> '{e_PLUGIN}games/games.php?platform=$1&page=$2'			
		);

        $config['gamesgenre'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?genre=([a-zA-Z]+)',
			'sef'			=> '{alias}/?genre={genre}',
			'redirect'		=> '{e_PLUGIN}games/games.php?genre=$1'			
		);

        $config['gamesgenrepage'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?genre=([a-zA-Z]+)&page=([0-9]+)',
			'sef'			=> '{alias}/?genre={genre}&page={page}',
			'redirect'		=> '{e_PLUGIN}games/games.php?genre=$1&page=$2'			
		);

        $config['gamesfilterplatform'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?filter=([a-zA-Z]+)&platform=([a-zA-Z]+)',
			'sef'			=> '{alias}/?filter={filter}&platform={platform}',
			'redirect'		=> '{e_PLUGIN}games/games.php?filter=$1&platform=$2'			
		);

        $config['gamesfilterplatformpage'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?filter=([a-zA-Z]+)&platform=([a-zA-Z]+)&page=([0-9]+)',
			'sef'			=> '{alias}/?filter={filter}&platform={platform}&page={page}',
			'redirect'		=> '{e_PLUGIN}games/games.php?filter=$1&platform=$2&page=$3'			
		);

        $config['gamesfiltergenre'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?filter=([a-zA-Z]+)&genre=([a-zA-Z]+)',
			'sef'			=> '{alias}/?filter={filter}&genre={genre}',
			'redirect'		=> '{e_PLUGIN}games/games.php?filter=$1&genre=$2&page=$3'			
		);

        $config['gamesfiltergenrepage'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?filter=([a-zA-Z]+)&genre=([a-zA-Z]+)&page=([0-9]+)',
			'sef'			=> '{alias}/?filter={filter}&genre={genre}&page={page}',
			'redirect'		=> '{e_PLUGIN}games/games.php?filter=$1&genre=$2&page=$3'			
		);

        $config['gamesplatformgenre'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?platform=([a-zA-Z]+)&genre=([a-zA-Z]+)',
			'sef'			=> '{alias}/?platform={platform}&genre={genre}',
			'redirect'		=> '{e_PLUGIN}games/games.php?platform=$1&genre=$2&page=$3'			
		);

        $config['gamesplatformgenrepage'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?platform=([a-zA-Z]+)&genre=([a-zA-Z]+)&page=([0-9]+)',
			'sef'			=> '{alias}/?platform={platform}&genre={genre}&page={page}',
			'redirect'		=> '{e_PLUGIN}games/games.php?platform=$1&genre=$2&page=$3'			
		);

        $config['gamesfilterplatformgenre'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?filter=([a-zA-Z]+)&platform=([a-zA-Z]+)&genre=([a-zA-Z]+)',
			'sef'			=> '{alias}/?filter={filter}&platform={platform}&genre={genre}',
			'redirect'		=> '{e_PLUGIN}games/games.php?filter=$1&platform=$2&genre=$3'			
		);

        $config['gamesfilterplatformgenrepage'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}\/\?filter=([a-zA-Z]+)&platform=([a-zA-Z]+)&genre=([a-zA-Z]+)&page=([0-9]+)',
			'sef'			=> '{alias}/?filter={filter}&platform={platform}&genre={genre}&page={page}',
			'redirect'		=> '{e_PLUGIN}games/games.php?filter=$1&platform=$2&genre=$3&page=$4'			
		);*/

		$config['game'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}/([0-9]+)/([^/]+)/?$',
			'sef'			=> '{alias}/{game_id}/{game_sef}',
			'redirect'		=> '{e_BASE}game.php?id=$1'
		);

        $config['navigation'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}/([0-9]+)/([^/]+)/([^/]+)/?$',
			'sef'			=> '{alias}/{game_id}/{game_sef}/{page}',
			'redirect'		=> '{e_BASE}game.php?id=$1&page=$3'
		);

        $config['review'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}/([0-9]+)/([^/]+)/reviews/([0-9]+)/([^/]+)/?$',
			'sef'			=> '{alias}/{game_id}/{game_sef}/reviews/{review_id}/{review_sef}',
			'redirect'		=> '{e_PLUGIN}games/review.php?id=$1&review=$3'
		);

        /*$config['newreview'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}/([0-9]+)/([^/]+)/reviews/([^/]+)/?$',
			'sef'			=> '{alias}/{game_id}/{game_sef}/reviews/{new}',
			'redirect'		=> '{e_PLUGIN}games/review.php?id=$1&action=$3'
		);*/
		$config['newreview'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}/([0-9]+)/([^/]+)/reviews/([^/]+)/?$',
			'sef'			=> '{alias}/{game_id}/{game_sef}/reviews/{new}',
			'redirect'		=> '{e_PLUGIN}games/review.php?id=$1&action=$3'
		);

        $config['editreview'] = array(
			'alias'         => 'games',
			'regex'			=> '^{alias}/([0-9]+)/([^/]+)/reviews/([0-9]+)/([^/]+)/([^/]+)/?$',
			'sef'			=> '{alias}/{game_id}/{game_sef}/reviews/{review_id}/{review_sef}/{edit}',
			'redirect'		=> '{e_PLUGIN}games/review.php?id=$1&review=$3&action=$5'
		);

        // Allow logged-in users to submit new games
        $config['submitgame'] = array(
                        'alias'         => 'games',
                        'regex'                 => '^{alias}/submit/?$',
                        'sef'                   => '{alias}/submit',
                        'redirect'              => '{e_PLUGIN}games/add_game.php'
                );
		return $config;
	}
	
}

?>
