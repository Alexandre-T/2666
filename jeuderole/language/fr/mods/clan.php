<?php
/**
*
* clan [Standard french]
*
* @package language
* @version $Id: clan.php 79 2013-10-01 00:10:32Z Skouat $
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
// Some characters you may want to copy&paste:
// ’ » “ ” …

$lang = array_merge($lang, array(
    'ASMODEENS'             => 'Asmodéens',
    'INFILTRES'             => 'Infiltrés',
    'INSOUMIS'              => 'Insoumis',
    'IZANAGHIS'             => 'Izanaghis',
    'VESTALES'              => 'Vestales',
    'SANS_CLAN'              => 'Sans clan',
    'SKJALDMEYJAR'          => 'Skjaldmeyjar',

    'ASMODEENS_DESCRIPTION'     => 'Asmodéens',
    'INFILTRES_DESCRIPTION'     => 'Infiltrés',
    'INSOUMIS_DESCRIPTION'      => 'Insoumis',
    'IZANAGHIS_DESCRIPTION'     => 'Izanaghis',
    'VESTALES_DESCRIPTION'      => 'Vestales',
    'SKJALDMEYJAR_DESCRIPTION'  => 'Skjaldmeyjar',
    
	'CLAN_MEMBERS'			=> 'Membres du clan',
    'CLANLIST'              => 'Membre d’un clan',
    'CONTACT'   			=> 'Contact',
    'CONTACT_DE'   			=> 'Contact de',
	
    'LAST_ACTIVE'			=> 'Dernière visite',
    
    'NO_CLAN'   			=> 'Ce clan n’existe pas.',
    
    'PERSONNAGE_PRINCIPAL'  => 'Personnage principal',
    
));

?>