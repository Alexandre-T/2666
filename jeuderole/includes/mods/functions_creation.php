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
 *
 * @ignore
 *
 */
if (! defined('IN_PHPBB')) {
    exit();
}

/**
 * Fonctions de 2666
 */

/**
 * Fonctions de ROME
 */

/**
 * Vérifie que l'utilisateur peut participer à la création de personnage
 * ou le redirige vers la première étape manquante
 */
function creation_verification($etape)
{
    creation_verification_etape((int) $etape);
}

/**
 * Vérifie que l'utilisateur peut participer à la création de personnage
 * ou le redirige vers une fonction
 */
function creation_verification_groupe()
{
    global $user, $phpEx;
    if ($user->data['user_id'] == ANONYMOUS) {
        login_box('', $user->lang['LOGIN_EXPLAIN_CREATION']);
        return false;
    }
    // Le personnage ne doit pas être actif
    if (is_user_in_group(GROUPE_ACTIF)) {
        // l'utilisateur est déjà actif, on le redirige vers sa fiche
        header("Location: ../fiche.$phpEx?message=1");
    }
    // L'utilisateur doit avoir signé le règlement sinon on ne le laisse pas commencer
    if (! is_user_in_group(GROUPE_SIGNATURE)) {
        // redirection vers le réglement avec message
        header("Location: ../signature.$phpEx?message=1");
    }
    if (is_user_in_group(GROUPE_DEMANDE_CREATION)) {
        // l'utilisateur est en attente, on le redirige vers la page d'attente
        header("Location: enattente.$phpEx?message=1");
    }
    return true;
}

/**
 * Vérifie que l'utilisateur peut participer à la création de personnage
 * ou le redirige vers une fonction
 */
function creation_verification_etape($etape)
{
    global $user, $template, $phpEx, $phpbb_root_path;
    $user->get_profile_fields($user->data['user_id']);
    $etapes[0] = ! creation_verification_groupe();
    $etapes[1] = $etapes[0];
    $etapes[2] = $etapes[1];
    $etapes[3] = empty($user->profile_fields['pf_sexe']);
    $etapes[4] = empty($user->profile_fields['pf_race']);
    $etapes[5] = empty($user->profile_fields['pf_avatar']);
    $etapes[6] = empty($user->profile_fields['pf_passe']) || empty($user->profile_fields['pf_agereel']) && AT_NEPHILIM == $user->profile_fields['pf_race'] || empty($user->profile_fields['pf_prenom']) || empty($user->profile_fields['pf_nom']);
    $etapes[7] = empty($user->profile_fields['pf_clan']) || empty($user->profile_fields['pf_pouvoir']) && AT_NEPHILIM == $user->profile_fields['pf_race'] || empty($user->profile_fields['pf_voleuse_nom']) && AT_NEPHILIM == $user->profile_fields['pf_race'] || empty($user->profile_fields['pf_voleuse_des']) && AT_NEPHILIM == $user->profile_fields['pf_race'] || empty($user->profile_fields['pf_voleuse_pouvoir']) && AT_NEPHILIM == $user->profile_fields['pf_race'] || empty($user->profile_fields['pf_don']) && AT_HUMAIN == $user->profile_fields['pf_race'] || $etapes[7] = empty($user->profile_fields['pf_nature']) || empty($user->profile_fields['pf_attitude']) || empty($user->profile_fields['pf_defaut']) || empty($user->profile_fields['pf_caractere']) || empty($user->profile_fields['pf_qualite']);
    $etapes[8] = empty($user->profile_fields['pf_pouvoir']) || AT_ANDROID == $user->profile_fields['pf_race'] && (empty($user->profile_fields['pf_plot']) || AT_NONRENSEIGNE == $user->profile_fields['pf_plot']) || AT_HUMAIN == $user->profile_fields['pf_race'] && (empty($user->profile_fields['pf_pouvoirdeux']) && AT_DIEU_MINEUR != $user->profile_fields['pf_dieu'] && AT_DIEU_ATHE != $user->profile_fields['pf_dieu']);
    $etapes[8] = empty($user->profile_fields['pf_anonymat']);
    
    // redirection éventuelle
    switch ($etape) {
        case 7:
        case 6:
            if ($etapes[6]) {
                $location = "Location: etape5.$phpEx?message=1";
            }
        case 5:
            if ($etapes[5]) {
                $location = "Location: etape4.$phpEx?message=1";
            }
        case 4:
            if ($etapes[4]) {
                $location = "Location: etape3.$phpEx?message=1";
            }
        case 3:
            if ($etapes[3]) {
                $location = "Location: etape2.$phpEx?message=1";
            }
        case 2:
            if ($etapes[2]) {
                $location = "Location: etape1.$phpEx?message=1";
            }
    }
    if (! empty($location)) {
        header($location);
    }
    // Assignation des variables
    $l_race = (key_exists('pf_race', $user->profile_fields)) ? $user->profile_fields['pf_race'] : AT_NONRENSEIGNE;
    $template->assign_vars(array(
        'ETAPE' => $etape,
        'S_ACTIF' => is_user_in_group(GROUPE_ACTIF),
        'S_BBCODE_ALLOWED' => true,
        'S_BBCODE_CHECKED' => true,
        'S_CREATION' => true,
        'POURCENTAGE' => $etape * 10,
        
        'U_ETAPE_0' => append_sid("{$phpbb_root_path}../creation/index.$phpEx"),
        'U_ETAPE_1' => append_sid("{$phpbb_root_path}../creation/etape1.$phpEx"),
        'U_ETAPE_2' => append_sid("{$phpbb_root_path}../creation/etape2.$phpEx"),
        'U_ETAPE_3' => append_sid("{$phpbb_root_path}../creation/etape3.$phpEx"),
        'U_ETAPE_4' => append_sid("{$phpbb_root_path}../creation/etape4.$phpEx"),
        'U_ETAPE_5' => append_sid("{$phpbb_root_path}../creation/etape5.$phpEx"),
        'U_ETAPE_6' => append_sid("{$phpbb_root_path}../creation/etape6.$phpEx"),
        'U_ETAPE_7' => append_sid("{$phpbb_root_path}../creation/etape7.$phpEx"),
        'S_ETAPE_0' => 0 == $etape,
        'S_ETAPE_1' => 1 == $etape,
        'S_ETAPE_2' => 2 == $etape,
        'S_ETAPE_3' => 3 == $etape,
        'S_ETAPE_4' => 4 == $etape,
        'S_ETAPE_5' => 5 == $etape,
        'S_ETAPE_6' => 6 == $etape,
        'S_ETAPE_7' => 7 == $etape,
        'S_ETAPE_8' => 8 == $etape,
        'S_ETAPE_9' => 9 == $etape,
        'S_ETAPE_10' => 10 == $etape,
        
        'S_HUMAIN' => ($l_race == AT_HUMAIN),
        'S_CREATION' => true,
        'S_CREE' => is_user_in_group(GROUPE_DEMANDE_CREATION),
        'S_INACTIF' => is_user_in_group(GROUPE_INACTIF),
        'S_NEPHILIM' => ($l_race == AT_NEPHILIM),
        'S_SIGNATURE' => is_user_in_group(GROUPE_SIGNATURE),
        'S_SMILIES_ALLOWED' => true,
        'S_SMILIES_CHECKED' => true,
        'VAR_DUMP' => var_export(array_merge($user->profile_fields, $etapes, $user->data), true),
        'U_ACTION' => creation_action($etape)
    ));
    return $etapes;
}

/**
 * Uploading/Changing contact avatar
 */
function contact_process_user(&$error, $custom_userdata = false, $can_upload = null, $number = 1)
{
    global $config, $phpbb_root_path, $auth, $user, $db;
    
    switch ($number) {
        case 4:
            $prefixe = 'cd';
            $a_type = 'pf_cd_avatar_type';
            $a_main = 'pf_cd_avatar';
            $a_height = 'pf_cd_avatar_height';
            $a_width = 'pf_cd_avatar_width';
            break;
        case 3:
            $prefixe = 'cc';
            $a_type = 'pf_cc_avatar_type';
            $a_main = 'pf_cc_avatar';
            $a_height = 'pf_cc_avatar_height';
            $a_width = 'pf_cc_avatar_width';
            break;
        case 2:
            $prefixe = 'cb';
            $a_type = 'pf_cb_avatar_type';
            $a_main = 'pf_cb_avatar';
            $a_height = 'pf_cb_avatar_height';
            $a_width = 'pf_cb_avatar_width';
            break;
        default:
            $prefixe = 'ca';
            $a_type = 'pf_ca_avatar_type';
            $a_main = 'pf_ca_avatar';
            $a_height = 'pf_ca_avatar_height';
            $a_width = 'pf_ca_avatar_width';
            break;
    }
    
    $data = array(
        'uploadurl' => request_var('uploadurl', ''),
        'remotelink' => request_var('remotelink', ''),
        'width' => request_var('width', 0),
        'height' => request_var('height', 0)
    );
    
    $error = validate_data($data, array(
        'uploadurl' => array(
            'string',
            true,
            5,
            255
        ),
        'remotelink' => array(
            'string',
            true,
            5,
            255
        ),
        'width' => array(
            'string',
            true,
            1,
            3
        ),
        'height' => array(
            'string',
            true,
            1,
            3
        )
    ));
    
    if (sizeof($error)) {
        return false;
    }
    
    $sql_ary = array();
    
    if ($custom_userdata === false) {
        $userdata = &$user->data;
    } else {
        $userdata = &$custom_userdata;
    }
    
    $data['user_id'] = $userdata['user_id'];
    // AT On autorise toujours le changement de contact.
    $change_avatar = true;
    $avatar_select = basename(request_var('avatar_select', ''));
    
    // Can we upload?
    if (is_null($can_upload)) {
        $can_upload = ($config['allow_avatar_upload'] && file_exists($phpbb_root_path . $config['avatar_path']) && phpbb_is_writable($phpbb_root_path . $config['avatar_path']) && $change_avatar && (@ini_get('file_uploads') || strtolower(@ini_get('file_uploads')) == 'on')) ? true : false;
    }
    
    if ((! empty($_FILES['uploadfile']['name']) || $data['uploadurl']) && $can_upload) {
        list ($sql_ary[$a_type], $sql_ary[$a_main], $sql_ary[$a_width], $sql_ary[$a_height]) = contact_upload($data, $prefixe, $error);
    } else 
        if ($data['remotelink'] && $change_avatar && $config['allow_avatar_remote']) {
            list ($sql_ary[$a_type], $sql_ary[$a_main], $sql_ary[$a_width], $sql_ary[$a_height]) = avatar_remote($data, $error);
        } else 
            if ($avatar_select && $change_avatar && $config['allow_avatar_local']) {
                $category = basename(request_var('category', ''));
                
                $sql_ary[$a_type] = AVATAR_GALLERY;
                $sql_ary[$a_main] = $avatar_select;
                
                // check avatar gallery
                if (! is_dir($phpbb_root_path . $config['avatar_gallery_path'] . '/' . $category)) {
                    $sql_ary[$a_main] = '';
                    $sql_ary[$a_type] = $sql_ary[$a_width] = $sql_ary[$a_height] = 0;
                } else {
                    list ($sql_ary[$a_width], $sql_ary[$a_height]) = getimagesize($phpbb_root_path . $config['avatar_gallery_path'] . '/' . $category . '/' . urldecode($sql_ary[$a_main]));
                    $sql_ary[$a_main] = $category . '/' . $sql_ary[$a_main];
                }
            } else 
                if (isset($_POST['delete']) && $change_avatar) {
                    $sql_ary[$a_main] = '';
                    $sql_ary[$a_type] = $sql_ary[$a_width] = $sql_ary[$a_height] = 0;
                } else 
                    if (! empty($userdata[$a_main])) {
                        // Only update the dimensions
                        
                        if (empty($data['width']) || empty($data['height'])) {
                            if ($dims = avatar_get_dimensions($userdata[$a_main], $userdata[$a_type], $error, $data['width'], $data['height'])) {
                                list ($guessed_x, $guessed_y) = $dims;
                                if (empty($data['width'])) {
                                    $data['width'] = $guessed_x;
                                }
                                if (empty($data['height'])) {
                                    $data['height'] = $guessed_y;
                                }
                            }
                        }
                        if (($config['avatar_max_width'] || $config['avatar_max_height']) && (($data['width'] != $userdata['user_avatar_width']) || $data['height'] != $userdata['user_avatar_height'])) {
                            if ($data['width'] > $config['avatar_max_width'] || $data['height'] > $config['avatar_max_height']) {
                                $error[] = sprintf($user->lang['AVATAR_WRONG_SIZE'], $config['avatar_min_width'], $config['avatar_min_height'], $config['avatar_max_width'], $config['avatar_max_height'], $data['width'], $data['height']);
                            }
                        }
                        
                        if (! sizeof($error)) {
                            if ($config['avatar_min_width'] || $config['avatar_min_height']) {
                                if ($data['width'] < $config['avatar_min_width'] || $data['height'] < $config['avatar_min_height']) {
                                    $error[] = sprintf($user->lang['AVATAR_WRONG_SIZE'], $config['avatar_min_width'], $config['avatar_min_height'], $config['avatar_max_width'], $config['avatar_max_height'], $data['width'], $data['height']);
                                }
                            }
                        }
                        
                        if (! sizeof($error)) {
                            $sql_ary[$a_width] = $data['width'];
                            $sql_ary[$a_height] = $data['height'];
                        }
                    }
    
    if (! sizeof($error)) {
        // Do we actually have any data to update?
        if (sizeof($sql_ary)) {
            $ext_new = $ext_old = '';
            if (isset($sql_ary[$a_main])) {
                $ext_new = (empty($sql_ary[$a_main])) ? '' : substr(strrchr($sql_ary[$a_main], '.'), 1);
                $ext_old = (empty($user->profile_fields[$a_main])) ? '' : substr(strrchr($user->profile_fields[$a_main], '.'), 1);
                
                if ($user->profile_fields[$a_type] == AVATAR_UPLOAD) {
                    // Delete old avatar if present
                    if ((! empty($user->profile_fields[$a_main]) && empty($sql_ary[$a_main])) || (! empty($user->profile_fields[$a_main]) && ! empty($sql_ary[$a_main]) && $ext_new !== $ext_old)) {
                        contact_delete($prefixe, $user->profile_fields);
                    }
                }
            }
            
            $sql = 'UPDATE ' . PROFILE_FIELDS_DATA_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
				WHERE user_id = ' . (($custom_userdata === false) ? $user->data['user_id'] : $custom_userdata['user_id']);
            $db->sql_query($sql);
        }
    }
    
    return (sizeof($error)) ? false : true;
}

/**
 * Avatar upload using the upload class
 */
function contact_upload($data, $prefixe, &$error)
{
    global $phpbb_root_path, $config, $db, $user, $phpEx;
    
    // Init upload class
    include_once ($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
    $upload = new fileupload('AVATAR_', array(
        'jpg',
        'jpeg',
        'gif',
        'png'
    ), $config['avatar_filesize'], $config['avatar_min_width'], $config['avatar_min_height'], $config['avatar_max_width'], $config['avatar_max_height'], (isset($config['mime_triggers']) ? explode('|', $config['mime_triggers']) : false));
    
    if (! empty($_FILES['uploadfile']['name'])) {
        $file = $upload->form_upload('uploadfile');
    } else {
        $file = $upload->remote_upload($data['uploadurl']);
    }
    
    $prefix = $config['avatar_salt'] . '_' . $prefixe . '_';
    $file->clean_filename('avatar', $prefix, $data['user_id']);
    
    $destination = $config['avatar_path'];
    
    // Adjust destination path (no trailing slash)
    if (substr($destination, - 1, 1) == '/' || substr($destination, - 1, 1) == '\\') {
        $destination = substr($destination, 0, - 1);
    }
    
    $destination = str_replace(array(
        '../',
        '..\\',
        './',
        '.\\'
    ), '', $destination);
    if ($destination && ($destination[0] == '/' || $destination[0] == "\\")) {
        $destination = '';
    }
    
    // Move file and overwrite any existing image
    $file->move_file($destination, true);
    
    if (sizeof($file->error)) {
        $file->remove();
        $error = array_merge($error, $file->error);
    }
    
    return array(
        AVATAR_UPLOAD,
        $data['user_id'] . '_' . time() . '.' . $file->get('extension'),
        $file->get('width'),
        $file->get('height')
    );
}

/**
 * Remove avatar
 */
function contact_delete($mode, $row, $clean_db = false)
{
    global $phpbb_root_path, $config, $db, $user;
    
    // Check if the users avatar is actually *not* a group avatar
    /*
     * if ($mode == 'user')
     * {
     * if (strpos($row['user_avatar'], 'g') === 0 || (((int)$row['user_avatar'] !== 0) && ((int)$row['user_avatar'] !== (int)$row['user_id'])))
     * {
     * return false;
     * }
     * }
     *
     * if ($clean_db)
     * {
     * avatar_remove_db($row[$mode . '_avatar']);
     * }
     */
    
    $filename = get_contact_filename($mode, $row['pf_' . $mode . '_avatar']);
    if (file_exists($phpbb_root_path . $config['avatar_path'] . '/' . $filename)) {
        @unlink($phpbb_root_path . $config['avatar_path'] . '/' . $filename);
        return true;
    }
    
    return false;
}

/**
 * Generates contact filename from the database entry
 */
function get_contact_filename($mode, $avatar_entry)
{
    global $config;
    
    $ext = substr(strrchr($avatar_entry, '.'), 1);
    $avatar_entry = intval($avatar_entry);
    return $config['avatar_salt'] . '_' . $mode . '_' . $avatar_entry . '.' . $ext;
}

function creation_action($etape)
{
    global $template, $phpbb_root_path, $phpEx;
    /* Mise en place de la barre de progression */
    $template->assign_block_vars('navlinks', array(
        'S_IS_CAT' => false,
        'S_IS_LINK' => true,
        'S_IS_POST' => false,
        'FORUM_NAME' => 'Création de personnage',
        'FORUM_ID' => 0,
        'U_VIEW_FORUM' => append_sid("{$phpbb_root_path}../creation/index.$phpEx")
    ));
    if (0 == $etape) {
        $template->assign_block_vars('navlinks', array(
            'S_IS_CAT' => false,
            'S_IS_LINK' => true,
            'S_IS_POST' => false,
            'FORUM_NAME' => 'Introduction',
            'FORUM_ID' => 0,
            'U_VIEW_FORUM' => append_sid("{$phpbb_root_path}../creation/index.$phpEx")
        ));
        return append_sid("{$phpbb_root_path}../creation/index.$phpEx");
    } else {
        $template->assign_block_vars('navlinks', array(
            'S_IS_CAT' => false,
            'S_IS_LINK' => true,
            'S_IS_POST' => false,
            'FORUM_NAME' => "Étape $etape sur 10",
            'FORUM_ID' => 0,
            'U_VIEW_FORUM' => append_sid("{$phpbb_root_path}../creation/etape$etape.$phpEx")
        ));
        return append_sid("{$phpbb_root_path}../creation/etape$etape.$phpEx");
    }
}

/**
 * Gestion du contact
 *
 * @param integer $numero            
 */
function gestionContact($numero)
{
    global $user, $template, $config,$phpEx;
    
    switch ($numero) {
        case 4:
            $prefixe = 'cd';
            $etape = 'etape10.'.$phpEx;
            break;
        case 3:
            $prefixe = 'cc';
            $etape = 'etape9.'.$phpEx;
            break;
        case 2:
            $prefixe = 'cb';
            $etape = 'etape8.'.$phpEx;
            break;
        default:
            $prefixe = 'ca';
            $etape = 'etape7.'.$phpEx;
            break;
    }
    
    $erreurTexte = '';
    
    $user->get_profile_fields($user->data['user_id']);
    
    // Enregistrement
    $cp = new custom_profile();
    $cp->update_profile_field_data($user->data['user_id'], $cp_data);
    unset($user->profile_fields);
    
    // Chargement des champs de profil
    $user->get_profile_fields($user->data['user_id']);
    
    // Enregistrement ?
    $submit = (isset($_POST['submit'])) ? true : false;
    $apercu = (isset($_POST['apercu'])) ? true : false;
    if ($submit || $apercu) {
        // Analyse et traitement de la variable posté
        $cp_data['pf_'.$prefixe.'_nom'] = utf8_normalize_nfc(request_var('nom', '', true));
        $cp_data['pf_'.$prefixe.'_description'] = utf8_normalize_nfc(request_var('description', '', true));
        $cp_data['pf_'.$prefixe.'_avatar_name'] = utf8_normalize_nfc(request_var('avatar', '', true));
        $resume = utf8_normalize_nfc(request_var('resume', '', true));
        
        $poll = $uid = $bitfield = $options = '';
        $allow_bbcode = $allow_urls = $allow_smilies = true;
        generate_text_for_storage($resume, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
        $cp_data['pf_'.$prefixe.'_resume'] = $resume;
        $cp_data['pf_'.$prefixe.'_uid'] = $uid;
        $cp_data['pf_'.$prefixe.'_bit'] = $bitfield;
        // Enregistrement
        $cp = new custom_profile();
        $cp->update_profile_field_data($user->data['user_id'], $cp_data);
        $publication = ! (empty($_FILES['uploadfile']['name']) && empty($_POST['uploadurl']));
        // Enregistrement dans un second temps de l'avatar fourni ;)
        if (! $publication && empty($user->profile_fields['pf_'.$prefixe.'_avatar'])) {
            $erreurTexte = 'La photo est obligatoire.';
        } elseif ($publication) {
            // Enregistrement de l'avatar !
            contact_process_user($error, false, true, 1);
            if ($error) {
                $erreurTexte = implode('<br/>', $error);
                unset($error);
            }
        } else {
            if ($submit) {
                // Ok on passe à l'étape 7
                header('Location: '.$etape);
                die();
            } else {
                // Aperçu
            }
        }
        unset($user->profile_fields);
    }
    
    // Chargement ou rechargement (en raison de l'aperçu) des champs de profil
    $user->get_profile_fields($user->data['user_id']);
    
    // Vérification des droits
    creation_verification(CREATION_ETAPE);
    
    // /Generate popup
    $messages = get_texts_for_popup(array(
        POST_CONSEILS_CONTACT
    ));
    
    // Initialisation des variables,
    $a_resume = generate_text_for_edit($user->profile_fields['pf_'.$prefixe.'_resume'], $user->profile_fields['pf_'.$prefixe.'_uid'], 7);
    
    // Generate smiley listing
    // generate_smilies('inline', 1);
    
    // Gestion du message d'erreur
    $message = request_var('message', 0);
    
    // Build custom bbcodes array
    display_custom_bbcodes();
    
    // Template
    $template->assign_vars(array(
        
        'AVATAR_CONTACT' => get_contact_avatar(1, $user->profile_fields['pf_'.$prefixe.'_avatar'], $user->profile_fields['pf_'.$prefixe.'_avatar_type'], $user->profile_fields['pf_'.$prefixe.'_avatar_width'], $user->profile_fields['pf_'.$prefixe.'_avatar_height']),
        'AVATAR_SIZE' => $config['avatar_filesize'],
        'FORM_NOM' => $user->profile_fields['pf_'.$prefixe.'_nom'],
        'FORM_AVATAR' => $user->profile_fields['pf_'.$prefixe.'_avatar_name'],
        'MESSAGE' => $a_resume['text'],
        'FORM_LIEN' => $user->profile_fields['pf_'.$prefixe.'_lien'],
        'FORM_DESCRIPTION' => $user->profile_fields['pf_'.$prefixe.'_description'],
        
        'S_FEMME' => AT_FEMME == $user->profile_fields['pf_sexe'],
        'S_HOMME' => AT_HOMME == $user->profile_fields['pf_sexe'],
        'S_HUMAIN' => AT_HUMAIN == $user->profile_fields['pf_race'],
        'S_NEPHILIM' => AT_NEPHILIM == $user->profile_fields['pf_race'],
        'S_MESSAGE' => 1 == $message,
        'S_HELPBLOCK_MESSAGE' => true,
        'S_ERREUR' => ! empty($erreurTexte),
        'ERREUR' => $erreurTexte,
        
        'POST_CONSEILS_CONTACT' => $messages[POST_CONSEILS_CONTACT],
        
        'HIDDEN_FIELDS' => build_hidden_fields(array(
            'from' => CREATION_ETAPE
        ))
    )
    );
}

/**
 *
 * @param unknown $dieu            
 * @param string $texte            
 */
function creation_pouvoirs($dieu, $texte = null)
{
    global $template, $phpbb_root_path;
    switch ($dieu) {
        case AT_DIEU_JUPITER:
            $fichier = 'jupiter.csv';
            break;
        case AT_DIEU_MINERVE:
            $fichier = 'minerve.csv';
            break;
        case AT_DIEU_PLUTON:
            $fichier = 'pluton.csv';
            break;
        case AT_DIEU_VENUS:
            $fichier = 'venus.csv';
            break;
        case AT_DIEU_NEPTUNE:
            $fichier = 'neptune.csv';
            break;
        case AT_DIEU_VESTA:
            $fichier = 'vesta.csv';
            break;
        default:
            return;
    }
    $contenu = file_get_contents(append_sid("{$phpbb_root_path}../creation/ressources/$fichier"));
    $index = 1;
    foreach (explode("\n", $contenu) as $ligne) {
        $tableau = array();
        if (empty($ligne) || substr(trim($ligne[0]), 0, 1) == '#')
            continue;
        $pouvoir = explode(';', $ligne);
        if (count($pouvoir) != 2)
            continue;
        $template->assign_block_vars('pouvoirs', array(
            'ID' => ++ $index,
            'TITRE' => $pouvoir[0],
            'DESCRIPTION' => $pouvoir[1],
            'S_SELECTED' => $texte == "[b]{$pouvoir[0]}[/b] : [i]{$pouvoir[1]}[/i]"
        ));
    }
}

function creation_pouvoir($idPouvoir, $dieu)
{
    global $phpbb_root_path;
    switch ($dieu) {
        case AT_DIEU_JUPITER:
            $fichier = 'jupiter.csv';
            break;
        case AT_DIEU_MINERVE:
            $fichier = 'minerve.csv';
            break;
        case AT_DIEU_PLUTON:
            $fichier = 'pluton.csv';
            break;
        case AT_DIEU_VENUS:
            $fichier = 'venus.csv';
            break;
        case AT_DIEU_NEPTUNE:
            $fichier = 'neptune.csv';
            break;
        case AT_DIEU_VESTA:
            $fichier = 'vesta.csv';
            break;
        default:
            return;
    }
    $contenu = file_get_contents(append_sid("{$phpbb_root_path}../creation/ressources/$fichier"));
    $index = 1;
    foreach (explode("\n", $contenu) as $ligne) {
        $tableau = array();
        if (empty($ligne) || substr(trim($ligne[0]), 0, 1) == '#')
            continue;
        $pouvoir = explode(';', $ligne);
        if (count($pouvoir) != 2)
            continue;
        if ($idPouvoir != ++ $index)
            continue;
        return "[b]{$pouvoir[0]}[/b] : [i]{$pouvoir[1]}[/i]";
    }
}
?>
