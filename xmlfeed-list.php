<?php

function uploadImageToMediaLibrary($postID, $url, $alt = "test") {

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $tmp = download_url( $url );
    $desc = $alt;
    $file_array = array();

    // Set variables for storage
    // fix file filename for query strings
    preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $url, $matches);
    $file_array['name'] = basename($matches[0]);
    $file_array['tmp_name'] = $tmp;

    // If error storing temporarily, unlink
    if ( is_wp_error( $tmp ) ) {
        @unlink($file_array['tmp_name']);
        $file_array['tmp_name'] = '';
    }

    // do the validation and storage stuff
    $id = media_handle_sideload( $file_array, $postID, $desc);

    // If error storing permanently, unlink
    if ( is_wp_error($id) ) {
        @unlink($file_array['tmp_name']);
        return $id;
    }

    return $id;
}

add_filter('cron_schedules', 'xml_add_setting_minutes');

function xml_add_setting_minutes($schedules) {
	$setting = get_option('xml_feed_settings');
	if(isset($setting['cron_interval'])) {
		$cron_interval = (int)$setting['cron_interval'];
	} else {
		$cron_interval = 900;
	}
	
    $schedules['every_setting_mins'] = array(
        'interval' => $cron_interval,
        'display' => __('Xml To Post', 'textdomain')
    );
    return $schedules;
}

// Schedule an action if it's not already scheduled
if (!wp_next_scheduled('xml_add_setting_minutes')) {
    wp_schedule_event(time(), 'every_setting_mins', 'xml_add_setting_minutes');
}

// Hook into that action that'll fire every three hours
add_action('xml_add_setting_minutes', 'get_xml_data');

function get_xml_data() {
	$setting = get_option('xml_feed_settings');
	if(isset($setting['url'])) {
		$xmlURL = $setting['url'];
		$curl = curl_init();

		curl_setopt_array($curl, Array(
			CURLOPT_URL            => $xmlURL,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_ENCODING       => 'UTF-8'
		));

		$data = curl_exec($curl);
		curl_close($curl);

		$xml = simplexml_load_string($data, null, LIBXML_NOCDATA);

		for($i = 0; $i <= 14; $i++){

			$image = $xml->channel->item[$i]->children('media', True)->content->attributes();
			$title = (string)$xml->channel->item[$i]->title;
			$link = (string)$xml->channel->item[$i]->link;
			$description = (string)$xml->channel->item[$i]->description;
			$pubDate = $xml->channel->item[$i]->pubDate;
			$date = date( 'Y-m-d H:i:s', strtotime($pubDate) );
			$guid = (int)$xml->channel->item[$i]->guid;

			$latestargs = array(
				'numberposts' => -1,
				'post_type'   => 'post',
				'meta_query' => array(
					array(
						'key' => 'guid',
						'value' => $guid,
						'compare' => '=',
					)
				)
			);

			$latest_post = get_posts( $latestargs );
			if(empty($latest_post)) {
				$args = array(
					'post_title'    => wp_strip_all_tags( $title ),
					'post_content'  => $description,
					'post_status'   => 'publish',
					'post_author'   => 1,
					'post_date'     => $date,
					'post_type'     => 'post',
					'meta_input' => array(
						'linkextrnal' => $link,
						'guid' => $guid
					)
				);

				//Insert the post into the database
				$post_id = wp_insert_post( $args );

				if($image) {
					$image_id = uploadImageToMediaLibrary($post_id,$image);
					$res2 = set_post_thumbnail( $post_id, $image_id );
				}

				if($post_id) {
					echo 'Post is created - '.$post_id;
				}
			}
			wp_reset_query();
		}
	} else {
		echo 'Xml Url is required';
	}
}