<?php
/**
 * The template for getting methods in JSON
 * Template Name: JSON Data
 */

$action = get_query_var('action');
$data = [];

switch($action) {
	case 'methods':
		$current_page = filter_input(INPUT_GET, 'pageid');
		$process_name = filter_input(INPUT_GET, 'process');
		$difficulty = filter_input(INPUT_GET, 'difficulty');
		$duration = filter_input(INPUT_GET, 'duration');
		$outcomes = filter_input(INPUT_GET, 'outcomes');
		$search_text = filter_input(INPUT_GET, 'search_text');

		$page_size = 99;

		$args=array(
	      'post_type' => 'method',
	      'post_status' => 'publish',
	      'posts_per_page' => $page_size,
	      'paged' => $current_page
	  	);

	  	if($search_text <> '') {
	  		$args['s'] = $search_text;
	  	}

		if($process_name <> '') {
			$args['tax_query'] = array(
		        array(
		            'taxonomy' => 'process',
		            'field' => 'slug',
		            'terms' => $process_name
		        )
		    );
		}

		$meta_query = array();
		if($difficulty <> '') {
			$meta_query[] = array(
	            'key' => 'difficulty_level',
	            'value' => $difficulty,
	            'compare' => '=',
	        );
		}


		if($duration <> '') {
			$duration = explode('-', $duration);
			$duration_min = (int)$duration[0];
			$duration_max = (isset($duration[1])) ? (int)$duration[1] : 0;

			$meta_query[] = array(
	            'key' => 'time_min',
	            'value' => $duration_min,
	            'compare' => '>=',
	            'type' => 'numeric',
	        );
	        $meta_query[] = array(
	            'key' => 'time_max',
	            'value' => $duration_min,
	            'compare' => '>=',
	            'type' => 'numeric',
	        );

	        if($duration_max > 0) {
		      	$meta_query[] = array(
		            'key' => 'time_min',
		            'value' => $duration_max,
		            'compare' => '<=',
		            'type' => 'numeric',
		        );  

		        $meta_query[] = array(
		            'key' => 'time_max',
		            'value' => $duration_max,
		            'compare' => '<=',
		            'type' => 'numeric',
		        );
		    }
		}

		if($outcomes <> '') {
			$meta_query[] = array(
	            'key' => 'outcomes',
	            'value' => serialize($outcomes),
	            'compare' => 'LIKE',
	        );
		}

		if(!(empty($meta_query))) {
			$meta_query['relation'] = 'AND';
			$args['meta_query'] = $meta_query;
		}

		// var_dump($args);
		
	  	// $results = query_posts($args);

	  	$the_query = new WP_Query( $args );
  		global $post;

	  	if ( $the_query->have_posts() ) {
	  		$items = [];
		    while ( $the_query->have_posts() ) {
		      	$the_query->the_post();
		      	$o = get_post_meta($post->ID);
				$process = get_the_terms( $post->ID , 'process' );
				$process_slug = $process[0]->slug;
				$link_url = get_permalink($post->ID);

			  	$image = get_field('method_image', $post->ID);
			  	if(!($image)) {
				    $image = oit_method_image($process[0]->term_id);
			  	}
			  	$image_url = $image['sizes']['method_thumbnail'];
	  			$item = array(
	  				'process' => $process_slug,
	  				'image_url' => $image_url,
	  				'ID' => $post->ID,
	  				'post_title' => $post->post_title,
	  				'post_link' => $link_url,
	  				'post_excerpt' => apply_filters('the_content', $o['excerpt'][0]),
	  				'difficulty_level' => $o['difficulty_level'][0],
	  				'duration' => oit_method_duration($o),
				);
				// echo '<hr />';
				// var_dump($item['process']);
				$items[] = $item;
		      
		    }
		    wp_reset_postdata();
		    $data['items'] = $items;

		    $total_count = $the_query->found_posts;
		  	if($total_count > ($current_page * $page_size)) {
		  	  $data['load_more'] = TRUE;
		  	}
	  	}
	  	wp_reset_query();

	    header( 'content-type: application/json; charset=utf-8' );
	    echo json_encode( $data );
	    die();
	    break;
}

