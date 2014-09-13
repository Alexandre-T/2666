<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) Alexandre TRANCHANT
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './jeuderole/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_user.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

//Chargement de la langue
$user->add_lang('mods/signature', FALSE, FALSE);

//Recherche conternant l'utilisateur
$s_signature = is_user_in_group ( GROUPE_SIGNATURE );

//Récupération des variables
$message = request_var('message', 0);
$age = request_var('age', 0);
$bienseance = request_var('bienseance', 0);
$pi = request_var('pi', 0);


var_dump($age,$bienseance,$pi,$_POST);
if (! $user->data ['is_bot'] && $user->data ['is_registered'] && ! $s_signature){
	//Il ne fait pas parti des signatures mais est-ce qu'il vient de signer ?
	//Analyse éventuelle des variables postées en formulaire
	if (isset($_POST) && is_array($_POST) && count($_POST)){
		
		if (20 === $age + $bienseance +$pi){
			$erreur = false;
			//Insertion !
			group_user_add(GROUPE_SIGNATURE,array($user->data['user_id']),array($user->data['username']));
			$s_signature = true;
			if (1 == $message){
				header('Location: creation/index.php');
			}
		}else{
			$erreur = true;			
		}
	}
}


//Construction de l'entête
page_header($user->lang['SIGNATURE_TITRE']);

$template->assign_vars(array(
	'BACKGROUND'  => 'reglement',
	'S_MESSAGE'	  => 1 == $message,
	'S_SIGNATURE' => $s_signature,
	'S_ERREUR' 	  => (isset($erreur) && $erreur),
));

//Appel de la bonne page
$template->set_filenames(array(
	'body' => 'signature.html')
);

//Création de la jumpbox annulé
//make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));

//Construction du pied de page
page_footer();

