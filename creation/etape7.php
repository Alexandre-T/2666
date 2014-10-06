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
define('CREATION_ETAPE', 7);
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

gestionContact(1);

// Output page
page_header($user->lang['CREATION_ETAPE7']);

$template->set_filenames(array(
	'body' => 'creation/etape7.html'
));

page_footer();

?>
