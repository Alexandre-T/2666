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

//Breadcrumbs
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

//Chargement des avatars pris
$resultats = Reserve::loadAvatars();
$resultats = array_merge($resultats, Reserve::loadContacts(1));
$resultats = array_merge($resultats, Reserve::loadContacts(2));
$resultats = array_merge($resultats, Reserve::loadContacts(3));
$resultats = array_merge($resultats, Reserve::loadContacts(4));
//Chargement des avatars réservés
$resultats = array_merge($resultats, Reserve::loadFromFile());
//Tri du tableau
ksort($resultats,SORT_LOCALE_STRING);
//Création du tableau avec les lettres
$final = array_flip(range('A', 'Z'));
array_walk($final,'at_return_array');
//Création du tableau de résultats
foreach($resultats as $key => $avatar){
    $final[strtoupper($key[0])][$key] = $avatar;
}
//echo('<pre>');var_dump($final,$resultats);die();
foreach ($final as $clef => $lettres){
    // categories in this example are "food" and "animal"
    $template->assign_block_vars('lettres', array(
        'LETTRE'    => $clef,
    ));
    
    // each item within the category is assigned to the second block.
    foreach ($lettres as $key => $row)
    {
        $template->assign_block_vars('lettres.avatars', $row);
    }
}

//Construction de l'entête
page_header($user->lang['AVATAR_TITRE']);

//Appel de la bonne page
$template->set_filenames(array(
	'body' => 'avatars.html')
);

//Construction du pied de page
page_footer();

function at_return_array(&$item){
    $item = array();
}
?>