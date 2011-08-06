<?php
/**
 * All groups listing page navigation
 *
 */
$tabs = array(
	'groups' => array(
		'text' => elgg_echo('groups'),
		'href' => "groups/translations/{$vars['group']}?filter=groups",
		'priority' => 200,
	),
	'blogs' => array(
		'text' => elgg_echo('groups:blogs'),
		'href' => "groups/translations/{$vars['group']}?filter=blogs",
		'priority' => 300,
	),
	'discussion' => array(
		'text' => elgg_echo('discussions'),
		'href' => "groups/translations/{$vars['group']}?filter=discussions",
		'priority' => 400,
	),	
	'Notranslated' => array(
		'text' => elgg_echo('No translated yet'),
		'href' => "groups/translations/{$vars['group']}?filter=notranslated",
		'priority' => 500,
	),
);

// sets default selected item
if (strpos(full_url(), 'filter') === false) {
	$tabs['newest']['selected'] = true;
}

foreach ($tabs as $name => $tab) {
	$tab['name'] = $name;

	elgg_register_menu_item('filter', $tab);
}

echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz'));
