<?php
/**
 * AT MOD : GROUPE PARTICULIER D'UN UTILISATEUR
 *
 *  Cette méthode cherche les groupes d'un utilisateur dans la base de données.
 *  Elle stocke le résultat de façon statique
 *  Si la méthode est rappelée, la requête SQL n'est pas relancée.
 *
 *  @param integer user_id;
 *  @param integer group_id;
 *  @return boolean
 */

function is_user_in_group($group_id,$user_id = null){
	//initialisation des paramètres
	if(null == $user_id ){
		global $user;
		$user_id = $user->data['user_id'];
	}else{
		$user_id = (int)$user_id;
	}
	//vérification des paramètres
	$group_id= (int)$group_id;
	if(0 == $user_id * $group_id){
		//erreur le groupe et l'utilisateur 0 n'existe pas
		return false;
	}
	global $db;
	static $users_groups;

	if (!is_array($users_groups)){
		$users_groups = array();
	}
	if (!array_key_exists($user_id, $users_groups)){
		$users_groups[$user_id]=array();
	}

	if (0 === count($users_groups[$user_id])){
		//il faut remplir le tableau avec la requête sql
		$sql = 'SELECT group_id
			FROM ' . USER_GROUP_TABLE . '
			WHERE user_id = ' . $user_id .' ORDER BY group_id';
		$result = $db->sql_query($sql);
		while ($users_groups[$user_id][] = $db->sql_fetchfield('group_id')){}
		$db->sql_freeresult($result);
		unset($sql);
	}
	return in_array($group_id, $users_groups[$user_id]);
}