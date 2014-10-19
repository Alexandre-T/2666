<?php
class Reserve{
    const NOM     = 0;
	const AVATAR  = 1;
    const TOPIC   = 2;
    const COULEUR = 3;
	const CASES   = 4;  
		
	public static function loadAvatars(){
	    global $db, $template;
		//requête SQL
		$sql = $db->sql_build_query('SELECT', array(
			'SELECT'	=> 'u.user_id, u.group_id as default_group, u.username, u.username_clean, u.user_colour,'
		                  .'p.pf_avatar as avatar, p.pf_prenom as prenom, p.pf_nom as nom, p.pf_fiche as fiche',

			'FROM'		=> array(
				USERS_TABLE		 => 'u',
				GROUPS_TABLE	 => 'g',
				USER_GROUP_TABLE => 'ugm',
			    PROFILE_FIELDS_DATA_TABLE => 'p'
			),

			'WHERE'		=> 'u.user_id = p.user_id AND u.group_id = g.group_id AND u.user_id = ugm.user_id and ugm.group_id = 2 '//GROUPE_ACTIF,
                          ." AND p.pf_avatar is not null AND p.pf_avatar <> ''", 
			'ORDER_BY'	=> "p.pf_avatar ASC, u.username_clean ASC"
		));
				
		$result = $db->sql_query($sql);
		$resultats = array();
	    while ($row = $db->sql_fetchrow($result)){
	        //La clef sert pour le tri
	        $resultats[$row['avatar'].'a'.$row['user_id']]['ID']=$row['user_id'];
	        $resultats[$row['avatar'].'a'.$row['user_id']]['NOM']=$row['prenom'] . ' ' . $row['nom'];
	        $resultats[$row['avatar'].'a'.$row['user_id']]['AVATAR']=$row['avatar'];
	        $resultats[$row['avatar'].'a'.$row['user_id']]['U_RESUME']   =generate_board_url().'/personnages-resumes/'.$row['username_clean'].'-t'. $row['fiche'].'.html';
	        $resultats[$row['avatar'].'a'.$row['user_id']]['USER_COLOUR']=get_username_string('colour', $row['user_id'], $row['nom'], $row['user_colour'], $row['nom']);
	        $resultats[$row['avatar'].'a'.$row['user_id']]['USER_NAME']  =get_username_string('username', $row['user_id'], $row['nom'], $row['user_colour'], $row['nom']);
	    }
	    $db->sql_freeresult($result);
	    return $resultats;
	}
	
	public static function loadContacts($number){
	    global $db, $template;
	    //Initialisation
	    $couleurs['']          = '333333';
	    $couleurs[AT_HUMAIN]   = COULEUR_HUMAIN;
	    $couleurs[AT_NEPHILIM] = COULEUR_NEPHILIM;
	    $couleurs[AT_ORIGINEL] = COULEUR_ORIGINEL;
	    switch ($number){
	        case 4 :
	            $pf_nom     = 'p.pf_cd_nom';
	            $pf_race    = 'p.pf_cd_race';
	            $pf_fiche   = 'p.pf_cd_fiche';
	            $pf_actif   = 'AND p.pf_cd_actif = 1';
	            $pf_avatar  = 'p.pf_cd_avatar_name';
	            $classement = 'e';
	            break;
	        case 3 :
	            $pf_nom   = 'p.pf_cc_nom';
	            $pf_race    = 'p.pf_cc_race';
	            $pf_fiche   = 'p.pf_cc_fiche';
	            $pf_actif = 'AND p.pf_cc_actif = 1';
	            $pf_avatar = 'p.pf_cc_avatar_name';
	            $classement = 'd';
	            break;
	        case 2 :
	            $pf_nom   = 'p.pf_cb_nom';
	            $pf_race    = 'p.pf_cb_race';
	            $pf_fiche   = 'p.pf_cb_fiche';
	            $pf_actif = '';
	            $pf_avatar = 'p.pf_cb_avatar_name';
	            $classement = 'c';
	            break;
	        default :
	            $pf_nom   = 'p.pf_ca_nom';
	            $pf_race    = 'p.pf_ca_race';
	            $pf_fiche   = 'p.pf_ca_fiche';
	            $pf_actif = '';
	            $pf_avatar = 'p.pf_ca_avatar_name';
	            $classement = 'b';
	            break;
	    }
	    
	    //requête SQL
        $sql = $db->sql_build_query('SELECT', array(
    	    'SELECT'	=> "u.user_id, -1 as default_group, $pf_nom as nom, u.username_clean, $pf_race as race,"
    	                  ."$pf_avatar as avatar, $pf_nom as nom, $pf_fiche as fiche",
    	
    	    'FROM'		=> array(
				USERS_TABLE		 => 'u',
				GROUPS_TABLE	 => 'g',
				USER_GROUP_TABLE => 'ugm',
			    PROFILE_FIELDS_DATA_TABLE => 'p'
			),

			'WHERE'		=> 'u.user_id = p.user_id AND u.group_id = g.group_id AND u.user_id = ugm.user_id and ugm.group_id = 2 '//GROUPE_ACTIF,
                          ." AND p.pf_ca_avatar is not null AND p.pf_ca_avatar <> '' " . $pf_actif, 
			
            'ORDER_BY'	=> "p.pf_ca_avatar ASC, u.username_clean ASC"
        ));
	
	    $result = $db->sql_query($sql);
	    $resultats = array();
	    while ($row = $db->sql_fetchrow($result)){
	        if (!empty(trim($row['avatar']))){
    	        //La clef sert pour le tri
    	        $resultats[$row['avatar'].$classement.$row['user_id']]['ID']=$row['user_id'];
    	        $resultats[$row['avatar'].$classement.$row['user_id']]['NOM']=$row['nom'];
    	        $resultats[$row['avatar'].$classement.$row['user_id']]['AVATAR']=$row['avatar'];
    	        $resultats[$row['avatar'].$classement.$row['user_id']]['U_RESUME']   =generate_board_url().'/personnages-resumes/'.$row['username_clean'].'-t'. $row['fiche'].'.html';
    	        $resultats[$row['avatar'].$classement.$row['user_id']]['USER_COLOUR']=get_username_string('colour', $row['user_id'], $row['nom'], $couleurs[$row['race']], $row['nom']);
    	        $resultats[$row['avatar'].$classement.$row['user_id']]['USER_NAME']  =get_username_string('username', $row['user_id'], $row['nom'], $couleurs[$row['race']], $row['nom']);
	        }
	    }
	    $db->sql_freeresult($result);
	    return $resultats;
	
	}
	
	public static function loadFromFile(){
		global $phpbb_root_path,$template;
		//Initialisation
		$couleurs[AT_HUMAIN]   = COULEUR_HUMAIN;
		$couleurs[AT_NEPHILIM] = COULEUR_NEPHILIM;
		$couleurs[AT_ORIGINEL] = COULEUR_ORIGINEL;
		//Chargement du fichier de configuration
		$nomFichier = $phpbb_root_path.'../resources/avatars.csv';
		//Ouverture et lecture du fichier
		$contenu = file_get_contents($nomFichier);
		if (!empty($contenu)){
			$lignes=explode("\n", $contenu);
			unset($contenu);
			$tableau=array();			
			foreach($lignes as $clef => $ligne){
				if (!empty($ligne) && substr(trim($ligne[0]),0,1) != '#' ){
					$new_line = explode(';',$ligne);
					if (count($new_line) >= self::CASES ){
						$tableau[]=$new_line;
					}
				}
			}
			unset($lignes);
			
			//Parcours du fichier
			foreach($tableau as $ligne){
				if (empty($ligne) || substr(trim($ligne[0]),0,1) == '#'||count($ligne) != self::CASES ) continue;
			    $resultats[$ligne[self::AVATAR].'a0']['ID']         = -1;
			    $resultats[$ligne[self::AVATAR].'a0']['NOM']        = $ligne[self::NOM];
			    $resultats[$ligne[self::AVATAR].'a0']['AVATAR']     = $ligne[self::AVATAR];
			    $resultats[$ligne[self::AVATAR].'a0']['U_RESUME']   = generate_board_url().LIEN_PERSONNAGES_IMPORTANTS;
			    $resultats[$ligne[self::AVATAR].'a0']['USER_COLOUR']= get_username_string('colour', -1, $ligne[self::NOM], $couleurs[$ligne[self::COULEUR]], $ligne[self::NOM]);
			    $resultats[$ligne[self::AVATAR].'a0']['USER_NAME']  = get_username_string('username', -1, $ligne[self::NOM], $couleurs[$ligne[self::COULEUR]], $ligne[self::NOM]);
			}
		}
		return $resultats;
	}
}
?>
