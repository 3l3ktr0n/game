<?php

if(!defined('e107_INIT'))
{
	require_once("../../class2.php");
}

if(e_QUERY)
{
    $qs = explode('.', e_QUERY);
    $gameID = intval($qs[0], 0);
    $page = vartrue($qs[1], 'exit');
}

if($gameID == 0)
{
	e107::redirect();
	exit;
}
if($page == 'exit')
{
	e107::redirect();
	exit;
}

var_dump($gameID);
var_dump($page);

switch($page)
{
    case 'articles':
        echo 'articles';
    break;
    case 'reviews':
        echo 'reviews';
    break;
    case 'images':
        echo 'images';
    break;
    default:
        echo 'default';
}

?>