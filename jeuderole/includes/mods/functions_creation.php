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
        trigger_error('Votre personnage est déjà actif, vous ne pouvez pas accéder aux fonctions de créations de personnage. Veuillez contacter Harahel par MP.');
    }
    // L'utilisateur doit avoir signé le règlement sinon on ne le laisse pas commencer
    if (! is_user_in_group(GROUPE_SIGNATURE)) {
        // redirection vers le réglement avec message
        header("Location: ../signature.$phpEx?message=1");
    }
    if (is_user_in_group(GROUPE_DEMANDE_CREATION)) {
        // l'utilisateur est en attente, on le redirige vers la page d'attente
        trigger_error('Votre demande de validation a bien été enregistrée. Veuillez patienter ou contacter Harahel par MP.');
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
    $etapes = array();
    $user->get_profile_fields($user->data['user_id']);
    $etapes[0] = creation_verification_groupe();
    $etapes[1] = $etapes[0];
    $etapes[2] = $etapes[1] && ! empty($user->profile_fields['pf_sexe']) && AT_NONRENSEIGNE !== $user->profile_fields['pf_sexe'];
    $etapes[3] = $etapes[2] && ! empty($user->profile_fields['pf_race']) && AT_NONRENSEIGNE !== $user->profile_fields['pf_race'];
    $etapes[4] = $etapes[3] && ! empty($user->profile_fields['pf_avatar']);
    $etapes[5] = $etapes[4] && ! (empty($user->profile_fields['pf_passe']) || empty($user->profile_fields['pf_agereel']) && AT_NEPHILIM == $user->profile_fields['pf_race'] || empty($user->profile_fields['pf_prenom']) || empty($user->profile_fields['pf_nom']) || empty($user->profile_fields['pf_profession']) );
    $etapes[6] = $etapes[5] && ! empty($user->profile_fields['pf_resume']) ;
    $etapes[7] = $etapes[6] && ! (empty($user->profile_fields['pf_clan']) || empty($user->profile_fields['pf_don']) && AT_HUMAIN == $user->profile_fields['pf_race'] || (empty($user->profile_fields['pf_pouvoir']) || empty($user->profile_fields['pf_voleuse_nom']) || empty($user->profile_fields['pf_voleuse_des']) || empty($user->profile_fields['pf_voleuse_pouvoir']) ) && AT_NEPHILIM == $user->profile_fields['pf_race'] );
    $etapes[8] = $etapes[7] && ! (empty($user->profile_fields['pf_ca_nom']) || empty($user->profile_fields['pf_ca_avatar']) || empty($user->profile_fields['pf_ca_avatar_name']) || empty($user->profile_fields['pf_ca_resume'])); 
    $etapes[9] = $etapes[8] && ! (empty($user->profile_fields['pf_cb_nom']) || empty($user->profile_fields['pf_cb_avatar']) || empty($user->profile_fields['pf_cb_avatar_name']) || empty($user->profile_fields['pf_cb_resume']));
    $etapes[10] = $etapes[9] && ('0' === $user->profile_fields['pf_cc_actif'] || ! (empty($user->profile_fields['pf_cc_nom']) || empty($user->profile_fields['pf_cc_avatar']) || empty($user->profile_fields['pf_cc_avatar_name']) || empty($user->profile_fields['pf_cc_resume'])));
    $etapes[11] = $etapes[10] && ('0' === $user->profile_fields['pf_cd_actif'] || ! (empty($user->profile_fields['pf_cd_nom']) || empty($user->profile_fields['pf_cd_avatar']) || empty($user->profile_fields['pf_cd_avatar_name']) || empty($user->profile_fields['pf_cd_resume'])));
    
    // redirection éventuelle
    $index = $etape;
    
    while (1 < $index ){
        if (! $etapes[$index]) {
            $mineur = $index - 1;
            $location = "Location: etape$mineur.$phpEx?message=1";
        }
        $index--;
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
        'POURCENTAGE' => ceil($etape * 100 / 11),
        
        'U_ETAPE_0' => append_sid("{$phpbb_root_path}../creation/index.$phpEx"),
        'U_ETAPE_1' => append_sid("{$phpbb_root_path}../creation/etape1.$phpEx"),
        'U_ETAPE_2' => append_sid("{$phpbb_root_path}../creation/etape2.$phpEx"),
        'U_ETAPE_3' => append_sid("{$phpbb_root_path}../creation/etape3.$phpEx"),
        'U_ETAPE_4' => append_sid("{$phpbb_root_path}../creation/etape4.$phpEx"),
        'U_ETAPE_5' => append_sid("{$phpbb_root_path}../creation/etape5.$phpEx"),
        'U_ETAPE_6' => append_sid("{$phpbb_root_path}../creation/etape6.$phpEx"),
        'U_ETAPE_7' => append_sid("{$phpbb_root_path}../creation/etape7.$phpEx"),
        'U_ETAPE_8' => append_sid("{$phpbb_root_path}../creation/etape8.$phpEx"),
        'U_ETAPE_9' => append_sid("{$phpbb_root_path}../creation/etape9.$phpEx"),
        'U_ETAPE_10' => append_sid("{$phpbb_root_path}../creation/etape10.$phpEx"),
        'U_ETAPE_11' => append_sid("{$phpbb_root_path}../creation/etape11.$phpEx"),
        
        'S_ETAPE_0_VALIDE'  => 0 == $etapes[0],
        'S_ETAPE_1_VALIDE'  => 1 == $etapes[1],
        'S_ETAPE_2_VALIDE'  => 2 == $etapes[2],
        'S_ETAPE_3_VALIDE'  => 3 == $etapes[3],
        'S_ETAPE_4_VALIDE'  => 4 == $etapes[4],
        'S_ETAPE_5_VALIDE'  => 5 == $etapes[5],
        'S_ETAPE_6_VALIDE'  => 6 == $etapes[6],
        'S_ETAPE_7_VALIDE'  => 7 == $etapes[7],
        'S_ETAPE_8_VALIDE'  => 8 == $etapes[8],
        'S_ETAPE_9_VALIDE'  => 9 == $etapes[9],
        'S_ETAPE_10_VALIDE' => 10 == $etapes[10],
        'S_ETAPE_11_VALIDE' => 11 == $etapes[11],
        
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
        'S_ETAPE_11' => 11 == $etape,
        
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
            'FORUM_NAME' => "Étape $etape sur 11",
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
    global $user, $template, $config, $phpbb_root_path, $phpEx;
    
    //Initialisation
    $poll = $uid = $bitfield = $options = '';
    $bbcode_status = $allow_bbcode = $allow_urls = $allow_url_bbcode = $allow_smilies = $allow_img_bbcode = $allow_quote_bbcode = true;
    $resume_display = $allow_flash_bbcode = false;
    $resume_display = $resume_edit = $resume_storage = utf8_normalize_nfc(request_var('resume', '', true));
    
    switch ($numero) {
        case 4:
            $prefixe = 'cd';
            $etape = 'etape11.'.$phpEx;
            break;
        case 3:
            $prefixe = 'cc';
            $etape = 'etape10.'.$phpEx;
            break;
        case 2:
            $prefixe = 'cb';
            $etape = 'etape9.'.$phpEx;
            break;
        default:
            $prefixe = 'ca';
            $etape = 'etape8.'.$phpEx;
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
        $checkbox = request_var('checkbox', 0);
        //Ce test empêche l'écrasement de donnée vide quand la checkbox n'est pas cochée
        if ($checkbox){
            
            // Analyse et traitement de la variable posté
            $race = request_var('race', AT_HUMAIN);
            $race = ($race !== AT_NEPHILIM)?AT_HUMAIN:AT_NEPHILIM;
            
            $sexe = request_var('sexe', AT_HOMME);
            $sexe = ($sexe !== AT_HOMME)?AT_FEMME:AT_HOMME;
            
            $clan = request_var('clan', AT_SANSCLAN);
            $acceptable = array(AT_ASMODEEN,AT_INFILTRE,AT_INSOUMIS,AT_IZANAGHI,AT_SKJALDMEYJAR,AT_VESTAL);
            if (!in_array($clan, $acceptable)){
                $clan = AT_SANSCLAN;
            }
            if (AT_SKJALDMEYJAR == $clan && AT_HOMME == $sexe){
                $clan = AT_SANSCLAN;
            }
            
            $cp_data['pf_'.$prefixe.'_race'] = $race;
            $cp_data['pf_'.$prefixe.'_sexe'] = $sexe;
            $cp_data['pf_'.$prefixe.'_clan'] = $clan;
            $cp_data['pf_'.$prefixe.'_nom'] = utf8_normalize_nfc(request_var('nom', '', true));
            $cp_data['pf_'.$prefixe.'_description'] = utf8_normalize_nfc(request_var('description', '', true));
            $cp_data['pf_'.$prefixe.'_avatar_name'] = utf8_normalize_nfc(request_var('avatar', '', true));
            generate_text_for_storage($resume_storage, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
            $cp_data['pf_'.$prefixe.'_resume'] = $resume_storage;
            $cp_data['pf_'.$prefixe.'_uid'] = $uid;
            $cp_data['pf_'.$prefixe.'_bit'] = $bitfield;
        }
        
        //Cas particulier des contacts 3 et 4
        if ($numero > 2){
            
            //Chargement des champs de profil
            $user->get_profile_fields($user->data['user_id']);
            if (0 == $checkbox){
                //Modification de champs
                if (3 == $numero ){
                    $cp_data['pf_cc_actif']=false;
                }
                $cp_data['pf_cd_actif']=false;
            }else{
                //Modification de champs
                if (3 == $numero ){
                    $cp_data['pf_cc_actif']=true;
                }
                $cp_data['pf_cd_actif']=true;
            }
        }        
        // Enregistrement        
        $cp = new custom_profile();
        $cp->update_profile_field_data($user->data['user_id'], $cp_data);
        $publication = ! (empty($_FILES['uploadfile']['name']) && empty($_POST['uploadurl']));
        // Enregistrement dans un second temps de l'avatar fourni ;)
        if (! $publication && empty($user->profile_fields['pf_'.$prefixe.'_avatar']) && $checkbox) {
            $erreurTexte = 'La photo est obligatoire.';
        } elseif ($publication) {
            // Enregistrement de l'avatar !
            contact_process_user($error, false, true, $numero);
            if ($error) {
                $erreurTexte = implode('<br/>', $error);
                unset($error);
            }
        } else {
            if ($submit) {
                // Ok on passe à l'étape suivante 
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
    
    if ($apercu){
        //$resume_display = generate_text_for_display($user->profile_fields['pf_'.$prefixe.'_resume'], $user->profile_fields['pf_'.$prefixe.'_uid'], $user->profile_fields['pf_'.$prefixe.'_bit'], 7);
        $message_parser = new parse_message($resume_display);
        $message_parser->parse($allow_bbcode, $allow_urls, $allow_smilies,  $allow_img_bbcode, $allow_flash_bbcode, $allow_quote_bbcode, $allow_url_bbcode);
        $resume_display = $message_parser->format_display($allow_bbcode, $allow_urls, $allow_smilies, false);
    }
    
    // Vérification des droits
    creation_verification(CREATION_ETAPE);
    
    // /Generate popup
    $messages = get_texts_for_popup(array(
        POST_CONSEILS_CONTACT
    ));
    
    // Initialisation des variables,
    $resume_edit = generate_text_for_edit($user->profile_fields['pf_'.$prefixe.'_resume'],$user->profile_fields['pf_'.$prefixe.'_uid'], 7);
    
    // Generate smiley listing
    // generate_smilies('inline', 1);
    
    // Gestion du message d'erreur
    $message = request_var('message', 0);
    
    // Build custom bbcodes array
    display_custom_bbcodes();

    //Calcul de la case à cocher
    $checked = '';
    if ($numero > 2 && $user->profile_fields['pf_'.$prefixe.'_actif']){
        $checked = 'checked="checked"';
    }
    // Template
    $template->assign_vars(array(
        
        'AVATAR_CONTACT' => get_contact_avatar($numero, $user->profile_fields['pf_'.$prefixe.'_avatar'], $user->profile_fields['pf_'.$prefixe.'_avatar_type'], $user->profile_fields['pf_'.$prefixe.'_avatar_width'], $user->profile_fields['pf_'.$prefixe.'_avatar_height']),
        'AVATAR_SIZE' => $config['avatar_filesize'],
        'FORM_NOM' => $user->profile_fields['pf_'.$prefixe.'_nom'],
        'FORM_AVATAR' => $user->profile_fields['pf_'.$prefixe.'_avatar_name'],
        'MESSAGE' => $resume_edit['text'],
        'FORM_LIEN' => $user->profile_fields['pf_'.$prefixe.'_lien'],
        'FORM_DESCRIPTION' => $user->profile_fields['pf_'.$prefixe.'_description'],
        'CHECKBOX_CHECKED' =>  $checked,
        'REQUIRED' => $checked?'required="required"':'',
        'CLASS_HIDDEN' => $checked?'':'hidden',
        
        'S_FEMME' => AT_FEMME == $user->profile_fields['pf_sexe'],
        'S_HOMME' => AT_HOMME == $user->profile_fields['pf_sexe'],
        'S_HUMAIN' => AT_HUMAIN == $user->profile_fields['pf_race'],
        'S_NEPHILIM' => AT_NEPHILIM == $user->profile_fields['pf_race'],
        
        'HUMAIN_CHECKED'   => (AT_NEPHILIM != $user->profile_fields['pf_'.$prefixe.'_race'])?'checked="checked"':'',
        'NEPHILIM_CHECKED' => (AT_NEPHILIM == $user->profile_fields['pf_'.$prefixe.'_race'])?'checked="checked"':'',
        
        'HOMME_CHECKED'   => (AT_HOMME == $user->profile_fields['pf_'.$prefixe.'_sexe'])?'checked="checked"':'',
        'FEMME_CHECKED'   => (AT_FEMME == $user->profile_fields['pf_'.$prefixe.'_sexe'])?'checked="checked"':'',
        
        'SELECTED_ASMODEEN'          => $user->profile_fields['pf_'.$prefixe.'_clan'] == AT_ASMODEEN?'selected':'',
        'SELECTED_INSOUMIS'          => $user->profile_fields['pf_'.$prefixe.'_clan'] == AT_INSOUMIS?'selected':'',
        'SELECTED_INFILTRE'          => $user->profile_fields['pf_'.$prefixe.'_clan'] == AT_INFILTRE?'selected':'',
        'SELECTED_IZANAGHI'          => $user->profile_fields['pf_'.$prefixe.'_clan'] == AT_IZANAGHI?'selected':'',
        'SELECTED_VESTAL'            => $user->profile_fields['pf_'.$prefixe.'_clan'] == AT_VESTAL?'selected':'',
        'SELECTED_SANSCLAN'          => $user->profile_fields['pf_'.$prefixe.'_clan'] == AT_SANSCLAN?'selected':'',
        'SELECTED_SKJALDMEYJAR'      => $user->profile_fields['pf_'.$prefixe.'_clan'] == AT_SKJALDMEYJAR?'selected':'',
        
        'AT_ASMODEEN'               => AT_ASMODEEN,
        'AT_INSOUMIS'               => AT_INSOUMIS,
        'AT_INFILTRE'               => AT_INFILTRE,
        'AT_IZANAGHI'               => AT_IZANAGHI,
        'AT_VESTAL'                 => AT_VESTAL,
        'AT_SANSCLAN'               => AT_SANSCLAN,
        'AT_SKJALDMEYJAR'           => AT_SKJALDMEYJAR,
        
        'S_MESSAGE' => 1 == $message,
        'S_HELPBLOCK_MESSAGE' => true,
        'S_ERREUR' => ! empty($erreurTexte),
        'ERREUR' => $erreurTexte,
        
        'BBCODE_STATUS'			=> ($bbcode_status) ? sprintf($user->lang['BBCODE_IS_ON'], '<a href="' . append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=bbcode') . '">', '</a>') : sprintf($user->lang['BBCODE_IS_OFF'], '<a href="' . append_sid("{$phpbb_root_path}faq.$phpEx", 'mode=bbcode') . '">', '</a>'),
        'IMG_STATUS'			=> ($allow_img_bbcode) ? $user->lang['IMAGES_ARE_ON'] : $user->lang['IMAGES_ARE_OFF'],
        'FLASH_STATUS'			=> ($allow_flash_bbcode) ? $user->lang['FLASH_IS_ON'] : $user->lang['FLASH_IS_OFF'],
        'SMILIES_STATUS'		=> ($allow_smilies) ? $user->lang['SMILIES_ARE_ON'] : $user->lang['SMILIES_ARE_OFF'],
        'URL_STATUS'			=> ($bbcode_status && $allow_url_bbcode) ? $user->lang['URL_IS_ON'] : $user->lang['URL_IS_OFF'],
        
        'S_APERCU'              => !empty($resume_display),
        'RESUME_DISPLAY'        => $resume_display,
        
        'S_BBCODE_IMG'			=> $allow_img_bbcode,
        'S_BBCODE_URL'			=> $allow_url_bbcode,
        'S_BBCODE_FLASH'		=> $allow_flash_bbcode,
        'S_BBCODE_QUOTE'		=> $allow_quote_bbcode,
        
        'POST_CONSEILS_CONTACT' => $messages[POST_CONSEILS_CONTACT],
        
        'HIDDEN_FIELDS' => build_hidden_fields(array(
            'from' => CREATION_ETAPE
        ))
    )
    );
}
function creation_message_lien($personnage,array $contacts){
    
    //Initialisation de variables
    $poll = $uid = $bitfield = $options = '';
    $bbcode_status = $allow_bbcode = $allow_urls = $allow_url_bbcode = $allow_smilies = $allow_img_bbcode = $allow_quote_bbcode = true;
    $allow_flash_bbcode = false;
    
    //Construction du texte
    $message_storage = file_get_contents(__DIR__ . '/message_lien.txt');
    $message_storage = str_replace('{PERSONNAGE}', $personnage, $message_storage);
    $contacts_txt = '';
    foreach($contacts as $contact){
        $contacts_txt .= "[*] {$contact['NOM']} - {$contact['RACE']} - {$contact['CLAN']}";
    }
    $message_storage = str_replace('{CONTACTS}', $contacts_txt, $message_storage);
    
    //Création du storage
    generate_text_for_storage($message_storage, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
    $message['message'] = $message_storage;
    $message['uid']  = $uid;
    $message['bit']  = $bitfield;
    
    return $message;
}

function creation_message_resume($personnage,array $contacts){

    //Initialisation de variables
    $poll = $uid = $bitfield = $options = '';
    $bbcode_status = $allow_bbcode = $allow_urls = $allow_url_bbcode = $allow_smilies = $allow_img_bbcode = $allow_quote_bbcode = true;
    $allow_flash_bbcode = false;

    //Construction du texte
    $message_storage = file_get_contents(__DIR__ . '/message_resume.txt');
    $message_storage = str_replace('{PERSONNAGE}', $personnage, $message_storage);
    $contacts_txt = '';
    foreach($contacts as $contact){
        $contacts_txt .= "[*] {$contact['NOM']} - {$contact['RACE']} - {$contact['CLAN']}";
    }
    $message_storage = str_replace('{CONTACTS}', $contacts_txt, $message_storage);

    //Création du storage
    generate_text_for_storage($message_storage, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
    $message['message'] = $message_storage;
    $message['uid']  = $uid;
    $message['bit']  = $bitfield;

    return $message;
}