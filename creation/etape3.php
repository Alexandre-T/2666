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
define('CREATION_ETAPE', 3);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../jeuderole/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_creation.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/creation');
$user->setup('posting');

//Analyse et traitement de la variable posté
$race  = request_var('race', 0);
$message = request_var('message',0);
$race = ($race !== AT_HUMAIN)?AT_NEPHILIM:AT_HUMAIN; 

$user->get_profile_fields($user->data['user_id']);
$cp_data['pf_race'] = $race;
if ($race != $user->profile_fields['pf_race']){
	//Attention, changement de race
	//On ajoute ici les modifications qui pourraient survenir
	$cp_data['pf_agereel'] = '';
	//On devrait supprimer les dons ou les pouvoirs
}
//Enregistrement
$cp = new custom_profile();
$cp->update_profile_field_data($user->data['user_id'], $cp_data);
unset($user->profile_fields);

//Rechargement après enregistrement
$user->get_profile_fields($user->data['user_id']);

$template->assign_vars(array(
	'FORM_AVATAR'	  	=> $user->profile_fields['pf_avatar'],
));

//Vérification des droits
creation_verification(CREATION_ETAPE);

//Template
$template->assign_vars(array(

	'S_FEMME'			  => AT_FEMME    == $user->profile_fields['pf_sexe'],
	'S_HOMME'			  => AT_HOMME    == $user->profile_fields['pf_sexe'],
	'S_HUMAIN'			  => AT_HUMAIN   == $user->profile_fields['pf_race'],
	'S_NEPHILIM'		  => AT_NEPHILIM == $user->profile_fields['pf_race'],
		
	'S_MESSAGE'	  		  => 1 == $message,
		
	'HIDDEN_FIELDS' 	  => build_hidden_fields(array('from'=> CREATION_ETAPE)),
		
));

// Output page
page_header($user->lang['CREATION_ETAPE3']);

$template->set_filenames(array(
	'body' => 'creation/etape3.html'
));

page_footer();

?>
