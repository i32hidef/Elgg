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
/*We control if the context is widgets or not, to put a translation in
	the widget of the group in case it exists
*/
error_log("BLOG OBJECT VIEW");
if(!elgg_in_context('widgets')){
	
	$owner = $blog->getOwnerEntity();
	$container = $blog->getContainerEntity();
	$categories = elgg_view('output/categories', $vars);
	$excerpt = $blog->excerpt;

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
		$list_body = elgg_view('object/elements/summary', $params);

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
		$list_body = elgg_view('object/elements/summary', $params);

		echo elgg_view_image_block($owner_icon, $list_body);
	}
}else{
	error_log("WIDgets context");
	//We will show blogs that are not translations
	if(!$blog->isTranslation()){
		error_log("EL BLOG: no es traduccion");
		$user = elgg_get_logged_in_user_entity();
		if(FALSE != ($translation = $blog->getTranslation($user->language))){
			error_log("EL BLOG: tiene traducciones en el idioma");
			$blog = $translation;
		}
		$owner = $blog->getOwnerEntity();
		$container = $blog->getContainerEntity();
		$categories = elgg_view('output/categories', $vars);
		$excerpt = $blog->excerpt;

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
			$list_body = elgg_view('object/elements/summary', $params);

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
		$list_body = elgg_view('object/elements/summary', $params);

		echo elgg_view_image_block($owner_icon, $list_body);
	}

}
}

	/*}else{
		//Forward to the parent
		error_log("ES TRADUCCION");
		if(!elgg_in_context('translations')){	
			$parent = $blog->getParent();
			//forward($parent->getURL());
		}
	
	}*/
