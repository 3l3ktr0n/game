<?php
$CREATE['game_wiki'] = "CREATE TABLE IF NOT EXISTS #game_wiki (
    wiki_id int(10) unsigned NOT NULL auto_increment,
    wiki_game_id int(10) unsigned NOT NULL default 0,
    wiki_title varchar(255) NOT NULL default '',
    wiki_body text NOT NULL,
    wiki_author int(10) unsigned NOT NULL default 0,
    wiki_datestamp int(10) unsigned NOT NULL default 0,
    wiki_edit_datestamp int(10) unsigned NOT NULL default 0,
    wiki_editor int(10) unsigned NOT NULL default 0,
    PRIMARY KEY  (wiki_id)
) ENGINE=MyISAM;";

$CREATE['game_wiki_pending'] = "CREATE TABLE IF NOT EXISTS #game_wiki_pending (
    pending_id int(10) unsigned NOT NULL auto_increment,
    wiki_id int(10) unsigned NOT NULL default 0,
    pending_title varchar(255) NOT NULL default '',
    pending_body text NOT NULL,
    pending_editor int(10) unsigned NOT NULL default 0,
    pending_datestamp int(10) unsigned NOT NULL default 0,
    PRIMARY KEY  (pending_id)
) ENGINE=MyISAM;";
?>
