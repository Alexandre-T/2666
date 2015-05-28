<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
/**
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './jeuderole/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('accueil');

// Output page
//AT MOD META BEGIN
//page_header($user->lang['INDEX']);
$meta['author']= 'Harahel';
$meta['keywords']='jeu de rôle par forum, jeu, 2666, exode, nephilim, science-fiction, odyssée';
$meta['description']='« 2666, à la recherche d\'une nouvelle Terre » est un jeu de rôle futuriste contant l\'exode de Nephilim et d\'humains contraints de fuir le système solaire.';
page_header('2666. À la recherche d\'une nouvelle Terre...',true, 0, 'forum', $meta);
//AT MOD META END


$template->assign_var('S_ACCUEIL',true);

$template->set_filenames(array(
	'body' => 'accueil_newb.html')
);

page_footer();

?>