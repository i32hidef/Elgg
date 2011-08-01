<?php
/**
 * Groups function library
 */

/**
 * List all groups
 */
function groups_handle_all_page() {

	// all groups doesn't get link to self
	elgg_pop_breadcrumb();
	elgg_push_breadcrumb(elgg_echo('groups'));

	elgg_register_title_button();
	
	$selected_tab = get_input('filter', 'newest');
	$user = elgg_get_logged_in_user_entity();
	error_log("SELECTED TAB " . $seleted_tab);
	switch ($selected_tab) {
		case 'pop':
			error_log("POP");
			$content = elgg_list_entities_from_relationship_count(array(
				'type' => 'group',
				'relationship' => 'member',
				'inverse_relationship' => false,
				'full_view' => false,
			));
			break;
		case 'active':
			error_log("ACTIVE");
			$content = elgg_list_entities(array(
				'type' => 'object',
				'subtype' => 'groupforumtopic',
				'order_by' => 'e.last_action desc',
				'limit' => 40,
				'full_view' => false,
			));
			break;
		case 'newest':
		default:
			error_log("DEFAULT");
			//LOGIC TO: Get every group and if it has translations in the user language get them 
			//In the views we filter if is a translation and then show what we want
			$entities = elgg_get_entities(array(
				'types' => 'group'));
			
			$list = array();
			$i=0;
			foreach($entities as $ent){
				if(!$ent->isTranslation()){
					if(false == ($translation = $ent->getTranslation($user->language))){
						$list[$i] = $ent;
						$i++;
					}else{
						$list[$i] = $translation;
						$i++;
					}
				}
			}
			
			$options = array(
				'type' => 'group',
				'full_view' => FALSE,
				'list_type_toggle' => FALSE,
				'pagination' => TRUE,
        		);
			//var_dump($list);	
			$content = elgg_view_entity_list($list,$options);
			error_log("Default");
			break;
	}

	$filter = elgg_view('groups/group_sort_menu', array('selected' => $selected_tab));
	
	$sidebar = elgg_view('groups/sidebar/find');
	$sidebar .= elgg_view('groups/sidebar/featured');

	$params = array(
		'content' => $content,
		'sidebar' => $sidebar,
		'filter' => $filter,
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page(elgg_echo('groups:all'), $body);
}

function groups_search_page() {
	elgg_push_breadcrumb(elgg_echo('search'));

	$tag = get_input("tag");
	$title = elgg_echo('groups:search:title', array($tag));

	// groups plugin saves tags as "interests" - see groups_fields_setup() in start.php
	$params = array(
		'metadata_name' => 'interests',
		'metadata_value' => $tag,
		'types' => 'group',
		'full_view' => FALSE,
	);
	$content = elgg_list_entities_from_metadata($params);
	if (!$content) {
		$content = elgg_echo('groups:search:none');
	}

	$sidebar = elgg_view('groups/sidebar/find');
	$sidebar .= elgg_view('groups/sidebar/featured');

	$params = array(
		'content' => $content,
		'sidebar' => $sidebar,
		'filter' => false,
		'title' => $title,
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * List owned groups
 */
function groups_handle_owned_page() {

	$page_owner = elgg_get_page_owner_entity();

	$title = elgg_echo('groups:owned');
	elgg_push_breadcrumb($title);

	elgg_register_title_button();

	$content = elgg_list_entities(array(
		'type' => 'group',
		'owner_guid' => elgg_get_page_owner_guid(),
		'full_view' => false,
	));

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * List groups the user is memober of
 */
function groups_handle_mine_page() {

	$page_owner = elgg_get_page_owner_entity();

	$title = elgg_echo('groups:yours');
	elgg_push_breadcrumb($title);

	elgg_register_title_button();

	$content = elgg_list_entities_from_relationship_count(array(
		'type' => 'group',
		'relationship' => 'member',
		'relationship_guid' => elgg_get_page_owner_guid(),
		'inverse_relationship' => false,
		'full_view' => false,
	));

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * Create or edit a group
 *
 * @param string $page
 * @param int $guid
 */
function groups_handle_edit_page($page, $guid = 0) {
	gatekeeper();
	
	if ($page == 'add') {
		elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
		$title = elgg_echo('groups:add');
		elgg_push_breadcrumb($title);
		$content = elgg_view('groups/edit');
	} else {
		$title = elgg_echo("groups:edit");
		$group = get_entity($guid);

		if ($group && $group->canEdit()) {
			elgg_set_page_owner_guid($group->getGUID());
			elgg_push_breadcrumb($group->name, $group->getURL());
			elgg_push_breadcrumb($title);
			$content = elgg_view("groups/edit", array('entity' => $group));
		} else {
			$content = elgg_echo('groups:noaccess');
		}
	}
	
	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * Translate a group
 *
 * @param string $page
 * @param int $guid
 */
function groups_handle_translate_page($page, $guid = 0) {
	gatekeeper();
	
	$title = elgg_echo("groups:translate");
	$group = get_entity($guid);

	if ($group && $group->canEdit()) {
		elgg_set_page_owner_guid($group->getGUID());
		elgg_push_breadcrumb($group->name, $group->getURL());
		elgg_push_breadcrumb($title);
		$content = elgg_view("groups/translate", array('entity' => $group));
	} else {
		$content = elgg_echo('groups:noaccess');
	}
		
	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);
	$body = elgg_view_layout('one_column', $params);

	echo elgg_view_page($title, $body);
}

/**
 * Group invitations for a user
 */
function groups_handle_invitations_page() {
	gatekeeper();

	$user = elgg_get_page_owner_entity();

	$title = elgg_echo('groups:invitations');
	elgg_push_breadcrumb($title);

	// @todo temporary workaround for exts #287.
	$invitations = groups_get_invited_groups($user->getGUID());
	$content = elgg_view('groups/invitationrequests', array('invitations' => $invitations));

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * Group profile page
 *
 * @param int $guid Group entity GUID
 */
function groups_handle_profile_page($guid) {
	elgg_set_page_owner_guid($guid);

	// turn this into a core function
	global $autofeed;
	$autofeed = true;

	$group = get_entity($guid);
	if (!$group) {
		forward('groups/all');
	}
	$user = elgg_get_logged_in_user_entity();
	
	/*error_log("GROUP LANGUAGE " . $group->getLanguage());
	error_log("USER LANGUAGE " . $user->language);
	var_dump($group->isTranslation());
	var_dump("Tiene traducción en el idioma del user?");
	var_dump($group->getTranslation($user->language));
	var_dump("Tiene traducciones?");
	var_dump($group->hasTranslations());*/
	//We will show only groups that are no translations
	if(!$group->isTranslation){
		error_log("NO ES TRADUCCION");
		//If the language is the same as the user language o dont have translations in the user language
		if(false == $group->getTranslation($user->language)){
			error_log("NO TIENE TRADUCCIONES EN EL IDIOMA");
			elgg_push_breadcrumb($group->name);
			
			$content = elgg_view('groups/profile/layout', array('entity' => $group));
			if (group_gatekeeper(false)) {
				$sidebar = elgg_view('groups/sidebar/members', array('entity' => $group));
			} else {
				$sidebar = '';
			}

			groups_register_profile_buttons($group);
			$params = array(
				'content' => $content,
				'sidebar' => $sidebar,
				'title' => $group->name,
				'filter' => '',
			);
			$body = elgg_view_layout('content', $params);

			echo elgg_view_page($group->name, $body);

		}else{
			error_log("TIENE TRADUCCIONES EN EL IDIOMA");
			$translation = $group->getTranslation($user->language);
			elgg_push_breadcrumb($translation->name);
			//In this case has to exists a translation in the language of the user	
			
			$content = elgg_view('groups/profile/layout', array('entity' => $translation));
			if (group_gatekeeper(false)) {
				$sidebar = elgg_view('groups/sidebar/members', array('entity' => $group));
			} else {
				$sidebar = '';
			}
			groups_register_profile_buttons($group);
			$params = array(
				'content' => $content,
				'sidebar' => $sidebar,
				'title' => $translation->name,
				'filter' => '',
			);
			$body = elgg_view_layout('content', $params);

			echo elgg_view_page($translation->name, $body);
		}
	}
}

/**
 * Group activity page
 *
 * @param int $guid Group entity GUID
 */
function groups_handle_activity_page($guid) {

	elgg_set_page_owner_guid($guid);

	$group = get_entity($guid);
	if (!$group || !elgg_instanceof($group, 'group')) {
		forward();
	}

	group_gatekeeper();

	$title = elgg_echo('groups:activity');

	elgg_push_breadcrumb($group->name, $group->getURL());
	elgg_push_breadcrumb($title);

	$db_prefix = elgg_get_config('dbprefix');

	$content = elgg_list_river(array(
		'joins' => array("JOIN {$db_prefix}entities e ON e.guid = rv.object_guid"),
		'wheres' => array("e.container_guid = $guid")
	));
	if (!$content) {
		$content = '<p>' . elgg_echo('groups:activity:none') . '</p>';
	}
	
	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * Group members page
 *
 * @param int $guid Group entity GUID
 */
function groups_handle_members_page($guid) {

	elgg_set_page_owner_guid($guid);

	$group = get_entity($guid);
	if (!$group || !elgg_instanceof($group, 'group')) {
		forward();
	}

	group_gatekeeper();

	$title = elgg_echo('groups:members:title', array($group->name));

	elgg_push_breadcrumb($group->name, $group->getURL());
	elgg_push_breadcrumb(elgg_echo('groups:members'));

	$content = elgg_list_entities_from_relationship(array(
		'relationship' => 'member',
		'relationship_guid' => $group->guid,
		'inverse_relationship' => true,
		'types' => 'user',
		'limit' => 20,
	));

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * Invite users to a group
 *
 * @param int $guid Group entity GUID
 */
function groups_handle_invite_page($guid) {
	gatekeeper();

	elgg_set_page_owner_guid($guid);

	$group = get_entity($guid);

	$title = elgg_echo('groups:invite:title');

	elgg_push_breadcrumb($group->name, $group->getURL());
	elgg_push_breadcrumb(elgg_echo('groups:invite'));

	if ($group && $group->canEdit()) {
		$content = elgg_view_form('groups/invite', array(
			'id' => 'invite_to_group',
			'class' => 'elgg-form-alt mtm',
		), array(
			'entity' => $group,
		));
	} else {
		$content .= elgg_echo('groups:noaccess');
	}

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * Manage requests to join a group
 * 
 * @param int $guid Group entity GUID
 */
function groups_handle_requests_page($guid) {

	gatekeeper();

	elgg_set_page_owner_guid($guid);

	$group = get_entity($guid);

	$title = elgg_echo('groups:membershiprequests');

	if ($group && $group->canEdit()) {
		elgg_push_breadcrumb($group->name, $group->getURL());
		elgg_push_breadcrumb($title);
		
		$requests = elgg_get_entities_from_relationship(array(
			'relationship' => 'membership_request',
			'relationship_guid' => $guid,
			'inverse_relationship' => true,
		));
		$content = elgg_view('groups/membershiprequests', array(
			'requests' => $requests,
			'entity' => $group,
		));

	} else {
		$content = elgg_echo("groups:noaccess");
	}

	$params = array(
		'content' => $content,
		'title' => $title,
		'filter' => '',
	);
	$body = elgg_view_layout('content', $params);

	echo elgg_view_page($title, $body);
}

/**
 * Registers the buttons for title area of the group profile page
 *
 * @param ElggGroup $group
 */
function groups_register_profile_buttons($group) {

	$actions = array();
	$user = elgg_get_logged_in_user_entity();

	// group owners
	if ($group->canEdit()) {
		// edit and invite
		if(false == ($translation = $group->getTranslation($user->language))){
			$url = elgg_get_site_url() . "groups/edit/{$group->getGUID()}";
			$actions[$url] = elgg_echo('groups:edit');
		}else{
			$url = elgg_get_site_url() . "groups/edit/{$translation->getGUID()}";
			$actions[$url] = elgg_echo('groups:edit');
		}
		
		$url = elgg_get_site_url() . "groups/invite/{$group->getGUID()}";
		$actions[$url] = elgg_echo('groups:invite');
	}

	// group members
	if ($group->isMember($user)) {
		// leave
		$url = elgg_get_site_url() . "action/groups/leave?group_guid={$group->getGUID()}";
		$url = elgg_add_action_tokens_to_url($url);
		$actions[$url] = 'groups:leave';
		//Translate
                $url = elgg_get_site_url() . "groups/translate/{$group->getGUID()}";
                //$url = elgg_add_action_tokens_to_url($url);
                $actions[$url] = 'groups:translate';

	} else {
		// join - admins can always join.
		$url = elgg_get_site_url() . "action/groups/join?group_guid={$group->getGUID()}";
		$url = elgg_add_action_tokens_to_url($url);
		if ($group->isPublicMembership() || $group->canEdit()) {
			$actions[$url] = 'groups:join';
		} else {
			// request membership
			$actions[$url] = 'groups:joinrequest';
		}
	}

	if ($actions) {
		foreach ($actions as $url => $text) {
			elgg_register_menu_item('title', array(
				'name' => $text,
				'href' => $url,
				'text' => elgg_echo($text),
				'link_class' => 'elgg-button elgg-button-action',
			));
		}
	}
}
