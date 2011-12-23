<?php
/**
 * Blog river view.
 */

$object = $vars['item']->getObjectEntity();
$excerpt = strip_tags($object->excerpt);
$excerpt = elgg_get_excerpt($excerpt);
$excerpt .= "to " . $object->language;
echo elgg_view('river/item', array(
	'item' => $vars['item'],
	'message' => $excerpt,
));
