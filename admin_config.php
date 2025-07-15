<?php

// Generated e107 Plugin Admin Area 

require_once('../../class2.php');
if (!getperms('P')) 
{
	e107::redirect('admin');
	exit;
}

// e107::lan('games',true);

class games_adminArea extends e_admin_dispatcher
{

	protected $modes = array(	
		'main' => array(
			'controller' 	=> 'games_ui',
			'path' 			=> null,
			'ui' 			=> 'games_form_ui',
			'uipath' 		=> null
		),
        'images' => array(
			'controller' 	=> 'images_ui',
			'path' 			=> null,
			'ui' 			=> 'images_form_ui',
			'uipath' 		=> null
		),
        'genres' => array(
			'controller' 	=> 'genres_ui',
			'path' 			=> null,
			'ui' 			=> 'genres_form_ui',
			'uipath' 		=> null
		),
		'platforms' => array(
			'controller' 	=> 'platforms_ui',
			'path' 			=> null,
			'ui' 			=> 'platforms_form_ui',
			'uipath' 		=> null
		)
	);	
	
	protected $adminMenu = array(
		'main/list'			=> array('caption'=> LAN_MANAGE, 'perm' => 'P'),
		'main/create'		=> array('caption'=> LAN_CREATE, 'perm' => 'P'),
        'main/div0'         => array('divider'=> true),
        'images/list'       => array('caption'=> LAN_CATEGORIES, 'perm' => 'P'),
		'images/create' 	=> array('caption'=> LAN_NEWS_63, 'perm' => 'P'),
        'images/div0'       => array('divider'=> true),
        'genres/list'       => array('caption'=> LAN_CATEGORIES, 'perm' => 'P'),
		'genres/create' 	=> array('caption'=> LAN_NEWS_63, 'perm' => 'P'),
        'genres/div0'       => array('divider'=> true),
		'platforms/list' 	=> array('caption'=> LAN_PREFS, 'perm' => 'P'),
	    'platforms/create'	=> array('caption'=> LAN_NEWS_64, 'perm' => 'P')
		// 'main/div0'      => array('divider'=> true),
		// 'main/custom'		=> array('caption'=> 'Custom Page', 'perm' => 'P'),	
	);

	protected $adminMenuAliases = array(
		'main/edit'	=> 'main/list'				
	);	
	
	protected $menuTitle = 'Games';

}
	
class games_ui extends e_admin_ui
{

		protected $pluginTitle		= 'Games';
		protected $pluginName		= 'games';
	//	protected $eventName		= 'games-games'; // remove comment to enable event triggers in admin. 		
		protected $table			= 'games';
		protected $pid				= 'game_id';
		protected $perPage			= 10; 
		protected $batchDelete		= true;
		protected $batchExport      = true;
		protected $batchCopy		= true;

		protected $tabs				= array('1','Images'); // Use 'tab'=>0  OR 'tab'=>1 in the $fields below to enable. 
	
		protected $listOrder		= 'game_id DESC';
	
		protected $fields = array(

			'checkboxes' => array(
                'title'         => '',
                'type'          => null,
                'data'          => null,
                'width'         => '5%',
                'thclass'       => 'center',
                'forced'        => true,
                'class'         => 'center',
                'toggle'        => 'e-multiselect',
                'readParms'     => array(),
                'writeParms'    => array()
            ),

			'game_id' => array(
                'title'         => LAN_ID,
                'data'          => 'int',
                'width'         => '5%',
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_title' => array(
                'title'         => 'Title',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'game_sef' => array(
				'title'         => LAN_SEFURL,
				'type'          => 'text',
				'data'          => 'str',
				'nolist'        => true,
				'width'         => 'auto',
				'thclass'       => '',
				'class'         => null,
				'nosort'        => false,
				'writeParms'    => array('size' => 'xxlarge', 'show' => 1, 'sef' => 'Title')
            ),		

			'game_keywords' => array(
                'title'         => 'Keywords',
                'type'          => 'tags',
                'data'          => 'safestr',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => array('maxlength' => 255, 'maxItems' => 30),
                'writeParms'    => array('maxItems' => 30, 'maxlength' => 255),
                'nosort'        => false,
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_genres' => array(
				'title'         => 'Genres',
				'type'          => 'method',
				'data'          => 'str',
				'width'         => 'auto',
				//'nolist' => true,
				'readParms'     => array(),
				'writeParms'    => array(),
				'class'         => 'left',
				'thclass'       => 'left'
			),

            'game_platforms' => array(
                                'title'         => 'Platforms',
                                'type'          => 'method',
                                'data'          => 'str',
                                'width'         => 'auto',
                                //'nolist' => true,
                                'readParms'     => array(),
                                'writeParms'    => array(),
                                'class'         => 'left',
                                'thclass'       => 'left'
                        ),


			'game_developer' => array(
                'title'         => 'Developer',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'game_publisher' => array(
                'title'         => 'Publisher',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_release_date' => array(
                'title'         => 'ReleaseDate',
                'type'          => 'datestamp',
                'data'          => 'int',
                'width'         => 'auto',
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => 'auto=1&type=date'
            ),

			'game_players' => array(
                'title'         => 'Players',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),
			
			'game_content' => array(
                'title'         => 'Content',
                'type'          => 'bbarea',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_cover' => array(
                'tab'           => 1,
                'title'         => 'Media',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'help'          => '',
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot1' => array(
                'tab'           => 1,
                'title'         => 'Screenshot1',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot2' => array(
                'tab'           => 1,
                'title'         => 'Screenshot2',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    =>  array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot3' => array(
                'tab'           => 1,
                'title'         => 'Screenshot3',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot4' => array(
                'tab'           => 1,
                'title'         => 'Screenshot4',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot5' => array(
                'tab'           => 1,
                'title'         => 'Screenshot5',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot6' => array(
                'tab'           => 1,
                'title'         => 'Screenshot6',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),
			
			'options' => array(
                'title'         => LAN_OPTIONS,
                'type'          => null,
                'data'          => null,
                'width'         => '10%',
                'thclass'       => 'center last',
                'class'         => 'center last',
                'forced'        => true,
                'readParms'     => array(),
                'writeParms'    => array()
            )
            
		);		
		
		protected $fieldpref = array();
		
	//	protected $preftabs        = array('General', 'Other' );
		protected $prefs = array(
		); 

		public function init()
		{
			// This code may be removed once plugin development is complete. 
			if(!e107::isInstalled('games'))
			{				
				e107::getMessage()->addWarning("This plugin is not yet installed. Saving and loading of preference or table data will fail.");
			}
			
			// Set drop-down values (if any). 
	
		}

		
		// ------- Customize Create --------
		
		public function beforeCreate($new_data,$old_data)
		{
			return $new_data;
		}
	
		public function afterCreate($new_data, $old_data, $id)
		{
			// do something
		}

		public function onCreateError($new_data, $old_data)
		{
			// do something		
		}		
		
		
		// ------- Customize Update --------
		
		public function beforeUpdate($new_data, $old_data, $id)
		{
			return $new_data;
		}

		public function afterUpdate($new_data, $old_data, $id)
		{
			// do something	
		}
		
		public function onUpdateError($new_data, $old_data, $id)
		{
			// do something		
		}		
		
		// left-panel help menu area. (replaces e_help.php used in old plugins)
		public function renderHelp()
		{
			$caption = LAN_HELP;
			$text = 'Some help text';

			return array('caption'=>$caption,'text'=> $text);

		}
				
}
				
class games_form_ui extends e_admin_form_ui
{
    function game_genres($curVal, $mode)
	{
		$sql = e107::getDb();
        $tp = e107::getParser();

		if($mode == 'read')
		{
            $genres = explode(",", $curVal);
            foreach($genres as $genre)
            {
                $sql->select("games_genres", "genre_name", "genre_id = {$genre}");
                $genre = $sql->fetch();
                $genres = $genre['genre_name'];
                return $genres;
            }
		}
		
		$genres = $sql->retrieve('games_genres', 'genre_id AS value, genre_name AS label', '', true);

		$defaults['selectize'] = array(
			'create'	=> false,
			'maxItems'	=> 6,
			'mode'		=> 'multi',
			'plugins'	=> array('remove_button'),
			'options'	=> $genres
		);

		return $this->text('game_genres', $curVal, 30, $defaults);
	}

    function game_platforms($curVal, $mode)
	{
		$sql = e107::getDb();
        $tp = e107::getParser();

		if($mode == 'read')
		{
            $platforms = explode(",", $curVal);
            foreach($platforms as $platform)
            {
                $sql->select("games_platforms", "platform_name", "platform_id = {$platform}");
                $platform = $sql->fetch();
                return $tp->toBadge($platform['platform_name']);
            }
		}
		
		$platforms = $sql->retrieve('games_platforms', 'platform_id AS value, platform_name AS label', '', true);

		$defaults['selectize'] = array(
			'create'	=> false,
			'maxItems'	=> 6,
			'mode'		=> 'multi',
			'plugins'	=> array('remove_button'),
			'options'	=> $platforms
		);

		return $this->text('game_platforms', $curVal, 30, $defaults);
	}
}

class images_form_ui extends e_admin_form_ui
{
    function game_id($curVal, $mode)
	{
		$sql = e107::getDb();

	    if($mode == 'read')
		{
			$sql->select('games', 'game_title', 'game_id = '.$curVal);
            $game = $sql->fetch();
			return $game['game_title'];
		}
		
		$game = $sql->retrieve('games', 'game_id AS value, game_title AS label', '', true);

		$defaults['selectize'] = array(
			'create'	=> false,
			'maxItems'	=> 1,
			'mode'		=> 'multi',
			'plugins'	=> array('remove_button'),
			'options'	=> $game
		);

		return $this->text('game_id', $curVal, 30, $defaults);
	}
}	

class images_ui extends e_admin_ui
{
		
		protected $table			= 'game_screenshots';
		protected $pid				= 'screenshot_id';
		protected $perPage			= 10; 
		protected $batchDelete		= true;
		protected $batchExport      = true;
		protected $batchCopy		= true;
	
		protected $listOrder		= 'game_id DESC';
	
		protected $fields = array(

			'checkboxes' => array(
                'title'         => '',
                'type'          => null,
                'data'          => null,
                'width'         => '5%',
                'thclass'       => 'center',
                'forced'        => true,
                'class'         => 'center',
                'toggle'        => 'e-multiselect',
                'readParms'     =>  array(),
                'writeParms'    =>  array()
            ),

            'screenshot_id' => array(
                'title'         => LAN_ID,
                'data'          => 'int',
                'width'         => '5%',
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'game_id' => array(
				'title'         => 'Game',
				'type'          => 'method',
				'data'          => 'str',
				'width'         => 'auto',
				//'nolist' => true,
				'readParms'     => array(),
				'writeParms'    => array(),
				'class'         => 'left',
				'thclass'       => 'left'
			),

			'game_screenshot1' => array(
                'tab'           => 1,
                'title'         => 'Screenshot',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'screenshot1_description' => array(
                'title'         => 'Description',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot2' => array(
                'tab'           => 1,
                'title'         => 'Screenshot2',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    =>  array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'screenshot2_description' => array(
                'title'         => 'Description',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot3' => array(
                'tab'           => 1,
                'title'         => 'Screenshot3',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'screenshot3_description' => array(
                'title'         => 'Description',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot4' => array(
                'tab'           => 1,
                'title'         => 'Screenshot4',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'screenshot4_description' => array(
                'title'         => 'Description',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot5' => array(
                'tab'           => 1,
                'title'         => 'Screenshot5',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'screenshot5_description' => array(
                'title'         => 'Description',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'game_screenshot6' => array(
                'tab'           => 1,
                'title'         => 'Screenshot6',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'screenshot6_description' => array(
                'title'         => 'Description',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'game_screenshot7' => array(
                'tab'           => 1,
                'title'         => 'Screenshot6',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'screenshot7_description' => array(
                'title'         => 'Description',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'game_screenshot8' => array(
                'tab'           => 1,
                'title'         => 'Screenshot6',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'screenshot8_description' => array(
                'title'         => 'Description',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'game_screenshot9' => array(
                'tab'           => 1,
                'title'         => 'Screenshot6',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'screenshot9_description' => array(
                'title'         => 'Description',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'game_screenshot10' => array(
                'tab'           => 1,
                'title'         => 'Screenshot6',
                'type'          => 'image',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'readParms'     => 'thumb=80x80',
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

            'screenshot10_description' => array(
                'title'         => 'Description',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'nolist'        => true,
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),
			
			'options' => array(
                'title'         => LAN_OPTIONS,
                'type'          => null,
                'data'          => null,
                'width'         => '10%',
                'thclass'       => 'center last',
                'class'         => 'center last',
                'forced'        => true,
                'readParms'     => array(),
                'writeParms'    => array()
            )

		);		
		
		protected $fieldpref = array();
		
		protected $prefs = array(
		);
			
}

class genres_ui extends e_admin_ui
{
		
		protected $table			= 'games_genres';
		protected $pid				= 'genre_id';
		protected $perPage			= 10; 
		protected $batchDelete		= true;
		protected $batchExport      = true;
		protected $batchCopy		= true;
	
		protected $listOrder		= 'genre_id DESC';
	
		protected $fields = array(

			'checkboxes' => array(
                'title'         => '',
                'type'          => null,
                'data'          => null,
                'width'         => '5%',
                'thclass'       => 'center',
                'forced'        => true,
                'class'         => 'center',
                'toggle'        => 'e-multiselect',
                'readParms'     =>  array(),
                'writeParms'    =>  array()
            ),

			'genre_id' => array(
                'title'         => '',
                'data'          => 'int',
                'width'         => 'auto',
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'genre_name' => array(
                'title'         => '',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'options' => array (
                'title'         => LAN_OPTIONS,
                'type'          => null,
                'data'          => null,
                'width'         => '10%',
                'thclass'       => 'center last',
                'class'         => 'center last',
                'forced'        => true,
                'readParms'     => array(),
                'writeParms'    => array()
            )

		);		
		
		protected $fieldpref = array();
		
		protected $prefs = array(
		);
			
}

class platforms_ui extends e_admin_ui
{
		
        protected $table			= 'games_platforms';
        protected $pid				= 'platform_id';
		protected $perPage			= 10; 
		protected $batchDelete		= true;
		protected $batchExport      = true;
		protected $batchCopy		= true;
	
		protected $listOrder		= 'platform_id DESC';
	
		protected $fields = array(

			'checkboxes' => array(
                'title'         => '',
                'type'          => null,
                'data'          => null,
                'width'         => '5%',
                'thclass'       => 'center',
                'forced'        => true,
                'class'         => 'center',
                'toggle'        => 'e-multiselect',
                'readParms'     =>  array(),
                'writeParms'    =>  array()
            ),

			'platform_id' => array(
                'title'         => '',
                'data'          => 'int',
                'width'         => 'auto',
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'platform_name' => array(
                'title'         => '',
                'type'          => 'text',
                'data'          => 'str',
                'width'         => 'auto',
                'help'          => '',
                'readParms'     => array(),
                'writeParms'    => array(),
                'class'         => 'left',
                'thclass'       => 'left'
            ),

			'options' => array (
                'title'         => LAN_OPTIONS,
                'type'          => null,
                'data'          => null,
                'width'         => '10%',
                'thclass'       => 'center last',
                'class'         => 'center last',
                'forced'        => true,
                'readParms'     => array(),
                'writeParms'    => array()
            )

		);		
		
		protected $fieldpref = array();
		
		protected $prefs = array(
		);
			
}

new games_adminArea();

require_once(e_ADMIN."auth.php");

e107::getAdminUI()->runPage();

require_once(e_ADMIN."footer.php");

exit;

?>