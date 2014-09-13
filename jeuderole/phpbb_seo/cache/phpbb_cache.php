<?php
/**
* phpBB_SEO Class
* www.phpBB-SEO.com
* @package Advanced phpBB3 SEO mod Rewrite
*/
if (!defined('IN_PHPBB')) {
	exit;
}
$this->cache_config['settings'] = array ( 'url_rewrite' => true, 'modrtype' => 3, 'sql_rewrite' => true, 'profile_inj' => true, 'profile_vfolder' => true, 'profile_noids' => true, 'rewrite_usermsg' => true, 'rewrite_files' => false, 'rem_sid' => true, 'rem_hilit' => true, 'rem_small_words' => false, 'virtual_folder' => true, 'virtual_root' => false, 'cache_layer' => true, 'rem_ids' => true, 'copyrights' => array ( 'img' => false, 'txt' => '', 'title' => '', ), );
$this->cache_config['forum'] = array ( 1 => 'presentation-du-jeu', 2 => 'description-du-jeu', 3 => 'creation-personnage', 4 => 'aides-de-jeu', 5 => 'personnage', 6 => 'fiche', 7 => 'liens', 8 => 'resumes-rp', );
?>