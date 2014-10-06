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
define('CREATION_ETAPE', 10);
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

//Vérification des droits
creation_verification(CREATION_ETAPE);

//Chargement des champs de profil
$user->get_profile_fields($user->data['user_id']);

//Calcul du nombre de contact
$nbContact = 2;
if ($user->profile_fields['pf_cc_actif']){
    $nbContact++;
}
if ($user->profile_fields['pf_cd_actif']){
    $nbContact++;
}

$resume = generate_text_for_display($user->profile_fields['pf_resume'], $user->profile_fields['pf_resume_uid'], $user->profile_fields['pf_resume_bit'], 7);

//Assignation de toutes les valeurs comprises dans PF
$template->assign_vars(array(
    //Champ booléen
    'S_CONTACT_3'  =>  $user->profile_fields['pf_cc_actif'],
    'S_CONTACT_4'  =>  $user->profile_fields['pf_cd_actif'],
    
    //Sexe et race sont déjà en place
    
    'PERSONNAGE_AGE'        =>  $user->profile_fields['pf_agereel'],
    'PERSONNAGE_AVATAR'     =>  get_user_avatar($user->data['user_avatar'], $user->data['user_avatar_type'], $user->data['user_avatar_width'], $user->data['user_avatar_height'], 'USER_AVATAR', true),
    'PERSONNAGE_AVATAR_NOM' => $user->profile_fields['pf_avatar'],
    'PERSONNAGE_DON'        =>  $user->profile_fields['pf_don'],
    'PERSONNAGE_CLAN'       =>  get_clan($user->profile_fields['pf_clan'],$user->profile_fields['pf_sexe']),
    
    'PERSONNAGE_NOM'        =>  $user->profile_fields['pf_nom'],
    'PERSONNAGE_PASSE'      =>  $user->profile_fields['pf_passe'],
    'PERSONNAGE_PRENOM'     =>  $user->profile_fields['pf_prenom'],
    'PERSONNAGE_PROFESSION' =>  $user->profile_fields['pf_profession'],
    'PERSONNAGE_POUVOIR'    =>  $user->profile_fields['pf_pouvoir'],
    'PERSONNAGE_RESUME'     =>  $resume,
    
    
    'VOLEUSE_NOM'           =>  $user->profile_fields['pf_voleuse_nom'],
    'VOLEUSE_DESCRIPTION'   =>  $user->profile_fields['pf_voleuse_des'],
    'VOLEUSE_POUVOIR'       =>  $user->profile_fields['pf_voleuse_pouvoir'],
    
    'NOMBRE_DE_CONTACTS'    =>  $nbContact,
    
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    ''  =>  $user->profile_fields['pf_'],
    
        
));
// Output page
page_header($user->lang['CREATION_ETAPE9']);

$template->set_filenames(array(
	'body' => 'creation/etape10.html'
));

page_footer();

?>
