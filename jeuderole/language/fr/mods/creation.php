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
	'CREATION_TITRE'	=>	'Création de personnage',
	'CREATION_ETAPE1'	=>	'Création de personnage : Étape 1',
	'CREATION_ETAPE2'	=>	'Création de personnage : Étape 2',
	'CREATION_ETAPE3'	=>	'Création de personnage : Étape 3',
	'CREATION_ETAPE4'	=>	'Création de personnage : Étape 4',
	'CREATION_ETAPE5'	=>	'Création de personnage : Étape 5',
	'CREATION_ETAPE6'	=>	'Création de personnage : Étape 6',
	'CREATION_ETAPE7'	=>	'Création de personnage : Étape 7',
    'CREATION_ETAPE8'	=>	'Création de personnage : Étape 8',
    'CREATION_ETAPE9'	=>	'Création de personnage : Étape 9',
    'CREATION_ETAPE10'	=>	'Création de personnage : Étape 10',
    'CREATION_ETAPE11'	=>	'Création de personnage : Étape 11',
));

?>