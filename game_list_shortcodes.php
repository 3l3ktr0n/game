<?php

if (!defined('e107_INIT')) { exit; }

class game_list_manager_shortcodes extends e_shortcode
{
    protected $gameListData;

    function __construct()
    {
        // We can use this constructor to pass any necessary initialization data.
    }

    public function sc_game_list_name()
    {
        return htmlspecialchars($this->var['list_name']);
    }

    public function sc_game_list_description()
    {
        return htmlspecialchars($this->var['list_description']);
    }

    public function sc_game_list_entry_game_title()
    {
        return htmlspecialchars($this->var['game_title']);
    }

    public function sc_game_list_entry_rank()
    {
        return '<td contenteditable="true" data-entry-id="' . intval($this->var['entry_id']) . '" class="editable-rank">'
            . htmlspecialchars($this->var['rank']) . '</td>';
    }

    public function sc_game_list_entry_notes()
    {
        return '<td contenteditable="true" data-entry-id="' . intval($this->var['entry_id']) . '" class="editable-notes">'
            . htmlspecialchars($this->var['notes']) . '</td>';
    }

    public function sc_game_list_entry_actions()
    {
        return '<a href="#" class="btn btn-danger delete-entry" data-entry-id="' . intval($this->var['entry_id']) . '">Delete</a>';
    }
}

?>