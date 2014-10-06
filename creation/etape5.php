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
define('CREATION_ETAPE', 5);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../jeuderole/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
include($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_creation.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/creation');
$user->setup('posting');

//Chargement des champs de profil
$user->get_profile_fields($user->data['user_id']);

//Initialisation de variables
$poll = $uid = $bitfield = $options = '';
$allow_bbcode = $allow_urls = $allow_smilies = true;
$resume_storage = $resume_edit = $resume_display = '';

// Build custom bbcodes array
display_custom_bbcodes();

//Enregistrement ?
$submit = (isset($_POST['submit'])) ? true : false;
//Aperçu ?
$apercu = (isset($_POST['apercu'])) ? true : false;

if ($submit || $apercu){
    //récupération des variables postées
    $resume_storage = $resume_edit = $resume_display = utf8_normalize_nfc(request_var('resume', '', true));
}

//On enregistre
if ($submit){
    generate_text_for_storage($resume_storage, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
    //Analyse et traitement de la variable posté
    $cp_data['pf_resume']     = $resume_storage;
    $cp_data['pf_resume_uid'] = $uid;
    $cp_data['pf_resume_bit'] = $bitfield;
    
    //Enregistrement
    $cp = new custom_profile();
    $cp->update_profile_field_data($user->data['user_id'], $cp_data);
    unset($user->profile_fields);
    header('Location: etape6.php');
    die();
}
//On regarde
if ($apercu){
    $message_parser = new parse_message($resume_display);
    $message_parser->parse(true, true, true, false);
    $resume_display = $message_parser->format_display(true, true, true, false);
}else{    
    $resume_edit = $user->profile_fields['pf_resume'];
    $uid = $user->profile_fields['pf_resume_uid'];
    //$resume_edit = generate_text_for_display($resume_edit, $uid, $bitfield, 7);
}

$resume_edit = generate_text_for_edit($resume_edit , $uid, 7);

//Vérification des droits
creation_verification(CREATION_ETAPE);

//Gestion du message d'erreur
$message = request_var('message',0);

//Template
$template->assign_vars(array(
    
    'S_MESSAGE'	  	 => 1 == $message,
    'S_APERCU'       => $apercu,
    'MESSAGE'        => $resume_edit['text'],
    'RESUME_DISPLAY' => $resume_display,
    
));


// Output page
page_header($user->lang['CREATION_ETAPE5']);

$template->set_filenames(array(
	'body' => 'creation/etape5.html'
));

page_footer();

