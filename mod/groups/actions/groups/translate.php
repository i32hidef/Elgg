<?php
/**
 * Elgg groups plugin translate action.
 *
 * @package ElggGroups
 */

// Load configuration
global $CONFIG;

/**
 * wrapper for recursive array walk decoding
 */
function profile_array_decoder(&$v) {
	$v = html_entity_decode($v, ENT_COMPAT, 'UTF-8');
}

// Get group fields
$input = array();
foreach ($CONFIG->group as $shortname => $valuetype) {
	// another work around for Elgg's encoding problems: #561, #1963
	$input[$shortname] = get_input($shortname);
	if (is_array($input[$shortname])) {
		array_walk_recursive($input[$shortname], 'profile_array_decoder');
	} else {
		$input[$shortname] = html_entity_decode($input[$shortname], ENT_COMPAT, 'UTF-8');
	}

	if ($valuetype == 'tags') {
		$input[$shortname] = string_to_tag_array($input[$shortname]);
	}
}
$input['name'] = get_input('name');
$input['name'] = html_entity_decode($input['name'], ENT_COMPAT, 'UTF-8');

$user = elgg_get_logged_in_user_entity();

$group_guid = (int)get_input('group_guid');
$new_group_flag = $group_guid == 0;

$group_old = new ElggGroup($group_guid); // load if present, if not create a new group
if (($group_guid) && (!$group_old->canEdit())) {
	register_error(elgg_echo("groups:cantedit"));

	forward(REFERER);
}
$input['language'] = get_input('language');
//New group that is the translation
$group = new ElggGroup();


// Assume we can edit or this is a new group
if (sizeof($input) > 0) {
	foreach($input as $shortname => $value) {
		$group->$shortname = $value;
	}
}

// Validate create
if (!$group->name) {
	register_error(elgg_echo("groups:notitle"));

	forward(REFERER);
}


// Set group tool options
if (isset($CONFIG->group_tool_options)) {
	foreach ($CONFIG->group_tool_options as $group_option) {
		$group_option_toggle_name = $group_option->name . "_enable";
		if ($group_option->default_on) {
			$group_option_default_value = 'yes';
		} else {
			$group_option_default_value = 'no';
		}
		$group->$group_option_toggle_name = get_input($group_option_toggle_name, $group_option_default_value);
	}
}

// Group membership - should these be treated with same constants as access permissions?
switch (get_input('membership')) {
	case ACCESS_PUBLIC:
		$group->membership = ACCESS_PUBLIC;
		break;
	default:
		$group->membership = ACCESS_PRIVATE;
}

if ($new_group_flag) {
	$group->access_id = ACCESS_PUBLIC;
}

//Save the translation group
$group->save();
$group->setLanguage($input['language']);
$group_old->addTranslation($group->guid);

// group creator needs to be member of new group and river entry created

add_to_river('river/group/translate', 'translate', $user->guid, $group_old->guid);
//Ver cual es el accessID de los grupos traducidos
//add_to_river('river/group/translate', 'translate', $group_old->guid, $group->guid, '2');

// Invisible group support
if (elgg_get_plugin_setting('hidden_groups', 'groups') == 'yes') {
	$visibility = (int)get_input('vis', '', false);
	if ($visibility != ACCESS_PUBLIC && $visibility != ACCESS_LOGGED_IN) {
		$visibility = $group->group_acl;
	}

	if ($group->access_id != $visibility) {
		$group->access_id = $visibility;
		$group->save();
	}
}

system_message(elgg_echo("groups:saved"));


forward($group->getParent()->getUrl());
