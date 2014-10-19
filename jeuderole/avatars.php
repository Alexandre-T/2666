<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) Alexandre TRANCHANT
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../jeuderole/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_reserve.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

//Chargement de la langue
$user->add_lang('mods/avatars', FALSE, FALSE);

$template->assign_block_vars('navlinks', array(
	'S_IS_CAT'		=> false,
	'S_IS_LINK'		=> true,
	'S_IS_POST'		=> false,
	'FORUM_NAME'	=> 'Présentation du jeu',
	'FORUM_ID'		=> 0,
	'U_VIEW_FORUM'	=> 'presentation-du-jeu/')
);

$template->assign_block_vars('navlinks', array(
	'S_IS_CAT'		=> false,
	'S_IS_LINK'		=> true,
	'S_IS_POST'		=> false,
	'FORUM_NAME'	=> 'Aides de jeu',
	'FORUM_ID'		=> 0,
	'U_VIEW_FORUM'	=> 'aides-de-jeu/')
);

$template->assign_block_vars('navlinks', array(
	'S_IS_CAT'		=> false,
	'S_IS_LINK'		=> true,
	'S_IS_POST'		=> false,
	'FORUM_NAME'	=> 'Avatars réservés',
	'FORUM_ID'		=> 0,
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}avatars.$phpEx"))
);

$resultats = Reserve::loadAvatars();
$resultats = array_merge($resultats, Reserve::loadContacts(1));
$resultats = array_merge($resultats, Reserve::loadContacts(2));
$resultats = array_merge($resultats, Reserve::loadContacts(3));
$resultats = array_merge($resultats, Reserve::loadContacts(4));
$resultats = array_merge($resultats, Reserve::loadFromFile());

ksort($resultats,SORT_LOCALE_STRING);
foreach ($resultats as $row){
    $template->assign_block_vars('avatars', $row);
}

//Construction de l'entête
page_header($user->lang['AVATAR_TITRE']);

//Appel de la bonne page
$template->set_filenames(array(
	'body' => 'avatars.html')
);

//Construction du pied de page
page_footer();

?>