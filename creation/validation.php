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
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../jeuderole/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
include($phpbb_root_path . 'includes/functions_profile_fields.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include($phpbb_root_path . 'includes/functions_convert.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_creation.' . $phpEx);
include($phpbb_root_path . 'includes/mods/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/mcp/mcp_post.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup(array('memberlist', 'groups','mods/validate'));

// Grab data
$action		  = request_var('action', 'viewprofile');
$user_id	  = request_var('u', ANONYMOUS);
$message      = request_var('message',0);

//Contrôle des actions
if (!in_array($action, array('viewprofile', 'validate', 'invalidate', 'devalidate', 'revalidate'))){
	trigger_error('NO_ACTION');
}

//Contrôle des messages
if (!in_array($message, array(0,1))){
	trigger_error('NO_ACTION');
}

//Anonymous ?
if(ANONYMOUS == is_user_anonymous($user->data['user_id'])){
	login_box();
}
//Contrôle des droits
if(! is_user_in_group( GROUPE_ADMINISTRATEUR ) ){
	trigger_error('NO_AUTH_OPERATION',E_USER_WARNING);
}

//Vérification de l'existence de l'utilisateur dans la base de données
$sql = 'SELECT *
	FROM ' . USERS_TABLE . '
	WHERE user_id = '.$user_id;
$result = $db->sql_query($sql);
$member = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

//Le membre n'existe pas où est ignoré
if (!$member || $member['user_type'] == USER_IGNORE || $member['user_type'] == USER_INACTIVE && $member['user_inactive_reason'] != INACTIVE_PROFILE){
    trigger_error('NO_USER');
}

$user_id = (int) $member['user_id'];

//Le membre n'est pas en demande de création et on veut le valider
if ('validate' == $action && !is_user_in_group(GROUPE_DEMANDE_CREATION,$user_id)){
	trigger_error($user->lang['VALIDATION_IMPOSSIBLE']);
	die();
}
//Le membre n'est pas actif, n'est pas inactif et n'est pas en attente de validation et on veut voir son profil
if ('viewprofile' == $action && !is_user_in_group(GROUPE_DEMANDE_CREATION,$user_id) && !is_user_in_group(GROUPE_ACTIF,$user_id) && !is_user_in_group(GROUPE_INACTIF,$user_id)){
    trigger_error($user->lang['CONSULTATION_IMPOSSIBLE']);
    die();
}
//Le membre n'est pas inactif et on veut le revalider
if ('revalidate' == $action && !is_user_in_group(GROUPE_INACTIF,$user_id)){
    trigger_error($user->lang['REVALIDATION_IMPOSSIBLE']);
    die();
}
//Le membre n'est pas actif et on veut le devalider
if ('devalidate' == $action && !is_user_in_group(GROUPE_ACTIF,$user_id)){
    trigger_error($user->lang['DEVALIDATION_IMPOSSIBLE']);
    die();
}


//Traitement des actions
switch ($action)
{
    case 'revalidate':
		//Chargement des valeurs de profil
		$user->get_profile_fields($member['user_id']);
        //placer dans le groupe "race" par défaut
		//Passage dans le groupe humain ou nephilim
		switch ($user->profile_fields['pf_race']) {
			case AT_NEPHILIM:
				group_user_attributes('default',GROUPE_NEPHILIM,array($member['user_id']),array($member['username']));
				break;
			case AT_HUMAIN:
				group_user_attributes('default',GROUPE_HUMAIN,array($member['user_id']),array($member['username']));
				break;
		}
		//retirer du groupe inactif
        group_user_del(GROUPE_INACTIF ,array($member['user_id']),array($member['username']));
        //placer dans le groupe actif
        group_user_add(GROUPE_ACTIF ,array($member['user_id']),array($member['username']));
        //déverrouiller les x sujets dans le forum résumés
        $sql = 'UPDATE ' . TOPICS_TABLE . ' set topic_status = 0 WHERE ';
        //Chargement des valeurs de profil
        $user->get_profile_fields($member['user_id']);
        //Création du tableau de modification
        $id = array(
            (int) $user->profile_fields['pf_fiche'],
            (int) $user->profile_fields['pf_ca_fiche'],
            (int) $user->profile_fields['pf_cb_fiche'],
            (int) $user->profile_fields['pf_cc_fiche'],
            (int) $user->profile_fields['pf_cd_fiche'],
            (int) $user->profile_fields['pf_sujet_lien'],
            (int) $user->profile_fields['pf_sujet_resume'],
            (int) $user->profile_fields['pf_telephone'],
            (int) $user->profile_fields['pf_ca_telephone'],
            (int) $user->profile_fields['pf_cb_telephone'],
            (int) $user->profile_fields['pf_cc_telephone'],
            (int) $user->profile_fields['pf_cd_telephone'],
        );
        $sql = $sql . $db->sql_in_set('topic_id',$id);
        $result = $db->sql_query($sql);
        //Message d'information
        $url = append_sid("{$phpbb_root_path}viewtopic.$phpEx", "t=".$user->profile_fields['pf_fiche']);
        $message   = $user->lang['DEVALIDATED'];
        $message  .= '<br/><br/>';
        $meta_info = append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=group&amp;g=".GROUPE_ACTIF);
        $message  .=  '<a href="' . $meta_info . '">'.$user->lang['ACTIF_MEMBERLIST'] .'</a>';
        $message  .= '<br/><br/>';
        $message  .=  '<a href="' . $url . '">'.$user->lang['GOTO_FICHE'] .'</a>';
        trigger_error($message);
        die();
        break;
    case 'devalidate':
        //placer dans le groupe inactif par défaut
        group_user_add(GROUPE_INACTIF ,array($member['user_id']),array($member['username']),false,true);
        //retirer du groupe actif
        group_user_del(GROUPE_ACTIF ,array($member['user_id']),array($member['username']));
        //verrouiller les x sujets dans le forum résumés
        $sql = 'UPDATE ' . TOPICS_TABLE . ' set topic_status = 1 WHERE ';
        //Chargement des valeurs de profil
        $user->get_profile_fields($member['user_id']);
        //Création du tableau de modification
        $id = array(
            (int) $user->profile_fields['pf_fiche'],
            (int) $user->profile_fields['pf_ca_fiche'],
            (int) $user->profile_fields['pf_cb_fiche'],
            (int) $user->profile_fields['pf_cc_fiche'],
            (int) $user->profile_fields['pf_cd_fiche'],
            (int) $user->profile_fields['pf_sujet_lien'],
            (int) $user->profile_fields['pf_sujet_resume'],            
            (int) $user->profile_fields['pf_telephone'],
            (int) $user->profile_fields['pf_ca_telephone'],
            (int) $user->profile_fields['pf_cb_telephone'],
            (int) $user->profile_fields['pf_cc_telephone'],
            (int) $user->profile_fields['pf_cd_telephone'],
        );        
        $sql = $sql . $db->sql_in_set('topic_id',$id);
        $result = $db->sql_query($sql);
        unset($sql,$id);
        //On procède à la dévalidation
        $cp_data['pf_actif'] = 0;
        //Enregistrement
        $cp = new custom_profile();
        $cp->update_profile_field_data($user->data['user_id'], $cp_data);
        //Message d'information
        $url = append_sid("{$phpbb_root_path}viewtopic.$phpEx", "t=".$user->profile_fields['pf_fiche']);
		$message   = $user->lang['DEVALIDATED'];
		$message  .= '<br/><br/>';
		$meta_info = append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=group&amp;g=".GROUPE_INACTIF);
		$message  .=  '<a href="' . $meta_info . '">'.$user->lang['INACTIF_MEMBERLIST'] .'</a>';
		$message  .= '<br/><br/>';
		$message  .=  '<a href="' . $url . '">'.$user->lang['GOTO_FICHE'] .'</a>';
		trigger_error($message);
		die();
    	break;
        
	case 'invalidate':
	    //REFUS DE LA VALIDATION
	    //Cette astuce permet d'ajouter l'utilisateur à un groupe par défaut sans avatar pour ne pas supprimer le sien.
		group_user_attributes('default',GROUPE_SIGNATURE ,array($member['user_id']),array($member['username']));
		//On retire l'utilisateur du groupe de demande de validation
		group_user_del(GROUPE_DEMANDE_CREATION ,array($member['user_id']),array($member['username']));
		//On redirige vers la page de création de MP
		header('Location: '.append_sid("{$phpbb_root_path}ucp.$phpEx",array('i' => 'pm', 'mode' => 'compose', 'u' => $member['user_id']),false));
		die();
	case 'validate':
		//On procède à la validation
		//Chargement des valeurs de profil
		$user->get_profile_fields($member['user_id']);
		//Passage dans le groupe humain ou nephilim
		switch ($user->profile_fields['pf_race']) {
			case AT_NEPHILIM:
				group_user_add(GROUPE_NEPHILIM,array($member['user_id']),array($member['username']),false,true);
				break;
			case AT_HUMAIN:
				group_user_add(GROUPE_HUMAIN,array($member['user_id']),array($member['username']),false,true);
				break;
			default:
				trigger_error('NO_RACE');
			    break;
		}
		//Passage dans le groupe actif
		group_user_del(GROUPE_DEMANDE_CREATION ,array($member['user_id']),array($member['username']));
		group_user_add(GROUPE_ACTIF,array($member['user_id']),array($member['username']));
		//Passage dans le groupe du clan correspondant
		switch ($user->profile_fields['pf_clan']) {
			case AT_ASMODEEN:
			case AT_INFILTRE:
			case AT_INSOUMIS:
			case AT_IZANAGHI:
			case AT_VESTAL:
			case AT_SKJALDMEYJAR:
			    group_user_add($user->profile_fields['pf_clan'],array($member['user_id']),array($member['username']));
				break;			
			default:
				group_user_add(AT_SANSCLAN,array($member['user_id']),array($member['username']));
				break;
		}
		//Préparation des sujets postés automatiquement :
		//Initialisation du bbcode de caractère
		//$poll = $uid = $bitfield = $options = '';
		//$allow_bbcode = $allow_urls = $allow_smilies = false;
		//generate_text_for_storage($resume_court, $uid, $bitfield, $flags, $allow_bbcode = false, $allow_urls = false, $allow_smilies = false);
		//generate_text_for_storage($resume_long, $uid, $bitfield, $flags, $allow_bbcode = false, $allow_urls = false, $allow_smilies = false);
		$mode       = 'post';
		$username   = $member['username'];
		$subject    = $user->profile_fields['pf_prenom'] . ' ' . $user->profile_fields['pf_nom'];
		$topic_type = POST_NORMAL;
		$poll       = null;
		
		// New Topic 
		$data = array(
		    // General Posting Settings
		    'forum_id'  => FORUM_RESUME_PERSONNAGES,    // The forum ID in which the post will be placed. (int)
		    'topic_id'  => FORUM_NEW_TOPIC,             // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
		    'icon_id'   => false,                       // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)
		    'contact_id'=> 0,  //The ID of the contact 0 or 1 to 4
		
		    // Defining Post Options
		    'enable_bbcode'  => true,    // Enable BBcode in this post. (bool)
		    'enable_smilies' => true,    // Enabe smilies in this post. (bool)
		    'enable_urls'    => true,    // Enable self-parsing URL links in this post. (bool)
		    'enable_sig'     => true,    // Enable the signature of the poster to be displayed in the post. (bool)
		
		    // Message Body
		    'message'        => $user->profile_fields['pf_resume'],       // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
		    'message_md5'    => md5($user->profile_fields['pf_resume']),  // The md5 hash of your message
		
		    // Values from generate_text_for_storage()
		    'bbcode_bitfield' => $user->profile_fields['pf_resume_bit'],    // Value created from the generate_text_for_storage() function.
		    'bbcode_uid'      => $user->profile_fields['pf_resume_uid'],        // Value created from the generate_text_for_storage() function.
		
		    // Other Options
		    'post_edit_locked'   => 1,   // Disallow post editing? 1 = Yes, 0 = No
		    'topic_title'        => truncate_string($subject),  // Subject/Title of the topic. (string)
		
		    // Email Notification Settings
		    'notify_set'        => false, // (bool)
		    'notify'            => false, // (bool)
		    'post_time'         => 0,        // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
		    'forum_name'        => '',        // For identifying the name of the forum in a notification email. (string)
		
		    // Indexing
		    'enable_indexing'   => true,        // Allow indexing the post? (bool)
		
		    // 3.0.6
		    'force_approved_state' => true, // Allow the post to be submitted without going into unapproved queue
		
		    // 3.1-dev, overwrites force_approve_state
		    'force_visibility'     => true, // Allow the post to be submitted without going into unapproved queue, or make it be deleted
		);
		
		$url  = submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
		
		//Préparation pour le changement de poster
		$post_info['user_id']=$user->data['user_id'];
		$post_info['topic_id'] = $data['topic_id'];
		$post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
		$post_info['post_postcount'] = $post_info['post_approved'] = true;
		$post_info['post_attachment'] = false;
		$post_info['forum_id'] = FORUM_RESUME_PERSONNAGES;
		$userdata['user_id']=$user_id;
		$userdata['username']=$member['username'];

		//Changement de poster
		change_poster($post_info, $userdata);
		$cp_data['pf_fiche'] = $data['topic_id']; 
		
		/* ***********************************************CONTACT 1************************************* */
		//On ne modifie que les champs à modifier
		$subject = $user->profile_fields['pf_ca_nom'] . ' Contact de ' . $member['username'];
		$data['post_id']     = null;
		$data['topic_id']    = FORUM_NEW_TOPIC;
		$data['contact_id']     = 1;
		$data['message']     = $user->profile_fields['pf_ca_resume'];
		$data['message_md5'] = md5($user->profile_fields['pf_ca_resume']);
		$data['bbcode_bitfield'] = $user->profile_fields['pf_ca_bit'];
		$data['bbcode_uid']  = $user->profile_fields['pf_ca_uid'];
		$data['topic_title'] = truncate_string($subject);
		//PREPARATION POUR LES LIENS
		$contacts[1]['NOM']  = $user->profile_fields['pf_ca_nom'];
		$contacts[1]['CLAN'] = get_clan($user->profile_fields['pf_ca_clan'],$user->profile_fields['pf_ca_sexe']);
		$contacts[1]['RACE'] = get_race($user->profile_fields['pf_ca_race'],$user->profile_fields['pf_ca_sexe']);
		//ENVOI DU MESSAGE
		submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
		$cp_data['pf_ca_fiche'] = $data['topic_id'];
		//Préparation pour le changement de posteur
		$post_info['user_id']  = $user->data['user_id'];
		$post_info['topic_id'] = $data['topic_id'];
		$post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
		//Changement de posteur
		change_poster($post_info, $userdata);
		/* ***********************************************CONTACT 2************************************* */
		//On ne modifie que les champs à modifier
		$subject = $user->profile_fields['pf_cb_nom'] . ' Contact de ' . $member['username'];
		$data['post_id']     = null;
		$data['topic_id']    = FORUM_NEW_TOPIC;
		$data['contact_id']     = 2;
		$data['message']     = $user->profile_fields['pf_cb_resume'];
		$data['message_md5'] = md5($user->profile_fields['pf_cb_resume']);
		$data['bbcode_bitfield'] = $user->profile_fields['pf_cb_bit'];
		$data['bbcode_uid']  = $user->profile_fields['pf_cb_uid'];
		$data['topic_title'] = truncate_string($subject);
		//PREPARATION POUR LES LIENS
		$contacts[2]['NOM']  = $user->profile_fields['pf_cb_nom'];
		$contacts[2]['CLAN'] = get_clan($user->profile_fields['pf_cb_clan'],$user->profile_fields['pf_cb_sexe']);
		$contacts[2]['RACE'] = get_race($user->profile_fields['pf_cb_race'],$user->profile_fields['pf_cb_sexe']);
	   //ENVOI DU MESSAGE
		submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
		$cp_data['pf_cb_fiche'] = $data['topic_id'];
		//Préparation pour le changement de posteur
		$post_info['user_id']  = $user->data['user_id'];
		$post_info['topic_id'] = $data['topic_id'];
		$post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
		//Changement de posteur
		change_poster($post_info, $userdata);
		/* ***********************************************CONTACT 3************************************* */
		//On ne modifie que les champs à modifier
		if (AT_ACTIF == $user->profile_fields['pf_cc_actif']){
    		$subject = $user->profile_fields['pf_cc_nom'] . ' Contact de ' . $member['username'];
    		$data['post_id']     = null;
    		$data['topic_id']    = FORUM_NEW_TOPIC;
    		$data['contact_id']     = 3;
    		$data['message']     = $user->profile_fields['pf_cc_resume'];
    		$data['message_md5'] = md5($user->profile_fields['pf_cc_resume']);
    		$data['bbcode_bitfield'] = $user->profile_fields['pf_cc_bit'];
    		$data['bbcode_uid']  = $user->profile_fields['pf_cc_uid'];
    		$data['topic_title'] = truncate_string($subject);
    		//PREPARATION POUR LES LIENS
    		$contacts[3]['NOM']  = $user->profile_fields['pf_cc_nom'];
    		$contacts[3]['CLAN'] = get_clan($user->profile_fields['pf_cc_clan'],$user->profile_fields['pf_cc_sexe']);
    		$contacts[3]['RACE'] = get_race($user->profile_fields['pf_cc_race'],$user->profile_fields['pf_cc_sexe']);
    		//ENVOI DU MESSAGE
    		submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
    		$cp_data['pf_cc_fiche'] = $data['topic_id'];
    		//Préparation pour le changement de posteur
    		$post_info['user_id']  = $user->data['user_id'];
    		$post_info['topic_id'] = $data['topic_id'];
    		$post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
    		//Changement de posteur
    		change_poster($post_info, $userdata);
        }
		/* ***********************************************CONTACT 4************************************* */
		//On ne modifie que les champs à modifier
		if (AT_ACTIF == $user->profile_fields['pf_cd_actif']){
    		$subject = $user->profile_fields['pf_cd_nom'] . ' Contact de ' . $member['username'];
    		$data['post_id']     = null;
    		$data['topic_id']    = FORUM_NEW_TOPIC;
    		$data['contact_id']     = 4;
    		$data['message']     = $user->profile_fields['pf_cd_resume'];
    		$data['message_md5'] = md5($user->profile_fields['pf_cd_resume']);
    		$data['bbcode_bitfield'] = $user->profile_fields['pf_cd_bit'];
    		$data['bbcode_uid']  = $user->profile_fields['pf_cd_uid'];
    		$data['topic_title'] = truncate_string($subject);
    		//PREPARATION POUR LES LIENS
    		$contacts[4]['NOM']  = $user->profile_fields['pf_cd_nom'];
    		$contacts[4]['CLAN'] = get_clan($user->profile_fields['pf_cd_clan'],$user->profile_fields['pf_cd_sexe']);
    		$contacts[4]['RACE'] = get_race($user->profile_fields['pf_cd_race'],$user->profile_fields['pf_cd_sexe']);
    		//ENVOI DU MESSAGE
    		submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
    		$cp_data['pf_cd_fiche'] = $data['topic_id'];
    		//Préparation pour le changement de posteur
    		$post_info['user_id']  = $user->data['user_id'];
    		$post_info['topic_id'] = $data['topic_id'];
    		$post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
    		//Changement de posteur
    		change_poster($post_info, $userdata);
		}
		$cp_data['pf_actif'] = AT_ACTIF;

		/*******************************************LIENS DU PERSONNAGE**********************************************/
		$subject    = 'Liens et contacts de ' . $member['username'];
		$message_lien = creation_message_lien($member['username'],$contacts);
		// New Topic 
		$data = array(
		    // General Posting Settings
		    'forum_id'  => FORUM_LIENS_PERSONNAGES,    // The forum ID in which the post will be placed. (int)
		    'topic_id'  => FORUM_NEW_TOPIC,             // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
		    'icon_id'   => false,                       // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)
		    'contact_id'=> 0,  //The ID of the contact 0 or 1 to 4
		    'post_edit_locked'  => 0,   // Disallow post editing? 1 = Yes, 0 = No

		    // Defining Post Options
		    'enable_bbcode'  => true,    // Enable BBcode in this post. (bool)
		    'enable_smilies' => true,    // Enabe smilies in this post. (bool)
		    'enable_urls'    => true,    // Enable self-parsing URL links in this post. (bool)
		    'enable_sig'     => true,    // Enable the signature of the poster to be displayed in the post. (bool)
		
		    // Message Body
		    'message'        => $message_lien['message'],       // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
		    'message_md5'    => md5($message_lien['message']),  // The md5 hash of your message
		
		    // Values from generate_text_for_storage()
    		'bbcode_bitfield' => $message_lien['bit'],    // Value created from the generate_text_for_storage() function.
    		'bbcode_uid'      => $message_lien['uid'],    // Value created from the generate_text_for_storage() function.
    		
    		// Other Options
    		'post_edit_locked'   => 1,   // Disallow post editing? 1 = Yes, 0 = No
    		'topic_title'        => truncate_string($subject),  // Subject/Title of the topic. (string)
    		
    		// Email Notification Settings
    		'notify_set'        => false, // (bool)
    		'notify'            => false, // (bool)
    		'post_time'         => 0,        // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
    		'forum_name'        => '',        // For identifying the name of the forum in a notification email. (string)
    		
    		// Indexing
    		'enable_indexing'   => true,        // Allow indexing the post? (bool)
    		
    		// 3.0.6
    		'force_approved_state' => true, // Allow the post to be submitted without going into unapproved queue
    		
    		// 3.1-dev, overwrites force_approve_state
    		'force_visibility'     => true, // Allow the post to be submitted without going into unapproved queue, or make it be deleted
		);
		
		submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
		
		//Préparation pour le changement de poster
		$post_info['user_id']=$user->data['user_id'];
		$post_info['topic_id'] = $data['topic_id'];
		$post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
		$post_info['post_postcount'] = $post_info['post_approved'] = true;
		$post_info['post_attachment'] = false;
		$post_info['forum_id'] = FORUM_LIENS_PERSONNAGES;
		$userdata['user_id']=$user_id;
		$userdata['username']=$member['username'];
		
		//Changement de poster
		change_poster($post_info, $userdata);
		$cp_data['pf_sujet_lien'] = $data['topic_id'];
        /**********************************RESUMES RP **************************************************/		
		$subject        = 'Résumés des RP de ' . $member['username'];
		$message_resume = creation_message_resume($member['username'],$contacts);
		// New Topic
		$data = array(
		    // General Posting Settings
		    'forum_id'  => FORUM_RESUMES_RP,    // The forum ID in which the post will be placed. (int)
		    'topic_id'  => FORUM_NEW_TOPIC,             // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
		    'icon_id'   => false,                       // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)
		    'contact_id'=> 0,  //The ID of the contact 0 or 1 to 4
		    'post_edit_locked'  => 0,   // Disallow post editing? 1 = Yes, 0 = No
		    
		    // Defining Post Options
		    'enable_bbcode'  => true,    // Enable BBcode in this post. (bool)
		    'enable_smilies' => true,    // Enabe smilies in this post. (bool)
		    'enable_urls'    => true,    // Enable self-parsing URL links in this post. (bool)
		    'enable_sig'     => true,    // Enable the signature of the poster to be displayed in the post. (bool)
		
		    // Message Body
		    'message'        => $message_resume['message'],       // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
		    'message_md5'    => md5($message_resume['message']),  // The md5 hash of your message
		
		    // Values from generate_text_for_storage()
		    'bbcode_bitfield' => $message_resume['bit'],    // Value created from the generate_text_for_storage() function.
		    'bbcode_uid'      => $message_resume['uid'],    // Value created from the generate_text_for_storage() function.
		
		    // Other Options
		    'post_edit_locked'   => 1,   // Disallow post editing? 1 = Yes, 0 = No
		    'topic_title'        => truncate_string($subject),  // Subject/Title of the topic. (string)
		
		    // Email Notification Settings
		    'notify_set'        => false, // (bool)
		    'notify'            => false, // (bool)
		    'post_time'         => 0,        // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
		    'forum_name'        => '',        // For identifying the name of the forum in a notification email. (string)
		
		    // Indexing
		    'enable_indexing'   => true,        // Allow indexing the post? (bool)
		
		    // 3.0.6
		    'force_approved_state' => true, // Allow the post to be submitted without going into unapproved queue
		
		    // 3.1-dev, overwrites force_approve_state
		    'force_visibility'     => true, // Allow the post to be submitted without going into unapproved queue, or make it be deleted
		);
		
		submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
		
		//Préparation pour le changement de poster
		$post_info['user_id']=$user->data['user_id'];
		$post_info['topic_id'] = $data['topic_id'];
		$post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
		$post_info['post_postcount'] = $post_info['post_approved'] = true;
		$post_info['post_attachment'] = false;
		$post_info['forum_id'] = FORUM_RESUMES_RP;
		$userdata['user_id']=$user_id;
		$userdata['username']=$member['username'];
		
		//Changement de poster
		change_poster($post_info, $userdata);
		$cp_data['pf_sujet_resume'] = $data['topic_id'];
		
		
		
		/**********************************TELEPHONE PORTABLE RP MEMBRE PRINCIPAL **************************************************/
		$subject           = 'Messagerie de ' . $member['username'];
		$message_telephone = creation_message_telephone($member['username'],$contacts);
		// New Topic
		$data = array(
		    // General Posting Settings
		    'forum_id'  => FORUM_TELEPHONE_PORTABLE,    // The forum ID in which the post will be placed. (int)
		    'topic_id'  => FORUM_NEW_TOPIC,             // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
		    'icon_id'   => false,                       // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)
		    'contact_id'=> 0,  //The ID of the contact 0 or 1 to 4
		    'post_edit_locked'  => 0,   // Disallow post editing? 1 = Yes, 0 = No
		
		    // Defining Post Options
		    'enable_bbcode'  => true,    // Enable BBcode in this post. (bool)
		    'enable_smilies' => true,    // Enabe smilies in this post. (bool)
		    'enable_urls'    => true,    // Enable self-parsing URL links in this post. (bool)
		    'enable_sig'     => true,    // Enable the signature of the poster to be displayed in the post. (bool)
		
		    // Message Body
		    'message'        => $message_telephone['message'],       // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
		    'message_md5'    => md5($message_telephone['message']),  // The md5 hash of your message
		
		    // Values from generate_text_for_storage()
		    'bbcode_bitfield' => $message_telephone['bit'],    // Value created from the generate_text_for_storage() function.
		    'bbcode_uid'      => $message_telephone['uid'],    // Value created from the generate_text_for_storage() function.
		
		    // Other Options
		    'post_edit_locked'   => 1,   // Disallow post editing? 1 = Yes, 0 = No
		    'topic_title'        => truncate_string($subject),  // Subject/Title of the topic. (string)
		
		    // Email Notification Settings
		    'notify_set'        => false, // (bool)
		    'notify'            => false, // (bool)
		    'post_time'         => 0,        // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
		    'forum_name'        => '',        // For identifying the name of the forum in a notification email. (string)
		
		    // Indexing
		    'enable_indexing'   => true,        // Allow indexing the post? (bool)
		
		    // 3.0.6
		    'force_approved_state' => true, // Allow the post to be submitted without going into unapproved queue
		
		    // 3.1-dev, overwrites force_approve_state
		    'force_visibility'     => true, // Allow the post to be submitted without going into unapproved queue, or make it be deleted
		);
		
		submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
		
		//Préparation pour le changement de poster
		$post_info['user_id']=$user->data['user_id'];
		$post_info['topic_id'] = $data['topic_id'];
		$post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
		$post_info['post_postcount'] = $post_info['post_approved'] = true;
		$post_info['post_attachment'] = false;
		$post_info['forum_id'] = FORUM_TELEPHONE_PORTABLE;
		$userdata['user_id']=$user_id;
		$userdata['username']=$member['username'];
		
		//Changement de poster
		change_poster($post_info, $userdata);
		$cp_data['pf_telephone'] = $data['topic_id'];

		//* ***********************************************CONTACT 1************************************* */
		//On ne modifie que les champs à modifier
	    $message_telephone = creation_message_telephone($user->profile_fields['pf_ca_nom'],$contacts);
	    $subject = 'Messagerie de ' . $user->profile_fields['pf_ca_nom'] . ' Contact de ' . $member['username'];
	    $data['post_id']     = null;
	    $data['topic_id']    = FORUM_NEW_TOPIC;
	    $data['contact_id']  = 1;
	    $data['message']     = $message_telephone['message'];
	    $data['message_md5'] = md5($message_telephone['message']);
	    $data['bbcode_bitfield'] = $message_telephone['bit'];
	    $data['bbcode_uid']  = $message_telephone['uid'];
	    $data['topic_title'] = truncate_string($subject);
	    //ENVOI DU MESSAGE
	    submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
	    $cp_data['pf_ca_telephone'] = $data['topic_id'];
	    //Préparation pour le changement de posteur
	    $post_info['user_id']  = $user->data['user_id'];
	    $post_info['topic_id'] = $data['topic_id'];
	    $post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
	    //Changement de posteur
	    change_poster($post_info, $userdata);
		
		//* ***********************************************CONTACT 2************************************* */
		//On ne modifie que les champs à modifier
	    $message_telephone = creation_message_telephone($user->profile_fields['pf_cb_nom'],$contacts);
	    $subject = 'Messagerie de ' . $user->profile_fields['pf_cb_nom'] . ' Contact de ' . $member['username'];
	    $data['post_id']     = null;
	    $data['topic_id']    = FORUM_NEW_TOPIC;
	    $data['contact_id']  = 2;
	    $data['message']     = $message_telephone['message'];
	    $data['message_md5'] = md5($message_telephone['message']);
	    $data['bbcode_bitfield'] = $message_telephone['bit'];
	    $data['bbcode_uid']  = $message_telephone['uid'];
	    $data['topic_title'] = truncate_string($subject);
	    //ENVOI DU MESSAGE
	    submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
	    $cp_data['pf_cb_telephone'] = $data['topic_id'];
	    //Préparation pour le changement de posteur
	    $post_info['user_id']  = $user->data['user_id'];
	    $post_info['topic_id'] = $data['topic_id'];
	    $post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
	    //Changement de posteur
	    change_poster($post_info, $userdata);
		
		//* ***********************************************CONTACT 3************************************* */
		//On ne modifie que les champs à modifier
		if (AT_ACTIF == $user->profile_fields['pf_cc_actif']){
		    $message_telephone = creation_message_telephone($user->profile_fields['pf_cc_nom'],$contacts);
		    $subject = 'Messagerie de ' . $user->profile_fields['pf_cc_nom'] . ' Contact de ' . $member['username'];
		    $data['post_id']     = null;
		    $data['topic_id']    = FORUM_NEW_TOPIC;
		    $data['contact_id']  = 3;
		    $data['message']     = $message_telephone['message'];
		    $data['message_md5'] = md5($message_telephone['message']);
		    $data['bbcode_bitfield'] = $message_telephone['bit'];
		    $data['bbcode_uid']  = $message_telephone['uid'];
		    $data['topic_title'] = truncate_string($subject);
		    //ENVOI DU MESSAGE
		    submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
		    $cp_data['pf_cc_telephone'] = $data['topic_id'];
		    //Préparation pour le changement de posteur
		    $post_info['user_id']  = $user->data['user_id'];
		    $post_info['topic_id'] = $data['topic_id'];
		    $post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
		    //Changement de posteur
		    change_poster($post_info, $userdata);
		}
		
		//* ***********************************************CONTACT 4************************************* */
		//On ne modifie que les champs à modifier
		if (AT_ACTIF == $user->profile_fields['pf_cd_actif']){
		    $message_telephone = creation_message_telephone($user->profile_fields['pf_cd_nom'],$contacts);
		    $subject = 'Messagerie de ' . $user->profile_fields['pf_cd_nom'] . ' Contact de ' . $member['username'];
		    $data['post_id']     = null;
		    $data['topic_id']    = FORUM_NEW_TOPIC;
		    $data['contact_id']     = 4;
		    $data['message']     = $message_telephone['message'];
		    $data['message_md5'] = md5($message_telephone['message']);
		    $data['bbcode_bitfield'] = $message_telephone['bit'];
		    $data['bbcode_uid']  = $message_telephone['uid'];
		    $data['topic_title'] = truncate_string($subject);
		    //ENVOI DU MESSAGE
		    submit_post ( $mode,  $subject,  $username,  $topic_type,  $poll,  $data);
		    $cp_data['pf_cd_telephone'] = $data['topic_id'];
		    //Préparation pour le changement de posteur
		    $post_info['user_id']  = $user->data['user_id'];
		    $post_info['topic_id'] = $data['topic_id'];
		    $post_info['topic_last_post_id'] = $post_info['post_id'] = $post_info['forum_last_post_id'] = $post_info['topic_first_post_id'] = $data['post_id'];
		    //Changement de posteur
		    change_poster($post_info, $userdata);
		}
		
		//Enregistrement
		$cp = new custom_profile();
		$cp->update_profile_field_data($user_id, $cp_data);
		//Retrait du groupe validation
		//group_user_del(GROUPE_DEMANDE_CREATION,array($member['user_id']),array($member['username']));
		
		//Message d'information
		$message   = $user->lang['VALIDATED'];
		$message  .= '<br/><br/>';
		$meta_info = append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=group&amp;g=".GROUPE_DEMANDE_CREATION);
		$message  .=  '<a href="' . $meta_info . '">'.$user->lang['WAITING_MEMBERLIST'] .'</a>';
		$message  .= '<br/><br/>';
		$message  .=  '<a href="' . $url . '">'.$user->lang['GOTO_FICHE'] .'</a>';
		trigger_error($message);
		die();
    	break;
	
	case 'viewprofile':
	default:
		$user->get_profile_fields($user_id);
		//Calcul du nombre de contact
		$nbContact = 2;
		if ($user->profile_fields['pf_cc_actif']){
		    $nbContact++;
		}
		if ($user->profile_fields['pf_cd_actif']){
		    $nbContact++;
		}
		
		$resume          = generate_text_for_display($user->profile_fields['pf_resume'], $user->profile_fields['pf_resume_uid'], $user->profile_fields['pf_resume_bit'], 7);
		$contact1_resume = generate_text_for_display($user->profile_fields['pf_ca_resume'], $user->profile_fields['pf_ca_uid'], $user->profile_fields['pf_ca_bit'], 7);
		$contact2_resume = generate_text_for_display($user->profile_fields['pf_cb_resume'], $user->profile_fields['pf_cb_uid'], $user->profile_fields['pf_cb_bit'], 7);
		$contact3_resume = generate_text_for_display($user->profile_fields['pf_cc_resume'], $user->profile_fields['pf_cc_uid'], $user->profile_fields['pf_cc_bit'], 7);
		$contact4_resume = generate_text_for_display($user->profile_fields['pf_cd_resume'], $user->profile_fields['pf_cd_uid'], $user->profile_fields['pf_cd_bit'], 7);
		
		//Assignation de toutes les valeurs comprises dans PF
		$template->assign_vars(array(
		
		    //Champ booléen
		    'S_CONTACT_3'  =>  $user->profile_fields['pf_cc_actif'],
		    'S_CONTACT_4'  =>  $user->profile_fields['pf_cd_actif'],
		
		    //Sexe et race sont déjà en place
		
		    'PERSONNAGE_AGE'        =>  $user->profile_fields['pf_agereel'],
		    'PERSONNAGE_AVATAR'     =>  get_user_avatar($member['user_avatar'], $member['user_avatar_type'], $member['user_avatar_width'], $member['user_avatar_height'], 'USER_AVATAR', true),
		    'PERSONNAGE_AVATAR_NOM' =>  $user->profile_fields['pf_avatar'],
		    'PERSONNAGE_DON'        =>  $user->profile_fields['pf_don'],
		    'PERSONNAGE_CLAN'       =>  get_clan($user->profile_fields['pf_clan'],$user->profile_fields['pf_sexe']),
		    'S_PERSONNAGE_FEMME'      =>  AT_FEMME == $user->profile_fields['pf_sexe'],
		    'S_PERSONNAGE_HOMME'      =>  AT_HOMME == $user->profile_fields['pf_sexe'],
		
		    'PERSONNAGE_NOM'        =>  $user->profile_fields['pf_nom'],
		    'PERSONNAGE_PASSE'      =>  $user->profile_fields['pf_passe'],
		    'PERSONNAGE_PRENOM'     =>  $user->profile_fields['pf_prenom'],
		    'PERSONNAGE_PROFESSION' =>  $user->profile_fields['pf_profession'],
		    'PERSONNAGE_POUVOIR'    =>  $user->profile_fields['pf_pouvoir'],
		    'PERSONNAGE_RESUME'     =>  $resume,
		
		
		    'VOLEUSE_NOM'           =>  $user->profile_fields['pf_voleuse_nom'],
		    'VOLEUSE_DESCRIPTION'   =>  $user->profile_fields['pf_voleuse_des'],
		    'VOLEUSE_POUVOIR'       =>  $user->profile_fields['pf_voleuse_pouvoir'],
		
		    'NOMBRE_DE_CONTACTS'    =>  $nbContact,
		
		    //Contact 1
		    'CONTACT1_NOM'          =>  $user->profile_fields['pf_ca_nom'],
		    'CONTACT1_AVATAR_NOM'   =>  $user->profile_fields['pf_ca_avatar_name'],
		    'CONTACT1_DESCRIPTION'  =>  $user->profile_fields['pf_ca_description'],
		    'CONTACT1_RESUME'       =>  $contact1_resume,
		    'CONTACT1_AVATAR'       =>  get_contact_avatar(1, $user->profile_fields['pf_ca_avatar'], $user->profile_fields['pf_ca_avatar_type'], $user->profile_fields['pf_ca_avatar_width'], $user->profile_fields['pf_ca_avatar_height']),
		
		    //Contact 2
		    'CONTACT2_NOM'          =>  $user->profile_fields['pf_cb_nom'],
		    'CONTACT2_AVATAR_NOM'   =>  $user->profile_fields['pf_cb_avatar_name'],
		    'CONTACT2_DESCRIPTION'  =>  $user->profile_fields['pf_cb_description'],
		    'CONTACT2_RESUME'       =>  $contact2_resume,
		    'CONTACT2_AVATAR'       =>  get_contact_avatar(2, $user->profile_fields['pf_cb_avatar'], $user->profile_fields['pf_cb_avatar_type'], $user->profile_fields['pf_cb_avatar_width'], $user->profile_fields['pf_cb_avatar_height']),
		
		    //Contact 3
		    'CONTACT3_NOM'          =>  $user->profile_fields['pf_cc_nom'],
		    'CONTACT3_AVATAR_NOM'   =>  $user->profile_fields['pf_cc_avatar_name'],
		    'CONTACT3_DESCRIPTION'  =>  $user->profile_fields['pf_cc_description'],
		    'CONTACT3_RESUME'       =>  $contact3_resume,
		    'CONTACT3_AVATAR'       =>  get_contact_avatar(3, $user->profile_fields['pf_cc_avatar'], $user->profile_fields['pf_cc_avatar_type'], $user->profile_fields['pf_cc_avatar_width'], $user->profile_fields['pf_cc_avatar_height']),
		
		    //Contact 4
		    'CONTACT4_NOM'          =>  $user->profile_fields['pf_cd_nom'],
		    'CONTACT4_AVATAR_NOM'   =>  $user->profile_fields['pf_cd_avatar_name'],
		    'CONTACT4_DESCRIPTION'  =>  $user->profile_fields['pf_cd_description'],
		    'CONTACT4_RESUME'       =>  $contact4_resume,
		    'CONTACT4_AVATAR'       =>  get_contact_avatar(4, $user->profile_fields['pf_cd_avatar'], $user->profile_fields['pf_cd_avatar_type'], $user->profile_fields['pf_cd_avatar_width'], $user->profile_fields['pf_cd_avatar_height']),
		
		));

		/*
		$controles = array(
			'Double-compte' 	=> $user->profile_fields['pf_doublecompte'] == '2'?'Non':(empty($user->profile_fields['pf_doublecompte'])?'Vide':'Oui'),
			'Anonymat'			=> $user->profile_fields['pf_anonymat'] == '2' ?'Non':(empty($user->profile_fields['pf_anonymat'])?'Vide':'Oui'),
			'Compte-Secret'		=> $user->profile_fields['pf_compte_secret'],
			'Compte-principal'	=> $user->profile_fields['pf_compte_principal'],
			'Compte-secondaire'	=> $user->profile_fields['pf_compte_secondaire'],
			'I.P.'				=> $member['user_ip'],
			'Anniversaire'		=> $member['user_birthday'],
			'Dernière visite'	=> $user->format_date($member['user_lastvisit']),
			'Messages'			=> $member['user_posts'],
			'Description'		=> str_word_count(strip_tags(generate_text_for_display($user->profile_fields['pf_description'],$user->profile_fields['pf_description_uid'],$user->profile_fields['pf_description_bit'],7)),0,'0..3') . ' mots',
			'Caractère'			=> str_word_count(strip_tags(generate_text_for_display($user->profile_fields['pf_caractere'],$user->profile_fields['pf_caractere_uid'],$user->profile_fields['pf_caractere_bit'],7)),0,'0..3'). ' mots',
			'Passé'				=> str_word_count(strip_tags(generate_text_for_display($user->profile_fields['pf_passe'],$user->profile_fields['pf_passe_uid'],$user->profile_fields['pf_passe_bit'],7)),0,'0..3'). ' mots',
		);
		foreach ($controles as $titre => $libelle){
			$template->assign_block_vars('verifications', array(
				'TITRE'		=>  $titre,
				'LIBELLE'	=>  $libelle,
			));
		}
		$liens = array(
			'Administrer l\'utilisateur' => append_sid($phpbb_root_path."adm/index.$phpEx",array('i'=>'users','mode'=>'overview','u'=>$user_id),true,$user->session_id),
			'Whois' => append_sid($phpbb_root_path."adm/index.$phpEx",array('i'=>'users','mode'=>'overview','action'=>'whois','user_ip'=>$member['user_ip']),true,$user->session_id),
			'Envoyer un MP' => append_sid($phpbb_root_path."ucp.$phpEx",array('i'=>'pm','mode'=>'compose','u'=>$user_id)),
			'Envoyer un mail' => "mailto:{$member['user_email']}", 
			'Rechercher les messages' => append_sid($phpbb_root_path."search.$phpEx",array('author_id'=>$user_id,'sr'=>'posts')),  
		);
		foreach ($liens as $libelle => $lien){
			$template->assign_block_vars('liens', array(
				'URL'		=>  $lien,
				'LIBELLE'	=>  $libelle,
			));
		}*/
		
		$template->assign_vars(array(
			'U_VALIDATION'		=> append_sid("{$phpbb_root_path}../creation/validation.$phpEx",array('u' => $user_id, 'action' => 'validate')),
			'U_INVALIDATION'    => append_sid("{$phpbb_root_path}../creation/validation.$phpEx",array('u' => $user_id, 'action' => 'invalidate')),
			'S_MESSAGE_ADMIN'	=> $message == 1,
			'S_ACTIF' 	        => false,
			'S_CREATION' 		=> false,
			'S_CREE'			=> is_user_in_group(GROUPE_DEMANDE_CREATION,$user_id),
			'S_INACTIF'			=> false,
			'S_VALIDATION' 		=> true,
			'HIDDEN_FIELDS'		=> build_hidden_fields(array('u' => $user_id, 'action'=>'validate')),
			'SIGNATURE'			=> generate_text_for_display($member['user_sig'],$member['user_sig_bbcode_uid'],$member['user_sig_bbcode_bitfield'],7)
		));
		$page = 'creation/validation.html';
	break;
}
// Output page
page_header($user->lang['VALIDATE_TITRE']);

$template->set_filenames(array(
	'body' => $page,
));

page_footer();


/**
 * Get simple post data
 */
function get_post_data($post_ids, $acl_list = false, $read_tracking = false)
{
    global $db, $auth, $config, $user;

    $rowset = array();

    if (!sizeof($post_ids))
    {
        return array();
    }

    $sql_array = array(
        'SELECT'	=> 'p.*, u.*, t.*, f.*',

        'FROM'		=> array(
            USERS_TABLE		=> 'u',
            POSTS_TABLE		=> 'p',
            TOPICS_TABLE	=> 't',
        ),

        'LEFT_JOIN'	=> array(
            array(
                'FROM'	=> array(FORUMS_TABLE => 'f'),
                'ON'	=> 'f.forum_id = t.forum_id'
            )
        ),

        'WHERE'		=> $db->sql_in_set('p.post_id', $post_ids) . '
			AND u.user_id = p.poster_id
			AND t.topic_id = p.topic_id',
    );

    if ($read_tracking && $config['load_db_lastread'])
    {
        $sql_array['SELECT'] .= ', tt.mark_time, ft.mark_time as forum_mark_time';

        $sql_array['LEFT_JOIN'][] = array(
            'FROM'	=> array(TOPICS_TRACK_TABLE => 'tt'),
            'ON'	=> 'tt.user_id = ' . $user->data['user_id'] . ' AND t.topic_id = tt.topic_id'
        );

        $sql_array['LEFT_JOIN'][] = array(
            'FROM'	=> array(FORUMS_TRACK_TABLE => 'ft'),
            'ON'	=> 'ft.user_id = ' . $user->data['user_id'] . ' AND t.forum_id = ft.forum_id'
        );
    }

    $sql = $db->sql_build_query('SELECT', $sql_array);
    $result = $db->sql_query($sql);
    unset($sql_array);

    while ($row = $db->sql_fetchrow($result))
    {
        if (!$row['forum_id'])
        {
            // Global Announcement?
            $row['forum_id'] = request_var('f', 0);
        }

        if ($acl_list && !$auth->acl_gets($acl_list, $row['forum_id']))
        {
            continue;
        }

        if (!$row['post_approved'] && !$auth->acl_get('m_approve', $row['forum_id']))
        {
            // Moderators without the permission to approve post should at least not see them. ;)
            continue;
        }

        $rowset[$row['post_id']] = $row;
    }
    $db->sql_freeresult($result);

    return $rowset;
}
?>
