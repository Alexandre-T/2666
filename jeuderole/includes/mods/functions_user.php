<?php

/**
 * AT MOD : GROUPE PARTICULIER D'UN UTILISATEUR
 *
 *  Cette méthode cherche les groupes d'un utilisateur dans la base de données.
 *  Elle stocke le résultat de façon statique
 *  Si la méthode est rappelée, la requête SQL n'est pas relancée.
 *
 *  @param integer user_id;
 *  @param integer group_id;
 *  @return boolean
 */
function is_user_in_group($group_id, $user_id = null)
{
    // initialisation des paramètres
    if (null == $user_id) {
        global $user;
        $user_id = $user->data['user_id'];
    } else {
        $user_id = (int) $user_id;
    }
    // vérification des paramètres
    $group_id = (int) $group_id;
    if (0 == $user_id * $group_id) {
        // erreur le groupe et l'utilisateur 0 n'existe pas
        return false;
    }
    global $db;
    static $users_groups;
    
    if (! is_array($users_groups)) {
        $users_groups = array();
    }
    if (! array_key_exists($user_id, $users_groups)) {
        $users_groups[$user_id] = array();
    }
    
    if (0 === count($users_groups[$user_id])) {
        // il faut remplir le tableau avec la requête sql
        $sql = 'SELECT group_id
			FROM ' . USER_GROUP_TABLE . '
			WHERE user_id = ' . $user_id . ' ORDER BY group_id';
        $result = $db->sql_query($sql);
        while ($users_groups[$user_id][] = $db->sql_fetchfield('group_id')) {}
        $db->sql_freeresult($result);
        unset($sql);
    }
    return in_array($group_id, $users_groups[$user_id]);
}

/**
 * Get clan from personnage
 *
 * @param
 *            int clan
 * @param
 *            int sexe (par défaut AT_HOMME)
 *            
 * @return string Clan
 */
function get_clan($clan, $sexe = AT_HOMME)
{
    $homme = ($sexe != AT_FEMME);
    switch ($clan) {
        case AT_ASMODEEN:
            return $homme ? 'Asmodéen' : 'Asmodéenne';
        case AT_INFILTRE:
            return $homme ? 'Infiltré' : 'Infiltrée';
        case AT_INSOUMIS:
            return $homme ? 'Insoumis' : 'Insoumise';
        case AT_IZANAGHI:
            return 'Izanaghi';
        case AT_VESTAL:
            return $homme ? 'Vestal' : 'Vestale';
        case AT_SKJALDMEYJAR:
            return 'Skjaldmey';
        default:
            return 'Sans clan';
    }
}

/**
 * Get race from personnage
 *
 * @param
 *            int race
 * @param
 *            int sexe (par défaut AT_HOMME)
 *            
 * @return string Race
 */
function get_race($race, $sexe = AT_HOMME)
{
    $homme = $sexe != AT_FEMME;
    switch ($race) {
        case AT_NEPHILIM:
            return 'Nephilim';
        case AT_ORIGINEL:
            return $homme ? 'Originel' : 'Originelle';
        default:
            return $homme ? 'Humain' : 'Humaine';
    }
}

/**
 * Get contact avatar
 *
 * @param
 *            integer number of the contact
 * @param string $avatar
 *            Users assigned avatar name
 * @param int $avatar_type
 *            Type of avatar
 * @param string $avatar_width
 *            Width of users avatar
 * @param string $avatar_height
 *            Height of users avatar
 * @param string $alt
 *            Optional language string for alt tag within image, can be a language key or text
 * @param bool $ignore_config
 *            Ignores the config-setting, to be still able to view the avatar in the UCP
 *            
 * @return string Avatar image
 */
function get_contact_avatar($contact, $avatar, $avatar_type, $avatar_width, $avatar_height, $alt = 'USER_AVATAR', $ignore_config = false)
{
    global $user, $config, $phpbb_root_path, $phpEx;
    
    $contact = max(1, min(4, (int) $contact));
    
    if (empty($avatar) || ! $avatar_type || (! $config['allow_avatar'] && ! $ignore_config)) {
        return '';
    }
    
    $avatar_img = '';
    
    switch ($avatar_type) {
        case AVATAR_UPLOAD:
            if (! $config['allow_avatar_upload'] && ! $ignore_config) {
                return '';
            }
            $avatar_img = $phpbb_root_path . "download/contact.$phpEx?contact=$contact&amp;avatar=";
            break;
        
        case AVATAR_GALLERY:
            if (! $config['allow_avatar_local'] && ! $ignore_config) {
                return '';
            }
            $avatar_img = $phpbb_root_path . $config['avatar_gallery_path'] . '/';
            break;
        
        case AVATAR_REMOTE:
            if (! $config['allow_avatar_remote'] && ! $ignore_config) {
                return '';
            }
            break;
    }
    
    $avatar_img .= $avatar;
    return '<img src="' . (str_replace(' ', '%20', $avatar_img)) . '" width="' . $avatar_width . '" height="' . $avatar_height . '" alt="' . ((! empty($user->lang[$alt])) ? $user->lang[$alt] : $alt) . '" />';
}

/**
 * Remplace tous les caractères bizarre par -
 *
 * @param string $nom            
 * @return string
 */
function seoencode($nom)
{
    return preg_replace('#[^A-Za-z0-9]+#', '-', $nom);
}
/**
 * Donne la couleur d'un contact
 */
function get_contact_couleur($race){
    switch ($race){
        case AT_ORIGINEL :
            return COULEUR_ORIGINEL;
        case AT_HUMAIN :
            return COULEUR_HUMAIN;
        case AT_NEPHILIM :
            return COULEUR_NEPHILIM;        
    }
    return;
}
/**
 * Donne les valeurs de contacts
 *
 * @param string $nom
 * @return string
 */
function get_contact_string($mode, $user_id, $username, $username_colour = '', $guest_username = false, $custom_profile_url = false)
{
    static $_profile_cache;
    
    // We cache some common variables we need within this function
    if (empty($_profile_cache)) {
        global $phpbb_root_path, $phpEx;
        
        $_profile_cache['base_url'] = append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u={USER_ID}');
        $_profile_cache['tpl_noprofile'] = '{USERNAME}';
        $_profile_cache['tpl_noprofile_colour'] = '<span style="color: {USERNAME_COLOUR};" class="username-coloured">{USERNAME}</span>';
        $_profile_cache['tpl_profile'] = '<a href="{PROFILE_URL}">{USERNAME}</a>';
        $_profile_cache['tpl_profile_colour'] = '<a href="{PROFILE_URL}" style="color: {USERNAME_COLOUR};" class="username-coloured">{USERNAME}</a>';
    }
    
    global $user, $auth;
    
    // This switch makes sure we only run code required for the mode
    switch ($mode) {
        case 'full':
        case 'no_profile':
        case 'colour':
            
            // Build correct username colour
            $username_colour = ($username_colour) ? '#' . $username_colour : '';
            
            // Return colour
            if ($mode == 'colour') {
                return $username_colour;
            }
        
        // no break;
        
        case 'username':
            
            // Build correct username
            if ($guest_username === false) {
                $username = ($username) ? $username : $user->lang['GUEST'];
            } else {
                $username = ($user_id && $user_id != ANONYMOUS) ? $username : ((! empty($guest_username)) ? $guest_username : $user->lang['GUEST']);
            }
            
            // Return username
            if ($mode == 'username') {
                return $username;
            }
        
        // no break;
        
        case 'profile':
            
            // Build correct profile url - only show if not anonymous and permission to view profile if registered user
            // For anonymous the link leads to a login page.
            if ($user_id && $user_id != ANONYMOUS && ($user->data['user_id'] == ANONYMOUS || $auth->acl_get('u_viewprofile'))) {
                // www.phpBB-SEO.com SEO TOOLKIT BEGIN
                // $profile_url = ($custom_profile_url !== false) ? $custom_profile_url . '&amp;u=' . (int) $user_id : str_replace(array('={USER_ID}', '=%7BUSER_ID%7D'), '=' . (int) $user_id, $_profile_cache['base_url']);
                global $phpbb_seo, $phpbb_root_path, $phpEx;                
                $phpbb_seo->set_user_url($username, $user_id);
                
                if ($custom_profile_url !== false) {
                    $profile_url = reapply_sid($custom_profile_url . (strpos($custom_profile_url, '?') !== false ? '&amp;' : '?') . 'u=' . (int) $user_id);
                } else {
                    $profile_url = append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . (int) $user_id);
                }
                // www.phpBB-SEO.com SEO TOOLKIT END
            } else {
                $profile_url = '';
            }
            
            // Return profile
            if ($mode == 'profile') {
                return $profile_url;
            }
        
        // no break;
    }
    
    if (($mode == 'full' && ! $profile_url) || $mode == 'no_profile') {
        return str_replace(array(
            '{USERNAME_COLOUR}',
            '{USERNAME}'
        ), array(
            $username_colour,
            $username
        ), (! $username_colour) ? $_profile_cache['tpl_noprofile'] : $_profile_cache['tpl_noprofile_colour']);
    }
    
    return str_replace(array(
        '{PROFILE_URL}',
        '{USERNAME_COLOUR}',
        '{USERNAME}'
    ), array(
        $profile_url,
        $username_colour,
        $username
    ), (! $username_colour) ? $_profile_cache['tpl_profile'] : $_profile_cache['tpl_profile_colour']);
}