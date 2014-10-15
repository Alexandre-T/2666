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
 *
 * @ignore
 *
 */
define('IN_PHPBB', true);
define('CREATION_ETAPE', 2);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../jeuderole/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include ($phpbb_root_path . 'common.' . $phpEx);
include ($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
include ($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include ($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include ($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include ($phpbb_root_path . 'includes/bbcode.' . $phpEx);
include ($phpbb_root_path . 'includes/mods/functions_user.' . $phpEx);
include ($phpbb_root_path . 'includes/mods/functions_creation.' . $phpEx);
include ($phpbb_root_path . 'includes/mods/functions_popup.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/creation');
$user->setup('posting');

// Chargement des champs de profil
$user->get_profile_fields($user->data['user_id']);

// Enregistrement ?
$submit = (isset($_POST['submit'])) ? true : false;
if ($submit) {
    // Analyse et traitement de la variable posté
    $race = request_var('race', 0);
    $race = ($race !== AT_HUMAIN) ? AT_NEPHILIM : AT_HUMAIN;
    $cp_data['pf_race'] = $race;
    if ($race != $user->profile_fields['pf_race']) {
        // Attention, changement de race
        // On ajoute ici les modifications qui pourraient survenir
        $cp_data['pf_agereel'] = 0;
        // On devrait supprimer les dons ou les pouvoirs
    }
    // Enregistrement
    $cp = new custom_profile();
    $cp->update_profile_field_data($user->data['user_id'], $cp_data);
    unset($user->profile_fields);
    header('Location: etape3.php');
    die();
}

// Vérification des droits
creation_verification(CREATION_ETAPE);

// Chargement des contenus des popups
$messages = get_texts_for_popup(array(
    POST_HUMAIN,
    POST_NEPHILIM
));

// Gestion du message d'erreur
$message = request_var('message', 0);

// Template
$template->assign_vars(array(
    
    'POST_HUMAIN' => $messages[POST_HUMAIN],
    'POST_NEPHILIM' => $messages[POST_NEPHILIM],
    
    'S_FEMME' => AT_FEMME == $user->profile_fields['pf_sexe'],
    'S_HOMME' => AT_HOMME == $user->profile_fields['pf_sexe'],
    'S_HUMAIN' => AT_HUMAIN == $user->profile_fields['pf_race'],
    'S_NEPHILIM' => AT_NEPHILIM == $user->profile_fields['pf_race'],
    'S_MESSAGE' => 1 == $message,
    
    'AT_HUMAIN' => AT_HUMAIN,
    'AT_NEPHILIM' => AT_NEPHILIM,
    
    'HUMAIN_CHECKED' => AT_HUMAIN == $user->profile_fields['pf_race'] ? 'checked' : '',
    'NEPHILIM_CHECKED' => AT_NEPHILIM == $user->profile_fields['pf_race'] ? 'checked' : '',
    
    'HIDDEN_FIELDS' => build_hidden_fields(array(
        'from' => CREATION_ETAPE
    ))
)
);

// Output page
page_header($user->lang['CREATION_ETAPE2']);

$template->set_filenames(array(
    'body' => 'creation/etape2.html'
));

page_footer();

?>
