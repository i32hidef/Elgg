<?php
/**
 * Group blog module
 */

$group = elgg_get_page_owner_entity();

if ($group->blog_enable == "no") {
	return true;
}

$all_link = elgg_view('output/url', array(
	'href' => "blog/group/$group->guid/all",
	'text' => elgg_echo('link:view:all'),
));

elgg_push_context('widgets');
$options = array(
	'type' => 'object',
	'subtype' => 'blog',
	'container_guid' => elgg_get_page_owner_guid(),
	'limit' => 6,
	'full_view' => false,
	'pagination' => false,
);
//$content = elgg_list_entities($options);
$entities = elgg_get_entities($options);

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

$content = elgg_view_entity_list($list,$options);

elgg_pop_context();

if (!$content) {
	$content = '<p>' . elgg_echo('blog:none') . '</p>';
}

$new_link = elgg_view('output/url', array(
	'href' => "blog/add/$group->guid",
	'text' => elgg_echo('blog:write'),
));

echo elgg_view('groups/profile/module', array(
	'title' => elgg_echo('blog:group'),
	'content' => $content,
	'all_link' => $all_link,
	'add_link' => $new_link,
));

