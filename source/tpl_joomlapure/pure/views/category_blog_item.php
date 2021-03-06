<?php
/**
 * @package     SEO Overrides
 * @author		Robert Went http://www.robertwent.com
 * @copyright   Copyright (C) 2013 - Robert Went
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Use CDN for content images and resize if needed
if (($cdnUrl && $cdnContentImages) || $imageResizeContent) {
	$dom = new DOMDocument();
	$dom->loadHTML($this->item->introtext);
	foreach ($dom->getElementsByTagName('img') as $item) {
		if (substr($item->getAttribute('src'), 0, 4) != 'http') {
			$original = $item->getAttribute('src');
			$newSrc = '';
			// Resize if selected and image has width and height attributes
			if ($imageResizeContent && $item->getAttribute('width') && $item->getAttribute('height')) {
				$width = rtrim($item->getAttribute('width'), 'px');
				$height = rtrim($item->getAttribute('height'), 'px');
				$settings = array('w'=>$width,'h'=>$height, 'smush'=>$imageResizeSmush, 'cache_http_minutes'=>$imageCacheTime, 'quality'=>$imageQuality);
				$newSrc = JoomlaPure::resize($item->getAttribute('src'),$settings);
			}
			// Add cdn if selected
			if ($cdnUrl && $cdnContentImages) {
				if ($newSrc) {
					$newSrc = $cdnUrl.'/'.ltrim($newSrc, '/');
				} else {
					$newSrc = $cdnUrl.'/'.ltrim($item->getAttribute('src'), '/');
				}
			}
			// Check if we have a new path and replace original
			if ($newSrc) {
				$item->setAttribute('data-original', $original);
				$item->setAttribute('src', $newSrc);
			}
		}
	}
	$this->item->introtext = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $dom->saveHTML());
}
?>