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

$erreurTexte = '';

$user->get_profile_fields($user->data['user_id']);

//Enregistrement
$cp = new custom_profile();
$cp->update_profile_field_data($user->data['user_id'], $cp_data);
unset($user->profile_fields);

//Chargement des champs de profil
$user->get_profile_fields($user->data['user_id']);

//Enregistrement ?
$submit = (isset($_POST['submit'])) ? true : false;
$apercu = (isset($_POST['apercu'])) ? true : false;
if ($submit || $apercu){
    //Analyse et traitement de la variable posté
    $cp_data['pf_ca_nom']		  = utf8_normalize_nfc(request_var('nom', '',true));
    $cp_data['pf_ca_description'] = utf8_normalize_nfc(request_var('description', '',true));
    $cp_data['pf_ca_avatar_name'] = utf8_normalize_nfc(request_var('avatar', '',true));
    $resume               		  = utf8_normalize_nfc(request_var('resume', '',true));
    
    $poll = $uid = $bitfield = $options = '';
    $allow_bbcode = $allow_urls = $allow_smilies = true;
    generate_text_for_storage($resume, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
    $cp_data['pf_ca_resume'] = $resume;
    $cp_data['pf_ca_uid'] = $uid;
    $cp_data['pf_ca_bit'] = $bitfield;
    //Enregistrement
    $cp = new custom_profile();
    $cp->update_profile_field_data($user->data['user_id'], $cp_data);
    $publication = !(empty($_FILES['uploadfile']['name']) && empty ($_POST['uploadurl']));
    //Enregistrement dans un second temps de l'avatar fourni ;)
    if (!$publication && empty($user->profile_fields['pf_ca_avatar'])){
        $erreurTexte = 'La photo est obligatoire.';
    }elseif ($publication) {
        //Enregistrement de l'avatar !
        contact_process_user($error,false,true,1);
        if($error){
            $erreurTexte = implode ('<br/>',$error);
            unset($error);
        }
    }else{
        if ($submit){
            //Ok on passe à l'étape 7
            header('Location: etape7.php');
            die();
        } else{
            //Aperçu            
        }
    }
    unset($user->profile_fields);
}

//Chargement ou rechargement (en raison de l'aperçu) des champs de profil
$user->get_profile_fields($user->data['user_id']);

//Vérification des droits
creation_verification(CREATION_ETAPE);

///Generate popup
$messages = get_texts_for_popup(array(POST_CONSEILS_CONTACT));

//Initialisation des variables,
$a_resume = generate_text_for_edit($user->profile_fields['pf_ca_resume'],$user->profile_fields['pf_ca_uid'],7);

// Generate smiley listing
//generate_smilies('inline', 1);

//Gestion du message d'erreur
$message = request_var('message',0);

// Build custom bbcodes array
display_custom_bbcodes();

//Template
$template->assign_vars(array(
    
    'AVATAR_CONTACT'            => get_contact_avatar(1,$user->profile_fields['pf_ca_avatar'], $user->profile_fields['pf_ca_avatar_type'], $user->profile_fields['pf_ca_avatar_width'], $user->profile_fields['pf_ca_avatar_height']),
    'AVATAR_SIZE'	            => $config['avatar_filesize'],
    'FORM_NOM'	  			    => $user->profile_fields['pf_ca_nom'],
    'FORM_AVATAR'  			    => $user->profile_fields['pf_ca_avatar_name'],
    'MESSAGE'      	  			=> $a_resume['text'],
    'FORM_LIEN'		  			=> $user->profile_fields['pf_ca_lien'],
    'FORM_DESCRIPTION'          => $user->profile_fields['pf_ca_description'],
    
	'S_FEMME'	                => AT_FEMME    == $user->profile_fields['pf_sexe'],
	'S_HOMME'	                => AT_HOMME    == $user->profile_fields['pf_sexe'],
	'S_HUMAIN'	                => AT_HUMAIN   == $user->profile_fields['pf_race'],
	'S_NEPHILIM'                => AT_NEPHILIM == $user->profile_fields['pf_race'],
	'S_MESSAGE'	                => 1 == $message,
    'S_HELPBLOCK_MESSAGE'       => true,
    'S_ERREUR'                  => !empty($erreurTexte),
    'ERREUR'                    => $erreurTexte,
		
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
