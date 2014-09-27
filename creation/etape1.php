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
define('CREATION_ETAPE', 1);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../jeuderole/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_creation.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/creation');

//Vérification des droits
creation_verification(CREATION_ETAPE);

//Chargement des champs de profil
$user->get_profile_fields($user->data['user_id']);

$submit = (isset($_POST['submit'])) ? true : false;
if ($submit){
    //Analyse et traitement de la variable posté
    $sexe  = request_var('sexe', 0);
    $sexe = ($sexe !== AT_FEMME)?AT_HOMME:AT_FEMME;
    $cp_data['pf_sexe'] = $sexe;    
    if ($sexe != $user->profile_fields['pf_sexe']){
        //Attention, changement de sexe
        //On réinitialise l'avatar
        $cp_data['pf_avatar'] = '';
    }
    //Enregistrement
    $cp = new custom_profile();
    $cp->update_profile_field_data($user->data['user_id'], $cp_data);
    unset($user->profile_fields);
    header('Location: etape2.php');
    die();    
}

//Initialisation des variables
if (empty( $user->profile_fields['pf_sexe'])|| AT_FEMME != $user->profile_fields['pf_sexe']){
	$sexe = AT_HOMME;
}else{
	$sexe = $user->profile_fields['pf_sexe'];
}

//Gestion du message d'erreur
$message = request_var('message', 0);

//Transmission à la vue
$template->assign_vars(array(
	'FEMME_CHECKED' => ($sexe==AT_FEMME)?'checked="checked"':'',
	'HOMME_CHECKED' => ($sexe==AT_HOMME)?'checked="checked"':'',
	'HIDDEN_FIELDS' => build_hidden_fields(array('from'=> CREATION_ETAPE )),
	'AT_HOMME' 		=> AT_HOMME,
	'AT_FEMME' 		=> AT_FEMME, 
	'S_MESSAGE'	  	=> 1 === $message,
));

// Output page
page_header($user->lang['CREATION_ETAPE1']);

$template->set_filenames(array(
	'body' => 'creation/etape1.html')
);

page_footer();

?>