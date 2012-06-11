<?php
/**
 * Moderation Notice
 * Copyright 2011 Starpaul20
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Tell MyBB when to run the hooks
$plugins->add_hook("newreply_start", "moderationnotice_newreply");
$plugins->add_hook("newthread_start", "moderationnotice_newthread");
$plugins->add_hook("editpost_end", "moderationnotice_editpost");
$plugins->add_hook("xmlhttp", "moderationnotice_editpost_quick");
$plugins->add_hook("showthread_start", "moderationnotice_newreply_quick");

// The information that shows up on the plugin manager
function moderationnotice_info()
{
	return array(
		"name"				=> "Moderation Notice",
		"description"		=> "Displays a notice to users if posts/threads/attachments are being moderated for them.",
		"website"			=> "http://galaxiesrealm.com/index.php",
		"author"			=> "Starpaul20",
		"authorsite"		=> "http://galaxiesrealm.com/index.php",
		"version"			=> "1.0",
		"guid"				=> "27d85f4d17c8e9788bc0942124ed62a0",
		"compatibility"		=> "16*"
	);
}

// This function runs when the plugin is activated.
function moderationnotice_activate()
{
	global $db;
	$insert_array = array(
		'title'		=> 'global_moderation_notice',
		'template'	=> $db->escape_string('<div class="pm_alert">
<div>{$moderationnotice_text}</div>
</div>'),
		'sid'		=> '-1',
		'version'	=> '',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $insert_array);

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("newreply", "#".preg_quote('{$reply_errors}')."#i", '{$reply_errors}{$moderation_notice}');
	find_replace_templatesets("newthread", "#".preg_quote('{$thread_errors}')."#i", '{$thread_errors}{$moderation_notice}');
	find_replace_templatesets("editpost", "#".preg_quote('{$post_errors}')."#i", '{$post_errors}{$moderation_notice}');
	find_replace_templatesets("xmlhttp_inline_post_editor", "#".preg_quote('text-align: right;">')."#i", 'text-align: right;"><span style="float: left;">{$moderation_notice}</span>');
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('<form')."#i", '{$moderation_notice}<form');
}

// This function runs when the plugin is deactivated.
function moderationnotice_deactivate()
{
	global $db;
	$db->delete_query("templates", "title IN('global_moderation_notice')");

	include MYBB_ROOT."/inc/adminfunctions_templates.php";
	find_replace_templatesets("newreply", "#".preg_quote('{$moderation_notice}')."#i", '', 0);
	find_replace_templatesets("newthread", "#".preg_quote('{$moderation_notice}')."#i", '', 0);
	find_replace_templatesets("editpost", "#".preg_quote('{$moderation_notice}')."#i", '', 0);
	find_replace_templatesets("xmlhttp_inline_post_editor", "#".preg_quote('<span style="float: left;">{$moderation_notice}</span>')."#i", '', 0);
	find_replace_templatesets("showthread_quickreply", "#".preg_quote('{$moderation_notice}')."#i", '', 0);
}

// Moderation notice on New Reply
function moderationnotice_newreply()
{
	global $db, $mybb, $lang, $templates, $forum, $forumpermissions, $moderation_notice;
	$lang->load("moderationnotice");

	if($forum['modposts'] == 1)
	{
		$moderationnotice_text = $lang->moderation_forum_posts;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}

	if($forum['modattachments'] == 1  && $forumpermissions['canpostattachments'] != 0)
	{
		$moderationnotice_text = $lang->moderation_forum_attachments;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}

	if($forum['modposts'] == 1 && $forum['modattachments'] == 1  && $forumpermissions['canpostattachments'] != 0)
	{
		$moderationnotice_text = $lang->moderation_forum_posts_attachments;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}

	if($mybb->user['moderateposts'] == 1)
	{
		$moderationnotice_text = $lang->moderation_user_posts;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}
}

// Moderation notice on New Thread
function moderationnotice_newthread()
{
	global $db, $mybb, $lang, $templates, $forum, $forumpermissions, $moderation_notice;
	$lang->load("moderationnotice");
	
	if($forum['modthreads'] == 1)
	{
		$moderationnotice_text = $lang->moderation_forum_thread;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}

	if($forum['modattachments'] == 1  && $forumpermissions['canpostattachments'] != 0)
	{
		$moderationnotice_text = $lang->moderation_forum_attachments;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}
	
	if($forum['modthreads'] == 1 && $forum['modattachments'] == 1  && $forumpermissions['canpostattachments'] != 0)
	{
		$moderationnotice_text = $lang->moderation_forum_thread_attachments;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}

	if($mybb->user['moderateposts'] == 1)
	{
		$moderationnotice_text = $lang->moderation_user_posts;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}
}

// Moderation notice on Edit Post
function moderationnotice_editpost()
{
	global $db, $mybb, $lang, $templates, $forum, $forumpermissions, $moderation_notice;
	$lang->load("moderationnotice");
	
	if($forum['mod_edit_posts'] == 1)
	{
		$moderationnotice_text = $lang->moderation_forum_edits;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}

	if($forum['modattachments'] == 1  && $forumpermissions['canpostattachments'] != 0)
	{
		$moderationnotice_text = $lang->moderation_forum_attachments;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}
	
	if($forum['mod_edit_posts'] == 1 && $forum['modattachments'] == 1  && $forumpermissions['canpostattachments'] != 0)
	{
		$moderationnotice_text = $lang->moderation_forum_edits_attachments;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}
}

// Moderation notice on Quick Edit
function moderationnotice_editpost_quick()
{
	global $db, $mybb, $lang, $moderation_notice;
	$lang->load("moderationnotice");
	
	$post = get_post($mybb->input['pid']);
	$forum = get_forum($post['fid']);
	
	if($mybb->input['action'] == "edit_post")
	{
		if($forum['mod_edit_posts'] == 1)
		{
			$moderation_notice = $lang->moderation_forum_edits_quick;
		}
	}
}

// Moderation notice above Quick Reply
function moderationnotice_newreply_quick()
{
	global $db, $mybb, $lang, $templates, $forum, $moderation_notice;
	$lang->load("moderationnotice");
	
	if($forum['modposts'] == 1)
	{
		$moderationnotice_text = $lang->moderation_forum_posts;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}
	
	if($mybb->user['moderateposts'] == 1)
	{
		$moderationnotice_text = $lang->moderation_user_posts;
		eval("\$moderation_notice = \"".$templates->get("global_moderation_notice")."\";");
	}
}

?>