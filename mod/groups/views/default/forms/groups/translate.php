<?php
/**
 * Group translate form
 * 
 * @package ElggGroups
 */

// new groups default to open membership
if (isset($vars['entity'])) {
	$membership = $vars['entity']->membership;
	$access = $vars['entity']->access_id;
	if ($access != ACCESS_PUBLIC && $access != ACCESS_LOGGED_IN) {
		// group only - this is done to handle access not created when group is created
		$access = ACCESS_PRIVATE;
	}
} else {
	$membership = ACCESS_PUBLIC;
	$access = ACCESS_PUBLIC;
}

?>
<div class="elgg-col-1of2">
<div>
	<label><?php echo elgg_echo("groups:name"); ?></label><br />
	<?php echo elgg_view("input/text", array(
		'name' => 'name',
		'value' => $vars['entity']->name,
	));
	?>
</div>
<?php

$group_profile_fields = elgg_get_config('group');
if ($group_profile_fields > 0) {
	foreach ($group_profile_fields as $shortname => $valtype) {
		$line_break = '<br />';
		if ($valtype == 'longtext') {
			$line_break = '';
		}
		echo '<div><label>';
		echo elgg_echo("groups:{$shortname}");
		echo "</label>$line_break";
		echo elgg_view("input/{$valtype}", array(
			'name' => $shortname,
			'value' => $vars['entity']->$shortname,
		));
		echo '</div>';
	}
}
$group = get_entity($vars['entity']->guid);
//THIS HAS TO BE MOVED FROM HERE: Create a view for that.
//If is new it has to show user->language otherwise it has to show the old value
$la= array();
foreach (ElggGroup::$languages as $lang){
        $la[$lang] = elgg_echo($lang);
}

echo '<div><label>';
echo elgg_echo('language');
echo "</label>$line_break";
echo elgg_view('input/dropdown', array(
        'name' => 'language',
        'id' => 'blog_status',
        'value' => $group->language,
        'options_values' => $la
));
echo '</div>';
$user = elgg_get_logged_in_user_entity();


if(false != ($translation = $group->getTranslation($user->language))){
?>
	</div>

	<div class="elgg-col-2of2">
	<div>
		<label><?php echo elgg_echo("groups:name"); ?></label><br />
		<?php echo elgg_view("input/text", array(
			'name' => 'name',
			'value' => $translation->name,
		));
		?>
	</div>
	<?php

	$group_profile_fields = elgg_get_config('group');
	if ($group_profile_fields > 0) {
		foreach ($group_profile_fields as $shortname => $valtype) {
			$line_break = '<br />';
			if ($valtype == 'longtext') {
				$line_break = '';
			}
			echo '<div><label>';
			echo elgg_echo("groups:{$shortname}");
			echo "</label>$line_break";
			error_log("SHORTNAME " . $shortname);
			error_log("VALUE " . $group->$shortname);
			echo elgg_view("input/{$valtype}", array(
				'name' => $shortname,
				'value' => $translation->$shortname,
			));
			echo '</div>';
		}
	}
}else{
?>
	</div>

	<div class="elgg-col-2of2">
	<div>
		<label><?php echo elgg_echo("groups:name"); ?></label><br />
		<?php echo elgg_view("input/text", array(
			'name' => 'name',
			'value' => $translation->name,
		));
		?>
	</div>
	<?php

	$group_profile_fields = elgg_get_config('group');
	if ($group_profile_fields > 0) {
		foreach ($group_profile_fields as $shortname => $valtype) {
			$line_break = '<br />';
			if ($valtype == 'longtext') {
				$line_break = '';
			}
			echo '<div><label>';
			echo elgg_echo("groups:{$shortname}");
			echo "</label>$line_break";
			error_log("SHORTNAME " . $shortname);
			error_log("VALUE " . $group->$shortname);
			echo elgg_view("input/{$valtype}", array(
				'name' => $shortname,
				'value' => $translation->$shortname,
			));
			echo '</div>';
		}
	}
}

//THIS HAS TO BE MOVED FROM HERE: Create a view for that.
//If is new it has to show user->language otherwise it has to show the old value
$la= array();
foreach (ElggGroup::$languages as $lang){
        $la[$lang] = elgg_echo($lang);
}

$user = elgg_get_logged_in_user_entity();

echo '<div><label>';
echo elgg_echo('language');
echo "</label>$line_break";
echo elgg_view('input/dropdown', array(
        'name' => 'language',
        'id' => 'blog_status',
        'value' => $user->language,
        'options_values' => $la
));
echo '</div>';


?>
</div>

</div>

<div class="elgg-foot">
<?php

if (isset($vars['entity'])) {
	echo elgg_view('input/hidden', array(
		'name' => 'group_guid',
		'value' => $vars['entity']->getGUID(),
	));
}

echo elgg_view('input/submit', array('value' => elgg_echo('save')));

if (isset($vars['entity'])) {
	$delete_url = 'action/groups/delete?guid=' . $vars['entity']->getGUID();
	echo elgg_view('output/confirmlink', array(
		'text' => elgg_echo('groups:delete'),
		'href' => $delete_url,
		'confirm' => elgg_echo('groups:deletewarning'),
		'class' => 'elgg-button elgg-button-delete float-alt',
	));
}
?>
</div>
