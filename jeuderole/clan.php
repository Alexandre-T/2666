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
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include ($phpbb_root_path . 'common.' . $phpEx);
include ($phpbb_root_path . 'includes/functions_display.' . $phpEx);
// AT - BEGIN MOD FICHE
include ($phpbb_root_path . 'includes/mods/functions_user.' . $phpEx);
// AT - END MOD FICHE

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup(array(
    'mods/clan',
    'groups'
));

// www.phpBB-SEO.com SEO TOOLKIT BEGIN
if (! empty($_REQUEST['un'])) {
    $_REQUEST['un'] = rawurldecode($_REQUEST['un']);
    if (! $phpbb_seo->is_utf8($_REQUEST['un'])) {
        $_REQUEST['un'] = utf8_normalize_nfc(utf8_recode($_REQUEST['un'], 'ISO-8859-1'));
    }
}
// www.phpBB-SEO.com SEO TOOLKIT END

// Can this user view profiles/memberlist?
if (! $auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel')) {
    if ($user->data['user_id'] != ANONYMOUS) {
        trigger_error('NO_VIEW_USERS');
    }
    
    login_box('', ((isset($user->lang['LOGIN_EXPLAIN_' . strtoupper($mode)])) ? $user->lang['LOGIN_EXPLAIN_' . strtoupper($mode)] : $user->lang['LOGIN_EXPLAIN_MEMBERLIST']));
}

// Vérification du clan
$clan_id = request_var('clan', 0);
if (! in_array($clan_id, array(
    AT_ASMODEEN,
    AT_INFILTRE,
    AT_INSOUMIS,
    AT_IZANAGHI,
    AT_VESTAL,
    AT_SKJALDMEYJAR,
    AT_SANSCLAN,
))) {
    trigger_error('NO_CLAN');
}

switch ($clan_id){
    case AT_ASMODEEN :
        $page_title = $user->lang['ASMODEENS'];
        break;
    case AT_INFILTRES :
        $page_title = $user->lang['INFILTRES'];
        break;
    case AT_INSOUMIS :
        $page_title = $user->lang['INSOUMIS'];
        break;
    case AT_IZANAGHI :
        $page_title = $user->lang['IZANAGHIS'];
        break;
    case AT_SANSCLAN:
        $page_title = $user->lang['SANSCLAN'];
        break;
    case AT_SKJALDMEYJAR :
        $page_title = $user->lang['SKJALDMEYJAR'];
        break;
    case AT_VESTAL :
        $page_title = $user->lang['VESTALES'];
        break;
}


// The basic memberlist
$template_html = 'clan_body.html';

// We JOIN here to save a query for determining membership for hidden groups. ;)
$sql = 'SELECT g.*, ug.user_id
		FROM ' . GROUPS_TABLE . ' g
		LEFT JOIN ' . USER_GROUP_TABLE . ' ug ON (ug.user_pending = 0 AND ug.user_id = ' . $user->data['user_id'] . " AND ug.group_id = $clan_id)
		WHERE g.group_id = $clan_id";
$result = $db->sql_query($sql);
$group_row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (! $group_row) {
    trigger_error('NO_CLAN');
}

$template->assign_vars(array(
    'CLAN_DESCRIPTION' => generate_text_for_display($group_row['group_desc'], $group_row['group_desc_uid'], $group_row['group_desc_bitfield'], $group_row['group_desc_options']),
    'CLAN_NAME' => ($group_row['group_type'] == GROUP_SPECIAL) ? $user->lang['G_' . $group_row['group_name']] : $group_row['group_name'],
    'CLAN_COLOR' => $group_row['group_colour']
));

// Get us some users :D
$sql = "SELECT DISTINCT u.user_id
		FROM " . USERS_TABLE . " u
		INNER JOIN " . PROFILE_FIELDS_DATA_TABLE . " p ON (p.user_id = u.user_id)
		LEFT OUTER JOIN " . USER_GROUP_TABLE . " ug ON (u.user_id = ug.user_id)
		WHERE u.user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ")
		  AND $clan_id IN (p.pf_ca_clan,p.pf_cb_clan,p.pf_cc_clan,p.pf_cd_clan,ug.group_id)
		  and p.pf_actif = " . AT_ACTIF ."
		ORDER BY u.username";
$result = $db->sql_query($sql);

$user_list = array();
while ($row = $db->sql_fetchrow($result)) {
    $user_list[] = (int) $row['user_id'];
}
$db->sql_freeresult($result);

// So, did we get any users?
if (sizeof($user_list)) {
    // Session time?! Session time...
    $sql = 'SELECT session_user_id, MAX(session_time) AS session_time
				FROM ' . SESSIONS_TABLE . '
				WHERE session_time >= ' . (time() - $config['session_length']) . '
					AND ' . $db->sql_in_set('session_user_id', $user_list) . '
				GROUP BY session_user_id';
    $result = $db->sql_query($sql);
    
    $session_times = array();
    while ($row = $db->sql_fetchrow($result)) {
        $session_times[$row['session_user_id']] = $row['session_time'];
    }
    $db->sql_freeresult($result);
    
    $sql = 'SELECT *
        FROM ' . USERS_TABLE . '
        WHERE ' . $db->sql_in_set('user_id', $user_list);
    $result = $db->sql_query($sql);
    
    $id_cache = array();
    while ($row = $db->sql_fetchrow($result)) {
        $row['session_time'] = (! empty($session_times[$row['user_id']])) ? $session_times[$row['user_id']] : 0;
        $row['last_visit'] = (! empty($row['session_time'])) ? $row['session_time'] : $row['user_lastvisit'];
        
        $id_cache[$row['user_id']] = $row;
    }
    $db->sql_freeresult($result);
    // Load custom profile fields
    include_once ($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
    $cp = new custom_profile();
    
    // Grab all profile fields from users in id cache for later use - similar to the poster cache
    $profile_fields_cache = $cp->generate_profile_fields_template('grab', $user_list);
    
    for ($i = 0, $end = sizeof($user_list); $i < $end; ++ $i) {
        $user_id = $user_list[$i];
        $row = & $id_cache[$user_id];
        $is_leader = (isset($row['group_leader']) && $row['group_leader']) ? true : false;
        
        $cp_row = array();
        $cp_row = (isset($profile_fields_cache[$user_id])) ? $cp->generate_profile_fields_template('show', false, $profile_fields_cache[$user_id]) : array();
        
        $memberrow = array();
        $profiles = show_profile($row, $clan_id, $profile_fields_cache[$user_id]);
        foreach ($profiles as $profile){
            $memberrow = array_merge($profile, array(
                'ROW_NUMBER' => $i + 1,
                
                'S_CUSTOM_PROFILE' => true,
                'S_GROUP_LEADER' => $is_leader,
                
                // @FIXME unique link
                'U_VIEW_PROFILE' => append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $user_id)
            ));
            $memberrow = array_merge($memberrow, $cp_row['row']);
            $template->assign_block_vars('memberrow', $memberrow);
        }
        
        if (isset($cp_row['blockrow']) && sizeof($cp_row['blockrow'])) {
            foreach ($cp_row['blockrow'] as $field_data) {
                $template->assign_block_vars('memberrow.custom_fields', $field_data);
            }
        }
        
        unset($id_cache[$user_id]);
    }
}

// Generate page
$template->assign_vars(array(
    'S_SHOW_GROUP' => true,
    'S_VIEWONLINE' => $auth->acl_get('u_viewonline')
));

// Output the page
page_header($page_title, false);

$template->set_filenames(array(
    'body' => $template_html
));
make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

page_footer();

/**
 * Prepare profile data
 */
function show_profile($data, $clan_id, $pf)
{
    global $config, $auth, $template, $user, $phpEx, $phpbb_root_path,$phpbb_seo;
    
    $username = $data['username'];
    $user_id = $data['user_id'];
    
    if ($config['load_onlinetrack']) {
        $update_time = $config['load_online_time'] * 60;
        $online = (time() - $update_time < $data['session_time'] && ((isset($data['session_viewonline']) && $data['session_viewonline']) || $auth->acl_get('u_viewonline'))) ? true : false;
    } else {
        $online = false;
    }
    
    if ($data['user_allow_viewonline'] || $auth->acl_get('u_viewonline')) {
        $last_visit = (! empty($data['session_time'])) ? $data['session_time'] : $data['user_lastvisit'];
    } else {
        $last_visit = '';
    }
    
    $resultats = array();
    //PHPBB SEO Hack ALEXANDRE N'enlève pas ça sinon tous les liens éclatent !!!!
    get_username_string('full', $user_id, $username, $data['user_colour']);
    // Dump it out to the template
    if ($pf['clan']['value'] == $clan_id) {        
        if (!empty($pf['fiche']['value'])){
            $u_fiche = append_sid("{$phpbb_root_path}personnages-resumes/". seoencode($username) ."-t{$pf['fiche']['value']}.html");            
        }else{
            $u_fiche = null;
        }
        
        $resultats[] = array(
            'JOINED' => $user->format_date($data['user_regdate']),
            'VISITED' => (empty($last_visit)) ? ' - ' : $user->format_date($last_visit),
            'POSTS' => ($data['user_posts']) ? $data['user_posts'] : 0,
            
            'USERNAME_FULL' => get_username_string('full', $user_id, $username, $data['user_colour']),
            'USERNAME' => get_username_string('username', $user_id, $username, $data['user_colour']),
            'USER_COLOR' => get_username_string('colour', $user_id, $username, $data['user_colour']),
            
            'S_CONTACT' => false,
            'U_FICHE' => $u_fiche,
            
            'U_SEARCH_USER' => ($auth->acl_get('u_search')) ? append_sid("{$phpbb_root_path}search.$phpEx", "author_id=$user_id&amp;sr=posts") : ''
        );
    }
    if ($pf['ca_clan']['value'] == $clan_id ) {
        $resultats[] = array(
            'JOINED' => $user->format_date($data['user_regdate']),
            'VISITED' => (empty($last_visit)) ? ' - ' : $user->format_date($last_visit),
            'POSTS' => ($data['user_posts']) ? $data['user_posts'] : 0,
            
            'CONTACT_FULL' => get_contact_string('full', $user_id, $pf['ca_nom']['value'], get_contact_couleur($pf['ca_race']['value'])) ,
            'CONTACT' => get_contact_string('username', $user_id, $pf['ca_nom']['value'], get_contact_couleur($pf['ca_race']['value'])),
            'CONTACT_COLOR' => get_contact_string('colour', $user_id, $pf['ca_nom']['value'], get_contact_couleur($pf['ca_race']['value'])),
            'PRINCIPAL_FULL' => get_username_string('full', $user_id, $username, $data['user_colour']),
            
            'S_CONTACT' => true,
            
            'U_CONTACT' => append_sid("{$phpbb_root_path}personnages-resumes/". seoencode($pf['ca_nom']['value']) ."-t{$pf['ca_fiche']['value']}.html"),
            'U_SEARCH_USER' => ($auth->acl_get('u_search')) ? append_sid("{$phpbb_root_path}search.$phpEx", "author_id=$user_id&amp;sr=posts") : ''
        );
    }
    if ($pf['cb_clan']['value'] == $clan_id ) {
        $resultats[] = array(
            'JOINED' => $user->format_date($data['user_regdate']),
            'VISITED' => (empty($last_visit)) ? ' - ' : $user->format_date($last_visit),
            'POSTS' => ($data['user_posts']) ? $data['user_posts'] : 0,
            
            'CONTACT_FULL' => get_contact_string('full', $user_id, $pf['cb_nom']['value'], get_contact_couleur($pf['cb_race']['value'])) ,
            'CONTACT' => get_contact_string('username', $user_id, $pf['cb_nom']['value'], get_contact_couleur($pf['cb_race']['value'])),
            'CONTACT_COLOR' => get_contact_string('colour', $user_id, $pf['cb_nom']['value'], get_contact_couleur($pf['cb_race']['value'])),
            'PRINCIPAL_FULL' => get_username_string('full', $user_id, $username, $data['user_colour']),
            
            'S_CONTACT' => true,
            
            'U_CONTACT' => append_sid("{$phpbb_root_path}personnages-resumes/". seoencode($pf['cb_nom']['value']) ."-t{$pf['cb_fiche']['value']}.html"),
            'U_SEARCH_USER' => ($auth->acl_get('u_search')) ? append_sid("{$phpbb_root_path}search.$phpEx", "author_id=$user_id&amp;sr=posts") : ''
        );
    }
    if ($pf['cc_clan']['value'] == $clan_id && $pf['cc_actif']['value'] == AT_ACTIF) {
        $resultats[] = array(
            'JOINED' => $user->format_date($data['user_regdate']),
            'VISITED' => (empty($last_visit)) ? ' - ' : $user->format_date($last_visit),
            'POSTS' => ($data['user_posts']) ? $data['user_posts'] : 0,
            
            'CONTACT_FULL' => get_contact_string('full', $user_id, $pf['cc_nom']['value'], get_contact_couleur($pf['cc_race']['value'])) ,
            'CONTACT' => get_contact_string('username', $user_id, $pf['cc_nom']['value'], get_contact_couleur($pf['cc_race']['value'])),
            'CONTACT_COLOR' => get_contact_string('colour', $user_id, $pf['cc_nom']['value'], get_contact_couleur($pf['cc_race']['value'])),
            'PRINCIPAL_FULL' => get_username_string('full', $user_id, $username, $data['user_colour']),
            
            'S_CONTACT' => true,
            
            'U_CONTACT' => append_sid("{$phpbb_root_path}personnages-resumes/". seoencode($pf['cc_nom']['value']) ."-t{$pf['cc_fiche']['value']}.html"),
            'U_SEARCH_USER' => ($auth->acl_get('u_search')) ? append_sid("{$phpbb_root_path}search.$phpEx", "author_id=$user_id&amp;sr=posts") : ''
        );
    }
    if ($pf['cd_clan']['value'] == $clan_id && $pf['cc_actif']['value'] == AT_ACTIF) {
        $resultats[] = array(
            'JOINED' => $user->format_date($data['user_regdate']),
            'VISITED' => (empty($last_visit)) ? ' - ' : $user->format_date($last_visit),
            'POSTS' => ($data['user_posts']) ? $data['user_posts'] : 0,
            
            'CONTACT_FULL' => get_contact_string('full', $user_id, $pf['cd_nom']['value'], get_contact_couleur($pf['cd_race']['value'])) ,
            'CONTACT' => get_contact_string('username', $user_id, $pf['cd_nom']['value'], get_contact_couleur($pf['cd_race']['value'])),
            'CONTACT_COLOR' => get_contact_string('colour', $user_id, $pf['cd_nom']['value'], get_contact_couleur($pf['cd_race']['value'])),
            'PRINCIPAL_FULL' => get_username_string('full', $user_id, $username, $data['user_colour']),
            
            'S_CONTACT' => true,
            
            'U_CONTACT' => append_sid("{$phpbb_root_path}personnages-resumes/". seoencode($pf['cd_nom']['value']) ."-t{$pf['cd_fiche']['value']}.html"),
            'U_SEARCH_USER' => ($auth->acl_get('u_search')) ? append_sid("{$phpbb_root_path}search.$phpEx", "author_id=$user_id&amp;sr=posts") : ''
        );
    }
    return $resultats;
}

function _sort_last_active($first, $second)
{
    global $id_cache, $sort_dir;
    
    $lesser_than = ($sort_dir === 'd') ? - 1 : 1;
    
    if (isset($id_cache[$first]['group_leader']) && $id_cache[$first]['group_leader'] && (! isset($id_cache[$second]['group_leader']) || ! $id_cache[$second]['group_leader'])) {
        return - 1;
    } else 
        if (isset($id_cache[$second]['group_leader']) && (! isset($id_cache[$first]['group_leader']) || ! $id_cache[$first]['group_leader']) && $id_cache[$second]['group_leader']) {
            return 1;
        } else {
            return $lesser_than * (int) ($id_cache[$first]['last_visit'] - $id_cache[$second]['last_visit']);
        }
}

?>