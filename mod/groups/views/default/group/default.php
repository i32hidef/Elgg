<?php 
/**
 * Group entity view
 * 
 * @package ElggGroups
 */

$group = $vars['entity'];
$user = elgg_get_logged_in_user_entity();
//var_dump($group->getTranslation($user->language));
//var_dump($user);
error_log("DEFAULT VIEW");
/*if(!$group->isTranslation()){
	error_log("NO ES TRADUCCION");
	error_log("USER LANG " . $user->language);
	var_dump($group->getTranslation($user->language));
	if($translation = $group->getTranslation($user->language)){
		error_log("----Tiene traduccion en idioma");
		$group = $translation;	
		
		$icon = elgg_view_entity_icon($group->getParent(), 'tiny');

		$metadata = elgg_view_menu('entity', array(
			'entity' => $group,
			'handler' => 'groups',
			'sort_by' => 'priority',
			'class' => 'elgg-menu-hz',
		));

		if (elgg_in_context('owner_block') || elgg_in_context('widgets')) {
			$metadata = '';
		}


		if ($vars['full_view']) {
			echo elgg_view("groups/profile/profile_block", $vars);
		} else {
			// brief view

			$params = array(
				'entity' => $group,
				'metadata' => $metadata,
				'subtitle' => $group->briefdescription,
			);
			$params = $params + $vars;
			$list_body = elgg_view('group/elements/summary', $params);

			echo elgg_view_image_block($icon, $list_body);
		}
	}else{*/
		error_log("DEFAULT VIEW");
		//error_log("----No tiene traduccion en idioma");
		$icon = elgg_view_entity_icon($group, 'tiny');

		$metadata = elgg_view_menu('entity', array(
			'entity' => $group,
			'handler' => 'groups',
			'sort_by' => 'priority',
			'class' => 'elgg-menu-hz',
		));

		if (elgg_in_context('owner_block') || elgg_in_context('widgets')) {
			$metadata = '';
		}


		if ($vars['full_view']) {
			echo elgg_view("groups/profile/profile_block", $vars);
		} else {
			// brief view

			$params = array(
				'entity' => $group,
				'metadata' => $metadata,
				'subtitle' => $group->briefdescription,
			);
			$params = $params + $vars;
			$list_body = elgg_view('group/elements/summary', $params);

			echo elgg_view_image_block($icon, $list_body);
		}

		//if($group->getLanguage() == $user->language || !$group->getTranslation($user->language)){	
		//	error_log("eaaa");
		//} 
		//error_log("TRADUCCION!!!");
		//$parent = $group->getParent();
		//var_dump($parent);
	//}
/*}else{
	error_log("FORWARD");
	//var_dump($group);
	//FORWARD
}*/

