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

function is_user_in_group($group_id,$user_id = null){
	//initialisation des paramètres
	if(null == $user_id ){
		global $user;
		$user_id = $user->data['user_id'];
	}else{
		$user_id = (int)$user_id;
	}
	//vérification des paramètres
	$group_id= (int)$group_id;
	if(0 == $user_id * $group_id){
		//erreur le groupe et l'utilisateur 0 n'existe pas
		return false;
	}
	global $db;
	static $users_groups;

	if (!is_array($users_groups)){
		$users_groups = array();
	}
	if (!array_key_exists($user_id, $users_groups)){
		$users_groups[$user_id]=array();
	}

	if (0 === count($users_groups[$user_id])){
		//il faut remplir le tableau avec la requête sql
		$sql = 'SELECT group_id
			FROM ' . USER_GROUP_TABLE . '
			WHERE user_id = ' . $user_id .' ORDER BY group_id';
		$result = $db->sql_query($sql);
		while ($users_groups[$user_id][] = $db->sql_fetchfield('group_id')){}
		$db->sql_freeresult($result);
		unset($sql);
	}
	return in_array($group_id, $users_groups[$user_id]);
}
/**
 * Get clan from personnage
 * 
 * @param int clan
 * @param int sexe (par défaut AT_HOMME)
 * 
 * @return string Clan
 */
function get_clan($clan, $sexe = AT_HOMME){
    $homme = $sexe !== AT_FEMME;
    switch ($clan) {
        case AT_ASMODEEN:
            return $homme?'Asmodeen':'Asmodéenne';
        case AT_INFILTRE:
            return $homme?'Infiltré':'Infiltrée';
        case AT_INSOUMIS:
            return $homme?'Insoumis':'Insoumise';
        case AT_IZANAGHI:
            return 'Izanaghi';
        case AT_VESTAL:
            return $homme?'Vestal':'Vestale';
        default:
            return 'Sans clan';
    }    
}

/**
 * Get contact avatar
 *
 * @param integer number of the contact
 * @param string $avatar Users assigned avatar name
 * @param int $avatar_type Type of avatar
 * @param string $avatar_width Width of users avatar
 * @param string $avatar_height Height of users avatar
 * @param string $alt Optional language string for alt tag within image, can be a language key or text
 * @param bool $ignore_config Ignores the config-setting, to be still able to view the avatar in the UCP
 *
 * @return string Avatar image
 */
function get_contact_avatar($contact, $avatar, $avatar_type, $avatar_width, $avatar_height, $alt = 'USER_AVATAR', $ignore_config = false)
{
    global $user, $config, $phpbb_root_path, $phpEx;
    
    $contact = max(1,min(4,(int)$contact));

    if (empty($avatar) || !$avatar_type || (!$config['allow_avatar'] && !$ignore_config))
    {
        return '';
    }

    $avatar_img = '';

    switch ($avatar_type)
    {
        case AVATAR_UPLOAD:
            if (!$config['allow_avatar_upload'] && !$ignore_config)
            {
                return '';
            }
            $avatar_img = $phpbb_root_path . "download/contact.$phpEx?contact=$contact&amp;avatar=";
            break;

        case AVATAR_GALLERY:
            if (!$config['allow_avatar_local'] && !$ignore_config)
            {
                return '';
            }
            $avatar_img = $phpbb_root_path . $config['avatar_gallery_path'] . '/';
            break;

        case AVATAR_REMOTE:
            if (!$config['allow_avatar_remote'] && !$ignore_config)
            {
                return '';
            }
            break;
    }

    $avatar_img .= $avatar;
    return '<img src="' . (str_replace(' ', '%20', $avatar_img)) . '" width="' . $avatar_width . '" height="' . $avatar_height . '" alt="' . ((!empty($user->lang[$alt])) ? $user->lang[$alt] : $alt) . '" />';
}