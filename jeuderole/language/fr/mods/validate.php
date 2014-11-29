<?php
/** 
*
* contexte [Standard french]
*
* @package language
* @version $Id$
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
// Privacy policy and T&C
$lang = array_merge($lang, array(
	'VALIDATE_TITRE'         => 'Validation de personnage',
	'VALIDATED' 	         => 'Le personnage a été correctement validé.',
	'WAITING_MEMBERLIST'	 => 'Retourner à la liste des personnages en attente de validation',
    'INACTIF_MEMBERLIST'     => 'Retourner à la liste des personnages inactif',
    'ACTIF_MEMBERLIST'       => 'Retourner à la liste des personnages actif',
	'GOTO_FICHE'	         => 'Consulter la fiche du personnage',
    'VALIDATION_IMPOSSIBLE'  => 'Cet utilisateur ne fait pas parti du groupe «En attente de validation», vous ne pouvez pas le valider.',
    'CONSULTATION_IMPOSSIBLE' => 'Cet utilisateur ne fait parti ni du groupe «En attente de validation», ni du groupe «Actif», ni du groupe «Inactif», vous ne pouvez pas le consulter, attendez qu’il ait terminé sa fiche.',
    'REVALIDATION_IMPOSSIBLE' => 'Cet utilisateur ne fait pas partie du groupe «Inactif», vous ne pouvez pas le revalider.',
    'DEVALIDATION_IMPOSSIBLE' => 'Cet utilisateur ne fait pas partie du groupe «Actif», vous ne pouvez pas le dévalider.',
));


?>
