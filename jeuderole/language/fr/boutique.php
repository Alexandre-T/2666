<?php
/** 
*
* boutique [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: boutique.php, v1.25 2009/12/16 16:36:00 Elglobo Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
   $lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …

$lang = array_merge($lang, array(
	'BOUTIQUE'	        => 'La boutique de 2666',
	'BIBLIOTHEQUE'	    => 'Notre bibliothèque',
	'BOOKMARK_ARCHESS'	=> 'La bibliothèque d’Archess',
	'BOOKMARK_HARAHEL'	=> 'La bibliothèque d’Harahel',
	'AND_YOU'		    => 'Si vous aussi vous voulez recommander quelques livres, surtout n’hésitez pas à nous en faire part dans la rubrique partenariat. J’ajouterai votre librairie.',
));

?>