<?php
/**
 * The template for redirecting page to his parent.
 * Template Name: Redirect to Parent
 */

global $post;
$redirect_url = get_permalink($post->post_parent);
$redirect_url = $redirect_url . '#' . $post->post_name;
wp_redirect($redirect_url);