<?php
/**
 * Group profile summary
 *
 * Icon and profile fields
 *
 * @uses $vars['group']
 */

if (!isset($vars['entity']) || !$vars['entity']) {
	echo elgg_echo('groups:notfound');
	return true;
}

$group = $vars['entity'];
$owner = $group->getOwnerEntity();

?>
<div class="groups-profile clearfix elgg-image-block">
	<div class="elgg-image">
		<div class="groups-profile-icon">
			<?php 
				if($group->isTranslation()){
					$parent = $group->getParent();
					echo elgg_view_entity_icon($parent, 'large', array('href' => ''));
				}else{
					echo elgg_view_entity_icon($group, 'large', array('href' => ''));
				}
			?>
		</div>
		<div class="groups-stats">
			<p>
				<b><?php echo elgg_echo("groups:owner"); ?>: </b>
				<?php
					/*if($group->isTranslation()){
						echo elgg_view('output/url', array(
							'text' => $parent->getOwnerEntity()->name,
							'value' => $parent->getURL(),
						));
					}else{*/
						echo elgg_view('output/url', array(
							'text' => $group->getOwnerEntity()->name,
							'value' => $group->getURL(),
						));
					//}
				?>
			</p>
			<p>
			<?php
				/*if($group->isTranslation()){
					echo elgg_echo('groups:members') . ": " . $parent->getMembers(0, 0, TRUE);
				}else{*/
					echo elgg_echo('groups:members') . ": " . $group->getMembers(0, 0, TRUE);
				//}
			?>
			</p>
		</div>
	</div>

	<div class="groups-profile-fields elgg-body">
		<?php
			echo elgg_view('groups/profile/fields', $vars);
		?>
	</div>
</div>
<?php
?>

