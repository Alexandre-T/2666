<?php
/** 
*
* common [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: common.php, v1.27 13:53 10:21 14/06/2011 Lolovoisin Exp $
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
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	//Utiliser dans 2666
	'CONSULTE_INDEX'			=> 'Consulte la page d’index du forum',
	'CONSULTE_SIGNATURE'		=> 'Consulte le contrat d’Harahel',
	'CREATION_INDEX'			=> 'S’apprête à créer son personnage',
	'CREATION_ETAPE1'			=> 'Est en train de créer son personnage',
	'CREATION_ETAPE2'			=> 'Est en train de créer un des contacts de son personnage',
	'CREATION_ETAPE3'			=> 'Est en train de décrire ses pouvoirs',
	'CREATION_ETAPE4'			=> 'Est en train de décrire ses pouvoirs',
	'SIGNATURE'					=> 'Est en train de signer ou de relire le réglement',
));

?>