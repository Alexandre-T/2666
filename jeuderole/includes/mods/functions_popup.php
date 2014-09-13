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

function get_texts_for_popup ($post_list){
	global $db;
	$bbcode_bitfield='';
	
	
	$sql = $db->sql_build_query('SELECT', array(
			'SELECT'	=> 'p.*',
			'FROM'		=> array(
					POSTS_TABLE		=> 'p',
			),
			'WHERE'		=> $db->sql_in_set('p.post_id', $post_list)
	));
	$result = $db->sql_query($sql);
	$messages = array();
	while ($row = $db->sql_fetchrow($result))
	{
		// Parse the message and subject
		$message = censor_text($row['post_text']);
		
		$bbcode_bitfield = $bbcode_bitfield | base64_decode($row['bbcode_bitfield']);
		$bbcode = new bbcode(base64_encode($bbcode_bitfield));
		// Second parse bbcode here
		if ($row['bbcode_bitfield'])
		{
			$bbcode->bbcode_second_pass($message, $row['bbcode_uid'], $row['bbcode_bitfield']);
		}
		
		$message = bbcode_nl2br($message);
		$message = smiley_text($message);
		
		//@FIXME attachments
		//if (!empty($attachments[$row['post_id']]))
		//{
		//	parse_attachments($forum_id, $message, $attachments[$row['post_id']], $update_count);
		//}
		$messages[$row['post_id']]=$message;
	}
	return $messages;
}
