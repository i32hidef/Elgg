<?php
/**
 * View for blog objects
 *
 * @package Blog
 */

$full = elgg_extract('full_view', $vars, FALSE);
$blog = elgg_extract('entity', $vars, FALSE);

if (!$blog) {
	return TRUE;
}

/*Ver si es traduccion si lo es metemos una variable a true y se guarda la entidad.
 Luego vamos comprobando si es o no en base a esa variable, y controlamos los contextos
 En principio solo hay que controlar si es translations o no */

//We will show blogs that are not translations
var_dump("ORIGINAL " . $blog->language);

$user = elgg_get_logged_in_user_entity();
if(($translation = $blog->getTranslation($user->language)) && !elgg_in_context('translations')){
	$blog = $translation;
	$istranslation = true;
	var_dump("TRANSLATION " . $translation->language);
}

	$owner = $blog->getOwnerEntity();
	$container = $blog->getContainerEntity();
	$categories = elgg_view('output/categories', $vars);
	$excerpt = $blog->excerpt;
	error_log("EXCERPT " . $excerpt);
	$owner_icon = elgg_view_entity_icon($owner, 'tiny');
	$owner_link = elgg_view('output/url', array(
		'href' => "blog/owner/$owner->username",
		'text' => $owner->name,
	));
	$author_text = elgg_echo('byline', array($owner_link));
	$tags = elgg_view('output/tags', array('tags' => $blog->tags));
	$date = elgg_view_friendly_time($blog->time_created);

	// The "on" status changes for comments, so best to check for !Off
	if ($blog->comments_on != 'Off') {
		$comments_count = $blog->countComments();
		//only display if there are commments
		if ($comments_count != 0) {
			$text = elgg_echo("comments") . " ($comments_count)";
			$comments_link = elgg_view('output/url', array(
				'href' => $blog->getURL() . '#blog-comments',
				'text' => $text,
			));
		} else {
			$comments_link = '';
		}
	} else {
		$comments_link = '';
	}

	$metadata = elgg_view_menu('entity', array(
		'entity' => $vars['entity'],
		'handler' => 'blog',
		'sort_by' => 'priority',
		'class' => 'elgg-menu-hz',
	));

	$subtitle = "<p>$author_text $date $comments_link</p>";
	$subtitle .= $categories;

	// do not show the metadata and controls in widget view nor in the translation view
	if (elgg_in_context('widgets') ){	//|| elgg_in_context('translations')) {
		$metadata = '';
	}

	if ($full) {

		$body = elgg_view('output/longtext', array(
			'value' => $blog->description,
			'class' => 'blog-post',
		));

		$header = elgg_view_title($blog->title);

		$params = array(
			'entity' => $blog,
			'title' => false,
			'metadata' => $metadata,
			'subtitle' => $subtitle,
			'tags' => $tags,
		);
		$params = $params + $vars;
		$list_body = elgg_view('object/elements/blogsummary', $params);

		$blog_info = elgg_view_image_block($owner_icon, $list_body);

echo <<<HTML
$header
$blog_info
$body
HTML;

	} else {
		// brief view
		$params = array(
			'entity' => $blog,
			'metadata' => $metadata,
			'subtitle' => $subtitle,
			'tags' => $tags,
			'content' => $excerpt,
		);
		$params = $params + $vars;
		$list_body = elgg_view('object/elements/blogsummary', $params);

		echo elgg_view_image_block($owner_icon, $list_body);
	}

