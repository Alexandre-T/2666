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
$bbcode_status = $allow_bbcode = $allow_urls = $allow_url_bbcode = $allow_smilies = $allow_img_bbcode = $allow_quote_bbcode = true;
$allow_flash_bbcode = false; 
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
    $message_parser->parse($allow_bbcode, $allow_urls, $allow_smilies,  $allow_img_bbcode, $allow_flash_bbcode, $allow_quote_bbcode, $allow_url_bbcode);
    $resume_display = $message_parser->format_display($allow_bbcode, $allow_urls, $allow_smilies, false);
}else{    
    $resume_edit = $user->profile_fields['pf_resume'];
    $uid = $user->profile_fields['pf_resume_uid'];
    //$resume_edit = generate_text_for_display($resume_edit, $uid, $bitfield, 7);
}

$resume_edit = generate_text_for_edit($resume_edit , $uid, 7);

//Vérification des droits
creation_verification(CREATION_ETAPE);

//Template
$template->assign_vars(array(
    
    'S_MESSAGE'	  	 => 1 == $message,
    'S_APERCU'       => $apercu,
    'MESSAGE'        => $resume_edit['text'],
    'RESUME_DISPLAY' => $resume_display,
    
    'BBCODE_STATUS'			=> ($bbcode_status) ? sprintf($user->lang['BBCODE_IS_ON'], '<a href="' . append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=bbcode') . '">', '</a>') : sprintf($user->lang['BBCODE_IS_OFF'], '<a href="' . append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=bbcode') . '">', '</a>'),
    'IMG_STATUS'			=> ($allow_img_bbcode) ? $user->lang['IMAGES_ARE_ON'] : $user->lang['IMAGES_ARE_OFF'],
    'FLASH_STATUS'			=> ($allow_flash_bbcode) ? $user->lang['FLASH_IS_ON'] : $user->lang['FLASH_IS_OFF'],
    'SMILIES_STATUS'		=> ($allow_smilies) ? $user->lang['SMILIES_ARE_ON'] : $user->lang['SMILIES_ARE_OFF'],
    'URL_STATUS'			=> ($bbcode_status && $allow_url_bbcode) ? $user->lang['URL_IS_ON'] : $user->lang['URL_IS_OFF'],
    
    'S_BBCODE_IMG'			=> $allow_img_bbcode,
    'S_BBCODE_URL'			=> $allow_url_bbcode,
    'S_BBCODE_FLASH'		=> $allow_flash_bbcode,
    'S_BBCODE_QUOTE'		=> $allow_quote_bbcode,
    
));


// Output page
page_header($user->lang['CREATION_ETAPE5']);

$template->set_filenames(array(
	'body' => 'creation/etape5.html'
));

page_footer();

