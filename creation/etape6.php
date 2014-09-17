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
define('CREATION_ETAPE', 6);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../jeuderole/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
include($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_popup.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_creation.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/creation');
$user->setup('posting');

//Analyse et traitement de la variable posté
$cp_data['pf_pouvoir']		    = trim(strip_tags(request_var('pouvoir', '')));
$cp_data['pf_don']		        = trim(strip_tags(request_var('don', '')));
$cp_data['pf_clan']		        = trim(strip_tags(request_var('clan', AT_SANSCLAN)));
$cp_data['pf_voleuse_nom']		= trim(strip_tags(request_var('voleuse_nom', '')));
$cp_data['pf_voleuse_pouvoir']	= trim(strip_tags(request_var('voleuse_pouvoir', '')));
$cp_data['pf_voleuse_des']	    = trim(strip_tags(request_var('voleuse_description', '')));
$message    = request_var('message',0);

$user->get_profile_fields($user->data['user_id']);

//Enregistrement
$cp = new custom_profile();
$cp->update_profile_field_data($user->data['user_id'], $cp_data);
unset($user->profile_fields);

//Rechargement après enregistrement
$user->get_profile_fields($user->data['user_id']);

///Generate popup
$messages = get_texts_for_popup(array(POST_CONSEILS_CONTACT));

//Initialisation des variables,
$a_resume = generate_text_for_edit($user->profile_fields['pf_ca_resume'],$user->profile_fields['pf_ca_uid'],7);

// Generate smiley listing
//generate_smilies('inline', 1);

// Build custom bbcodes array
display_custom_bbcodes();

$template->assign_vars(array(
    'FORM_NOM'	  			    => $user->profile_fields['pf_ca_nom'],
	'MESSAGE'      	  			=> $a_resume['text'],
	'FORM_LIEN'		  			=> $user->profile_fields['pf_ca_lien'],
	'FORM_DESCRIPTION'          => $user->profile_fields['pf_ca_description'],    
));

//Vérification des droits
creation_verification(CREATION_ETAPE);

//Template
$template->assign_vars(array(

	'S_FEMME'	 => AT_FEMME    == $user->profile_fields['pf_sexe'],
	'S_HOMME'	 => AT_HOMME    == $user->profile_fields['pf_sexe'],
	'S_HUMAIN'	 => AT_HUMAIN   == $user->profile_fields['pf_race'],
	'S_NEPHILIM' => AT_NEPHILIM == $user->profile_fields['pf_race'],
	'S_MESSAGE'	 => 1 == $message,
    'S_HELPBLOCK_MESSAGE' => true,    
		
	'POST_CONSEILS_CONTACT'	=> $messages[POST_CONSEILS_CONTACT],
		
	'HIDDEN_FIELDS' 	  => build_hidden_fields(array('from'=> CREATION_ETAPE)),
		
));

// Output page
page_header($user->lang['CREATION_ETAPE6']);

$template->set_filenames(array(
	'body' => 'creation/etape6.html'
));

page_footer();

?>
