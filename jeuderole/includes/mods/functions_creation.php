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
 * @ignore
 */
if (! defined ( 'IN_PHPBB' )) {
	exit ();
}

/**
 * Fonctions de 2666
 */






/**
 * Fonctions de ROME
 */


/**
 * 
 * Vérifie que l'utilisateur peut participer à la création de personnage 
 * ou le redirige vers la première étape manquante
 */
function creation_verification($etape) {
	creation_verification_etape ( (int) $etape);
}

/**
 * 
 * Vérifie que l'utilisateur peut participer à la création de personnage 
 * ou le redirige vers une fonction
 */
function creation_verification_groupe() {
	global $user,$phpEx;
	if ($user->data['user_id'] == ANONYMOUS){
		login_box('', $user->lang['LOGIN_EXPLAIN_CREATION']);
		return false;
	}
	//Le personnage ne doit pas être actif
	if (is_user_in_group ( GROUPE_ACTIF )) {
		//l'utilisateur est déjà actif, on le redirige vers sa fiche
		header ( "Location: ../fiche.$phpEx?message=1" );
	}
	//L'utilisateur doit avoir signé le règlement sinon on ne le laisse pas commencer
	if (! is_user_in_group ( GROUPE_SIGNATURE )) {
		//redirection vers le réglement avec message
		header ( "Location: ../signature.$phpEx?message=1" );
	}
	if (is_user_in_group ( GROUPE_DEMANDE_CREATION )){
		//l'utilisateur est en attente, on le redirige vers la page d'attente
		header ( "Location: enattente.$phpEx?message=1" );
	}
	return true;
}

/**
 * 
 * Vérifie que l'utilisateur peut participer à la création de personnage 
 * ou le redirige vers une fonction
 */
function creation_verification_etape($etape) {
	global $user,$template,$phpEx,$phpbb_root_path;
	$user->get_profile_fields ( $user->data ['user_id'] );
	$etapes[0] = !creation_verification_groupe();
	$etapes[1] = $etapes[0];
	$etapes[2] = $etapes[1];
	$etapes[3] = empty ( $user->profile_fields ['pf_sexe'] );
	$etapes[4] = empty ( $user->profile_fields ['pf_race'] );
	$etapes[5] = empty ( $user->profile_fields ['pf_avatar'] );
	$etapes[6] = empty ( $user->profile_fields ['pf_passe'] ) || 
			     empty ( $user->profile_fields ['pf_agereel'] ) && AT_NEPHILIM == $user->profile_fields ['pf_race'] ||
			     empty ( $user->profile_fields ['pf_prenom'] ) || 
			     empty ( $user->profile_fields ['pf_nom'] ); 
	$etapes[7] = empty ( $user->profile_fields ['pf_clan'] ) ||
				 empty ( $user->profile_fields ['pf_pouvoir'] ) && AT_NEPHILIM == $user->profile_fields ['pf_race'] ||
			     empty ( $user->profile_fields ['pf_voleuse_nom'] ) && AT_NEPHILIM == $user->profile_fields ['pf_race'] ||
			     empty ( $user->profile_fields ['pf_voleuse_des'] ) && AT_NEPHILIM == $user->profile_fields ['pf_race'] ||
			     empty ( $user->profile_fields ['pf_voleuse_pouvoir'] ) && AT_NEPHILIM == $user->profile_fields ['pf_race'] ||
			     empty ( $user->profile_fields ['pf_don'] ) && AT_HUMAIN == $user->profile_fields ['pf_race'] ||
	$etapes[7] = empty ( $user->profile_fields ['pf_nature'] ) || 
			     empty ( $user->profile_fields ['pf_attitude'] ) || 
			     empty ( $user->profile_fields ['pf_defaut'] ) || 
			     empty ( $user->profile_fields ['pf_caractere'] ) || 
			     empty ( $user->profile_fields ['pf_qualite'] );
	$etapes[8] = empty( $user->profile_fields['pf_pouvoir'] ) ||
				 AT_ANDROID == $user->profile_fields ['pf_race'] && (
				 empty( $user->profile_fields['pf_plot']) || AT_NONRENSEIGNE == $user->profile_fields['pf_plot']) ||
				 AT_HUMAIN == $user->profile_fields ['pf_race'] && (
				 empty( $user->profile_fields['pf_pouvoirdeux'] ) && AT_DIEU_MINEUR != $user->profile_fields ['pf_dieu'] && AT_DIEU_ATHE != $user->profile_fields ['pf_dieu']);
	$etapes[8] = empty( $user->profile_fields['pf_anonymat'] );
				
	//redirection éventuelle		     
	switch ($etape) {
		case 7 :
		case 6 :
			if($etapes[6]){
				$location = "Location: etape5.$phpEx?message=1";
			}
		case 5 :
			if($etapes[5]){
				$location = "Location: etape4.$phpEx?message=1";
			}
		case 4 :
			if($etapes[4]){
				$location = "Location: etape3.$phpEx?message=1";
			}
		case 3 :
			if($etapes[3]){
				$location = "Location: etape2.$phpEx?message=1";
			}
		case 2 :
			if($etapes[2]){
				$location = "Location: etape1.$phpEx?message=1";
			}
	}
	if (! empty ( $location )) {
		header ( $location );
	}
	//Assignation des variables
	$l_race = (key_exists('pf_race', $user->profile_fields))?$user->profile_fields['pf_race']:AT_NONRENSEIGNE;	
	$template->assign_vars(array(	
		'ETAPE' 	          => $etape,
		'S_ACTIF' 	          => is_user_in_group(GROUPE_ACTIF),
		'S_BBCODE_ALLOWED'	  => true,
		'S_BBCODE_CHECKED'    => true,
		'S_CREATION' 		  => true,
		'POURCENTAGE'		  => $etape*10,
		
		'U_ETAPE_0'			  => append_sid("{$phpbb_root_path}../creation/index.$phpEx"),
		'U_ETAPE_1'			  => append_sid("{$phpbb_root_path}../creation/etape1.$phpEx"),
		'U_ETAPE_2'			  => append_sid("{$phpbb_root_path}../creation/etape2.$phpEx"),
		'U_ETAPE_3'			  => append_sid("{$phpbb_root_path}../creation/etape3.$phpEx"),
		'U_ETAPE_4'			  => append_sid("{$phpbb_root_path}../creation/etape4.$phpEx"),
		'U_ETAPE_5'			  => append_sid("{$phpbb_root_path}../creation/etape5.$phpEx"),
		'U_ETAPE_6'			  => append_sid("{$phpbb_root_path}../creation/etape6.$phpEx"),
		'U_ETAPE_7'			  => append_sid("{$phpbb_root_path}../creation/etape7.$phpEx"),
		'S_ETAPE_0'			  => 0 == $etape,
		'S_ETAPE_1'			  => 1 == $etape,
		'S_ETAPE_2'			  => 2 == $etape,
		'S_ETAPE_3'			  => 3 == $etape,
		'S_ETAPE_4'			  => 4 == $etape,
		'S_ETAPE_5'			  => 5 == $etape,
		'S_ETAPE_6'			  => 6 == $etape,
		'S_ETAPE_7'			  => 7 == $etape,
		'S_ETAPE_8'			  => 8 == $etape,
		'S_ETAPE_9'			  => 9 == $etape,
		'S_ETAPE_10'		  => 10 == $etape,
		
		'S_HUMAIN'		  	  => ($l_race == AT_HUMAIN),
		'S_CREE'			  => is_user_in_group(GROUPE_DEMANDE_CREATION),
		'S_INACTIF'			  => is_user_in_group(GROUPE_INACTIF),
		'S_NEPHILIM'	  	  => ($l_race == AT_NEPHILIM),
		'S_SIGNATURE' 		  => is_user_in_group(GROUPE_SIGNATURE),
		'S_SMILIES_ALLOWED'	  => true,
		'S_SMILIES_CHECKED'	  => true,
		'VAR_DUMP'    		  => var_export(array_merge($user->profile_fields,$etapes,$user->data),true),
		'U_ACTION'    		  => creation_action($etape),
	));
	return $etapes;
}

function creation_action($etape){
	global $template,$phpbb_root_path,$phpEx;
	/* Mise en place de la barre de progression */
	$template->assign_block_vars('navlinks', array(
		'S_IS_CAT'		=> false,
		'S_IS_LINK'		=> true,
		'S_IS_POST'		=> false,
		'FORUM_NAME'	=> 'Création de personnage',
		'FORUM_ID'		=> 0,
		'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}../creation/index.$phpEx"))
	);
	if(0 == $etape){
		$template->assign_block_vars('navlinks', array(
			'S_IS_CAT'		=> false,
			'S_IS_LINK'		=> true,
			'S_IS_POST'		=> false,
			'FORUM_NAME'	=> 'Introduction',
			'FORUM_ID'		=> 0,
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}../creation/index.$phpEx"))
		);
		return append_sid("{$phpbb_root_path}../creation/index.$phpEx");
	}else{
		$template->assign_block_vars('navlinks', array(
			'S_IS_CAT'		=> false,
			'S_IS_LINK'		=> true,
			'S_IS_POST'		=> false,
			'FORUM_NAME'	=> "Étape $etape sur 10",
			'FORUM_ID'		=> 0,
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}../creation/etape$etape.$phpEx"))
		);
		return append_sid("{$phpbb_root_path}../creation/etape$etape.$phpEx");
	}
}

function creation_pouvoirs($dieu,$texte=null){
	global $template,$phpbb_root_path;
	switch ($dieu){
		case AT_DIEU_JUPITER : $fichier = 'jupiter.csv'; break;
		case AT_DIEU_MINERVE : $fichier = 'minerve.csv'; break;
		case AT_DIEU_PLUTON : $fichier = 'pluton.csv'; break;
		case AT_DIEU_VENUS : $fichier = 'venus.csv'; break;
		case AT_DIEU_NEPTUNE : $fichier = 'neptune.csv'; break;
		case AT_DIEU_VESTA : $fichier = 'vesta.csv'; break;
		default : return;
	}
	$contenu = file_get_contents(append_sid("{$phpbb_root_path}../creation/ressources/$fichier"));
	$index   = 1;
	foreach(explode("\n", $contenu) as $ligne){
		$tableau = array();
		if (empty($ligne) || substr(trim($ligne[0]),0,1) == '#' )continue;
		$pouvoir  = explode(';',$ligne);
		if (count($pouvoir) != 2) continue;
		$template->assign_block_vars('pouvoirs',array(
			'ID' => ++$index,
			'TITRE' => $pouvoir[0],
			'DESCRIPTION' => $pouvoir[1],
			'S_SELECTED' => $texte == "[b]{$pouvoir[0]}[/b] : [i]{$pouvoir[1]}[/i]" 
		));
	}
}
function creation_pouvoir($idPouvoir,$dieu){
	global $phpbb_root_path;
	switch ($dieu){
		case AT_DIEU_JUPITER : $fichier = 'jupiter.csv'; break;
		case AT_DIEU_MINERVE : $fichier = 'minerve.csv'; break;
		case AT_DIEU_PLUTON : $fichier = 'pluton.csv'; break;
		case AT_DIEU_VENUS : $fichier = 'venus.csv'; break;
		case AT_DIEU_NEPTUNE : $fichier = 'neptune.csv'; break;
		case AT_DIEU_VESTA : $fichier = 'vesta.csv'; break;
		default : return;
	}
	$contenu = file_get_contents(append_sid("{$phpbb_root_path}../creation/ressources/$fichier"));
	$index   = 1;
	foreach(explode("\n", $contenu) as $ligne){
		$tableau = array();
		if (empty($ligne) || substr(trim($ligne[0]),0,1) == '#' )continue;
		$pouvoir  = explode(';',$ligne);
		if (count($pouvoir) != 2) continue;
		if ($idPouvoir <> ++$index) continue;
		return "[b]{$pouvoir[0]}[/b] : [i]{$pouvoir[1]}[/i]";
	}
}
?>
