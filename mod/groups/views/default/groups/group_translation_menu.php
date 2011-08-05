<?php
/**
 * All groups listing page navigation
 *
 */

$tabs = array(
	'groups' => array(
		'text' => elgg_echo('groups'),
		'href' => 'groups/translations',
		'priority' => 200,
	),
	'blogs' => array(
		'text' => elgg_echo('groups:blogs'),
		'href' => 'groups/all?filter=popular',
		'priority' => 300,
	),
	'discussion' => array(
		'text' => elgg_echo('discussions'),
		'href' => 'groups/all?filter=discussion',
		'priority' => 400,
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
