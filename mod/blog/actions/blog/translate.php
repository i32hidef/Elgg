<?php
/**
 * Translate blog entity
 *
 * @package Blog
 */

// start a new sticky form session in case of failure
elgg_make_sticky_form('blog');

// save or preview
//$save = (bool)get_input('save');
//error_log("ACTION TRANSLATE " . $save);
// store errors to pass along
$error = FALSE;
$error_forward_url = REFERER;
$user = elgg_get_logged_in_user_entity();

// edit or create a new entity
$guid = get_input('guid');
//$language = get_input('language');

$blog = new ElggBlog();
$blog->subtype = 'blog';
$new_post = TRUE;

$entity = get_entity($guid);

// set the previous status for the hooks to update the time_created and river entries
$old_status = $blog->status;

// set defaults and required values.
$values = array(
	'title' => '',
	'description' => '',
	'status' => 'draft',
	'access_id' => ACCESS_DEFAULT,
	'comments_on' => 'On',
	'excerpt' => '',
	'language' => '',
	'tags' => '',
	'container_guid' => (int)get_input('container_guid'),
);

// fail if a required entity isn't set
$required = array('title', 'description');

// load from POST and do sanity and access checking
foreach ($values as $name => $default) {
	$value = get_input($name, $default);

	if (in_array($name, $required) && empty($value)) {
		$error = elgg_echo("blog:error:missing:$name");
	}

	if ($error) {
		break;
	}

	switch ($name) {
		case 'tags':
			if ($value) {
				$values[$name] = string_to_tag_array($value);
			} else {
				unset ($values[$name]);
			}
			break;

		case 'excerpt':
			if ($value) {
				$value = elgg_get_excerpt($value);
			} else {
				$value = elgg_get_excerpt($values['description']);
			}
			$values[$name] = $value;
			break;

		case 'container_guid':
			// this can't be empty or saving the base entity fails
			if (!empty($value)) {
				if (can_write_to_container($user->getGUID(), $value)) {
					$values[$name] = $value;
				} else {
					$error = elgg_echo("blog:error:cannot_write_to_container");
				}
			} else {
				unset($values[$name]);
			}
			break;

		// don't try to set the guid
		case 'guid':
			unset($values['guid']);
			break;

		default:
			$values[$name] = $value;
			if($name == 'language') error_log("GUARDANDO LENGuaje en traduccion " . $values[$name]);
			break;
	}
}

// if preview, force status to be draft
//if ($save == false) {
//	$values['status'] = 'draft';
//}

// assign values to the entity, stopping on error.
if (!$error) {
	foreach ($values as $name => $value) {
		if (FALSE === ($blog->$name = $value)) {
			$error = elgg_echo('blog:error:cannot_save' . "$name=$value");
			break;
		}
	}
}

// only try to save base entity if no errors
if (!$error) {
	if ($blog->save()) {
		
		$entity->addTranslation($blog->getGUID());
		$blog->container_guid = $entity->container_guid;

		// remove sticky form entries
		elgg_clear_sticky_form('blog');

		// remove autosave draft if exists
		$blog->clearAnnotations('blog_auto_save');

		// no longer a brand new post.
		//$blog->clearMetadata('new_post');

		// if this was an edit, create a revision annotation
		if (!$new_post && $revision_text) {
			$blog->annotate('blog_revision', $revision_text);
		}

		system_message(elgg_echo('blog:message:saved'));

		$status = $blog->status;
		$db_prefix = elgg_get_config('dbprefix');

		// add to river if changing status or published, regardless of new post
		// because we remove it for drafts.
		error_log("NEW POST ? " . $new_post);
		error_log("OLD STATUS ? " . $old_status);
		error_log("STATUS " . $status);
		error_log("User GUID " . elgg_get_logged_in_user_guid());
		if (($new_post || $old_status == 'draft') && $status == 'published') {
			add_to_river('river/object/blog/translate', 'translate', elgg_get_logged_in_user_guid(), $blog->getGUID());

			if ($guid) {
				$blog->time_created = time();
				$blog->save();
			}
		} elseif ($old_status == 'published' && $status == 'draft') {
			elgg_delete_river(array(
				'object_guid' => $blog->guid,
				'action_type' => 'create',
			));
		}

		if ($blog->status == 'published' || $save == false) {
			forward($blog->getURL());
		} else {
			forward("blog/edit/$blog->guid");
		}
			
	} else {
		register_error(elgg_echo('blog:error:cannot_save'));
		forward($error_forward_url);
	}
} else {
	register_error($error);
	forward($error_forward_url);
}
