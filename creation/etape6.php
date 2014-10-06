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
include($phpbb_root_path . 'includes/mods/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_popup.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_creation.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/creation');

//Chargement des champs de profil
$user->get_profile_fields($user->data['user_id']);

//Enregistrement ?
$submit = (isset($_POST['submit'])) ? true : false;
if ($submit){
    //Analyse et traitement de la variable posté
    $cp_data['pf_pouvoir']		    = utf8_normalize_nfc(trim(strip_tags(request_var('pouvoir', '',true))));
    $cp_data['pf_don']		        = utf8_normalize_nfc(trim(strip_tags(request_var('don', '',true))));
    $cp_data['pf_clan']		        = trim(strip_tags(request_var('clan', AT_SANSCLAN)));
    $cp_data['pf_voleuse_nom']		= utf8_normalize_nfc(trim(strip_tags(request_var('voleuse_nom', '',true))));
    $cp_data['pf_voleuse_pouvoir']	= utf8_normalize_nfc(trim(strip_tags(request_var('voleuse_pouvoir', '',true))));
    $cp_data['pf_voleuse_des']	    = utf8_normalize_nfc(trim(strip_tags(request_var('voleuse_description', '',true))));
    
    //Enregistrement
    $cp = new custom_profile();
    $cp->update_profile_field_data($user->data['user_id'], $cp_data);
    unset($user->profile_fields);
    header('Location: etape7.php');
    die();
}

//Vérification des droits
creation_verification(CREATION_ETAPE);

///Generate popup
$messages = get_texts_for_popup(array(POST_CONSEILS_POUVOIR,POST_CONSEILS_VOLEUSE,POST_CONSEILS_DON,POST_CONSEILS_CLAN));

//Gestion du message d'erreur
$message = request_var('message',0);

//Template
$template->assign_vars(array(
	'FORM_POUVOIR'	  			 => $user->profile_fields['pf_pouvoir'],
	'FORM_CLAN'		  			 => $user->profile_fields['pf_clan'],
	'SELECTED_ASMODEEN'          => $user->profile_fields['pf_clan'] == AT_ASMODEEN?'selected':'',
    'SELECTED_INSOUMIS'          => $user->profile_fields['pf_clan'] == AT_INSOUMIS?'selected':'',
    'SELECTED_INFILTRE'          => $user->profile_fields['pf_clan'] == AT_INFILTRE?'selected':'',
    'SELECTED_IZANAGHI'          => $user->profile_fields['pf_clan'] == AT_IZANAGHI?'selected':'',
    'SELECTED_VESTAL'            => $user->profile_fields['pf_clan'] == AT_VESTAL?'selected':'',
    'SELECTED_SANSCLAN'          => $user->profile_fields['pf_clan'] == AT_SANSCLAN?'selected':'',

    'AT_ASMODEEN'               => AT_ASMODEEN,
    'AT_INSOUMIS'               => AT_INSOUMIS,
    'AT_INFILTRE'               => AT_INFILTRE,
    'AT_IZANAGHI'               => AT_IZANAGHI,
    'AT_VESTAL'                 => AT_VESTAL,
    'AT_SANSCLAN'               => AT_SANSCLAN,
    
    'S_MESSAGE'	  		        => 1 == $message,
    
    'FORM_DON'	  				=> $user->profile_fields['pf_don'],
	'FORM_VOLEUSE_NOM'			=> $user->profile_fields['pf_voleuse_nom'],
	'FORM_VOLEUSE_DESCRIPTION' 	=> $user->profile_fields['pf_voleuse_des'],
	'FORM_VOLEUSE_POUVOIR'  	=> $user->profile_fields['pf_voleuse_pouvoir'],
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
		
	'S_PRENOM'			  => $user->profile_fields['pf_prenom'],
	
	'POST_CONSEILS_DON' 	=> $messages[POST_CONSEILS_DON],
	'POST_CONSEILS_POUVOIR' => $messages[POST_CONSEILS_POUVOIR],
	'POST_CONSEILS_VOLEUSE' => $messages[POST_CONSEILS_VOLEUSE],
	'POST_CONSEILS_CLAN' 	=> $messages[POST_CONSEILS_CLAN],
		
		
	'HIDDEN_FIELDS' 	  => build_hidden_fields(array('from'=> CREATION_ETAPE)),
		
));

// Output page
page_header($user->lang['CREATION_ETAPE6']);

$template->set_filenames(array(
	'body' => 'creation/etape6.html'
));

page_footer();

?>
