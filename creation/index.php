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
define('CREATION_ETAPE', 0);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../jeuderole/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
include($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_creation.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_popup.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/creation');

creation_verification(CREATION_ETAPE);

$messages = get_texts_for_popup(array(POST_SYNOPSIS,POST_BACKGROUND));

// Output page
page_header($user->lang['CREATION_TITRE']);

$template->assign_vars(array(
	'POST_SYNOPSIS'		=> $messages[POST_SYNOPSIS],
	'POST_BACKGROUND'	=> $messages[POST_BACKGROUND],
));

$template->set_filenames(array(
	'body' => 'creation/index.html')
);

page_footer();

?>