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
define('CREATION_ETAPE', 4);
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

//Chargement des champs de profil
$user->get_profile_fields($user->data['user_id']);

//Enregistrement ?
$submit = (isset($_POST['submit'])) ? true : false;
if ($submit){
    //Analyse et traitement de la variable posté
    $cp_data['pf_prenom']		= trim(strip_tags(request_var('prenom', '')));
    $cp_data['pf_nom']			= trim(strip_tags(request_var('nom', '')));
    $cp_data['pf_profession']	= trim(strip_tags(request_var('profession', '')));
    $cp_data['pf_passe']	    = trim(strip_tags(request_var('passe', '')));
    $agereel	= request_var('age', 0);
    if (AT_HUMAIN == $user->profile_fields['pf_race']){
        //cas particulier des humains
        $cp_data['pf_agereel'] = '';
    }else{
        //cas particulier des Nephilim
        $cp_data['pf_agereel'] = max(18,min(9999,$agereel));
    }
    
    //Enregistrement
    $cp = new custom_profile();
    $cp->update_profile_field_data($user->data['user_id'], $cp_data);
    unset($user->profile_fields);
    header('Location: etape5.php');
    die();
}

//Vérification des droits
creation_verification(CREATION_ETAPE);

///Generate popup
$messages = get_texts_for_popup(array(POST_CONSEILS_PERSONNAGE));

//Gestion du message d'erreur
$message = request_var('message',0);

//Template
$template->assign_vars(array(

    'FORM_AVATAR'	  	=> $user->profile_fields['pf_avatar'],
    'FORM_AGE_REEL'	  	=> $user->profile_fields['pf_agereel'],
    'FORM_PRENOM'	  	=> $user->profile_fields['pf_prenom'],
    'FORM_NOM'		  	=> $user->profile_fields['pf_nom'],
    'FORM_PROFESSION'  	=> $user->profile_fields['pf_profession'],
    'FORM_PASSE'		=> $user->profile_fields['pf_passe'],
    
	'S_FEMME'			  => AT_FEMME    == $user->profile_fields['pf_sexe'],
	'S_HOMME'			  => AT_HOMME    == $user->profile_fields['pf_sexe'],
	'S_HUMAIN'			  => AT_HUMAIN   == $user->profile_fields['pf_race'],
	'S_NEPHILIM'		  => AT_NEPHILIM == $user->profile_fields['pf_race'],
	'S_MESSAGE'	  		  => 1 == $message,
		
	'S_CREATION'		  => true,
		
	'POST_CONSEILS_PERSONNAGE' => $messages[POST_CONSEILS_PERSONNAGE],
		
	'HIDDEN_FIELDS' 	  => build_hidden_fields(array('from'=> CREATION_ETAPE)),
		
));

// Output page
page_header($user->lang['CREATION_ETAPE4']);

$template->set_filenames(array(
	'body' => 'creation/etape4.html'
));

page_footer();

?>
