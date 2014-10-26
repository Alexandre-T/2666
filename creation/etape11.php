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
define('CREATION_ETAPE', 11);
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

$submit = (isset($_POST['submit'])) ? true : false;
if ($submit){
    group_user_add(GROUPE_DEMANDE_CREATION,array($user->data['user_id']),array($user->data['username']));

    $meta_info = append_sid("{$phpbb_root_path}index.$phpEx");
    $message = $user->lang['DEMANDE_DE_VALIDATION_OK'] . '<br /><br /><a href="' . "{$phpbb_root_path}espace-detente" . '">' . $user->lang['RETURN_ESPACE_DETENTE'].  '</a>';
    $message .= '<br /><br />' . sprintf($user->lang['RETURN_INDEX'], '<a href="' . $meta_info . '">', '</a>');
    meta_refresh(30, $meta_info);
    trigger_error($message);
}

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

$resume          = generate_text_for_display($user->profile_fields['pf_resume'], $user->profile_fields['pf_resume_uid'], $user->profile_fields['pf_resume_bit'], 7);
$contact1_resume = generate_text_for_display($user->profile_fields['pf_ca_resume'], $user->profile_fields['pf_ca_uid'], $user->profile_fields['pf_ca_bit'], 7);
$contact2_resume = generate_text_for_display($user->profile_fields['pf_cb_resume'], $user->profile_fields['pf_cb_uid'], $user->profile_fields['pf_cb_bit'], 7);
$contact3_resume = generate_text_for_display($user->profile_fields['pf_cc_resume'], $user->profile_fields['pf_cc_uid'], $user->profile_fields['pf_cc_bit'], 7);
$contact4_resume = generate_text_for_display($user->profile_fields['pf_cd_resume'], $user->profile_fields['pf_cd_uid'], $user->profile_fields['pf_cd_bit'], 7);

//Assignation de toutes les valeurs comprises dans PF
$template->assign_vars(array(
    
    //Champ booléen
    'S_CONTACT_3'  =>  $user->profile_fields['pf_cc_actif'],
    'S_CONTACT_4'  =>  $user->profile_fields['pf_cd_actif'],
    
    //Sexe et race sont déjà en place
    
    'PERSONNAGE_AGE'        =>  $user->profile_fields['pf_agereel'],
    'PERSONNAGE_AVATAR'     =>  get_user_avatar($user->data['user_avatar'], $user->data['user_avatar_type'], $user->data['user_avatar_width'], $user->data['user_avatar_height'], 'USER_AVATAR', true),
    'PERSONNAGE_AVATAR_NOM' =>  $user->profile_fields['pf_avatar'],
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
    
    //Contact 1
    'CONTACT1_NOM'          =>  $user->profile_fields['pf_ca_nom'],
    'CONTACT1_AVATAR_NOM'   =>  $user->profile_fields['pf_ca_avatar_name'],
    'CONTACT1_DESCRIPTION'  =>  $user->profile_fields['pf_ca_description'],
    'CONTACT1_RESUME'       =>  $contact1_resume,
    'CONTACT1_AVATAR'       =>  get_contact_avatar(1, $user->profile_fields['pf_ca_avatar'], $user->profile_fields['pf_ca_avatar_type'], $user->profile_fields['pf_ca_avatar_width'], $user->profile_fields['pf_ca_avatar_height']),
    'CONTACT1_SEXE'         =>  (AT_HOMME == $user->profile_fields['pf_ca_sexe'])?'Homme':'Femme',
    'CONTACT1_CLAN'         =>  get_clan($user->profile_fields['pf_ca_clan'], $user->profile_fields['pf_ca_sexe']),
    'CONTACT1_RACE'         =>  get_race($user->profile_fields['pf_ca_race'], $user->profile_fields['pf_ca_sexe']),

    //Contact 2
    'CONTACT2_NOM'          =>  $user->profile_fields['pf_cb_nom'],
    'CONTACT2_AVATAR_NOM'   =>  $user->profile_fields['pf_cb_avatar_name'],
    'CONTACT2_DESCRIPTION'  =>  $user->profile_fields['pf_cb_description'],
    'CONTACT2_RESUME'       =>  $contact2_resume,
    'CONTACT2_AVATAR'       =>  get_contact_avatar(2, $user->profile_fields['pf_cb_avatar'], $user->profile_fields['pf_cb_avatar_type'], $user->profile_fields['pf_cb_avatar_width'], $user->profile_fields['pf_cb_avatar_height']),
    'CONTACT2_SEXE'         =>  (AT_HOMME == $user->profile_fields['pf_cb_sexe'])?'Homme':'Femme',
    'CONTACT2_CLAN'         =>  get_clan($user->profile_fields['pf_cb_clan'], $user->profile_fields['pf_cb_sexe']),
    'CONTACT2_RACE'         =>  get_race($user->profile_fields['pf_cb_race'], $user->profile_fields['pf_cb_sexe']),
    
    //Contact 3
    'CONTACT3_NOM'          =>  $user->profile_fields['pf_cc_nom'],
    'CONTACT3_AVATAR_NOM'   =>  $user->profile_fields['pf_cc_avatar_name'],
    'CONTACT3_DESCRIPTION'  =>  $user->profile_fields['pf_cc_description'],
    'CONTACT3_RESUME'       =>  $contact3_resume,
    'CONTACT3_AVATAR'       =>  get_contact_avatar(3, $user->profile_fields['pf_cc_avatar'], $user->profile_fields['pf_cc_avatar_type'], $user->profile_fields['pf_cc_avatar_width'], $user->profile_fields['pf_cc_avatar_height']),
    'CONTACT3_SEXE'         =>  (AT_HOMME == $user->profile_fields['pf_cc_sexe'])?'Homme':'Femme',
    'CONTACT3_CLAN'         =>  get_clan($user->profile_fields['pf_cc_clan'], $user->profile_fields['pf_cc_sexe']),
    'CONTACT3_RACE'         =>  get_race($user->profile_fields['pf_cc_race'], $user->profile_fields['pf_cc_sexe']),
    

    //Contact 4
    'CONTACT4_NOM'          =>  $user->profile_fields['pf_cd_nom'],
    'CONTACT4_AVATAR_NOM'   =>  $user->profile_fields['pf_cd_avatar_name'],
    'CONTACT4_DESCRIPTION'  =>  $user->profile_fields['pf_cd_description'],
    'CONTACT4_RESUME'       =>  $contact4_resume,
    'CONTACT4_AVATAR'       =>  get_contact_avatar(4, $user->profile_fields['pf_cd_avatar'], $user->profile_fields['pf_cd_avatar_type'], $user->profile_fields['pf_cd_avatar_width'], $user->profile_fields['pf_cd_avatar_height']),
    'CONTACT4_SEXE'         =>  (AT_HOMME == $user->profile_fields['pf_cd_sexe'])?'Homme':'Femme',
    'CONTACT4_CLAN'         =>  get_clan($user->profile_fields['pf_cd_clan'], $user->profile_fields['pf_cd_sexe']),
    'CONTACT4_RACE'         =>  get_race($user->profile_fields['pf_cd_race'], $user->profile_fields['pf_cd_sexe']),
    
    /*''  =>  $user->profile_fields['pf_'],
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
    ''  =>  $user->profile_fields['pf_'],*/
    
        
));
// Output page
page_header($user->lang['CREATION_ETAPE11']);

$template->set_filenames(array(
	'body' => 'creation/etape11.html'
));

page_footer();

?>
