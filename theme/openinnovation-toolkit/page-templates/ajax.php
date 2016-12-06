<?php
/**
 * The template for performing AJAX requests
 * Template Name: AJAX DATA
 */
global $wpdb, $THEME;
$wpdb->show_errors();

$errors = new WP_Error();
$ipaddress = $_SERVER['REMOTE_ADDR'];
$time_local = current_time( 'mysql', $gmt = 0 );
$time_gmt = current_time( 'mysql', $gmt = 1 );

$action = get_query_var('action');

switch($action) {
	case 'contribute':
    $data = array (
        'contribution_type' => filter_input(INPUT_POST, 'cmb_type'),
        'name' => filter_input(INPUT_POST, 'txt_name'),
        'email' => filter_input(INPUT_POST, 'txt_email'),
        'message' => filter_input(INPUT_POST, 'txt_message'),
        'http_referer' => filter_input(INPUT_POST, 'http_referer'),
        'status' => 'publish',
        'updated_datetime' => $time_gmt,
        'ip_address' => addslashes($ipaddress)
    );

    $uploadedfile = $_FILES['txt_attachment'];
    if(is_null($uploadedfile) OR $uploadedfile['name']=='') {
      // no file uploaded by contributor
    } else {
      if ( ! function_exists( 'wp_handle_upload' ) ) {
        require_once( ABSPATH .'wp-admin/includes/file.php' );
        require_once( ABSPATH .'wp-admin/includes/image.php' );
      }

      $supported_types = array (
          'application/pdf',
          'image/jpg',
          'image/jpeg',
          'image/gif',
          'image/png'
      );
      $max_file_size = 1024 * 1024 * 2; //2MB

      // Check the type of file. We'll use this as the 'post_mime_type'.
      $filetype = wp_check_filetype( basename( $uploadedfile['name'] ) );
      $uploaded_type = $filetype['type'];

      // Check if the type is supported. If not, throw an error.
      if(!in_array($uploaded_type, $supported_types)) {
        $errors->add( 'unsupported_file', sprintf( __( 'Invalid file type.' ), get_option( 'admin_email' ) ) );
      }

      // Check maximum upload file size allowed
      $file_size = $uploadedfile['size'];
      if($file_size > $max_file_size || $file_size==0){
        $errors->add( 'unsupported_file', sprintf( __( 'File blank or larger the limit.' ), get_option( 'admin_email' ) ) );
      }

      if ( $errors->get_error_code() ){
        $result['success']	= 0;
        $result['message'] 	= $errors->get_error_message();
      } else {
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        if ( $movefile && !isset( $movefile['error'] ) ) {
          $filename = $movefile['url'];

          // Get the path to the upload directory.
          $wp_upload_dir = wp_upload_dir();

          // Prepare an array of post data for the attachment.
          $attachment = array(
            'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
            'post_mime_type' => $uploaded_type,
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
            'post_content'   => '',
            'post_status'    => 'inherit'
          );

          // Insert the attachment.
          $attach_id = wp_insert_attachment( $attachment, $filename );
          if($attach_id){
//              $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
//              $metaUpdated = wp_update_attachment_metadata( $attach_id, $attach_data );
            $data['attachment_url'] = $movefile['url'];
          } else {
            $result['success'] = 0;
            $result['message'] = 'Undefined error occured.';
          }
        } else {
          $result['success'] = 0;
          $result['message'] = 'Error occured while uploading.';
        }
      }
    }

    if ( $errors->get_error_code() ){
      $result['success']	= 0;
      $result['message'] 	= $errors->get_error_message();
    } else {
      $mail_to = 'open-innovation-toolkit@mozilla.com';
      $subject = "[Open Innovation Toolkit] Contribute to the Toolkit";
      $message = '<p>Dear Team,</p>';
      $message .= '<p>Someone contributed to the Open Innovation Toolkit through the website. Please find the details below::</p>';

      $message .= '<p>Contribution Type: '.$data['contribution_type'] .'</p>';
      $message .= '<p>Name: '.$data['name'] .'</p>';
      $message .= '<p>Email: '.$data['email'] .'</p>';
      $message .= '<p>Message: '.$data['message'] .'</p>';
      $message .= '<p>Attachment: '.$data['attachment_url'] .'</p>';
      $message .= '<p>Http Referer: '.$data['http_referer'] .'</p>';
      $headers  = array('Content-Type: text/html; charset=UTF-8');
//      $headers[] = 'Bcc: chandan@futurescape.co';

      add_filter( 'wp_mail_content_type', 'set_html_content_type' );
      $mailSent = wp_mail($mail_to, $subject, $message, $headers);
      remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

      $result['success'] = 1;
      $result['message'] = '<h3 class="heading3">Thank you so much for contributing to the toolkit!</h3> The involvement of community members like you is what makes this project truly open source. Feel from to come back to this page at any time if you have further suggestions, ideas, or insights. We&rsquo;re always looking for ways to improve.';
    }

    header( 'content-type: application/json; charset=utf-8' );
    echo json_encode( $result );
    die();
    break;
    
    
  case 'contribute-recaptcha':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // Construct the Google verification API request link.
      $params = array();
      $params['secret'] = '6LcuJSUTAAAAAH7oaKPyN_1uekhZ4ZJAhguZm7hR'; // Secret key
      if (!empty($_POST) && isset($_POST['g-recaptcha-response'])) {
        $params['response'] = urlencode($_POST['g-recaptcha-response']);
      }
      $params['remoteip'] = $_SERVER['REMOTE_ADDR'];

      $params_string = http_build_query($params);
      $requestURL = 'https://www.google.com/recaptcha/api/siteverify?' . $params_string;

      // Get cURL resource
      $curl = curl_init();

      // Set some options
      curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $requestURL,
      ));

      // Send the request
      $response = curl_exec($curl);
      // Close request to clear up some resources
      curl_close($curl);

      $response = @json_decode($response, true);

      if ($response["success"] == true) {
        $data = array (
            'contribution_type' => filter_input(INPUT_POST, 'cmb_type'),
            'name' => filter_input(INPUT_POST, 'txt_name'),
            'email' => filter_input(INPUT_POST, 'txt_email'),
            'message' => filter_input(INPUT_POST, 'txt_message'),
            'http_referer' => filter_input(INPUT_POST, 'http_referer'),
            'status' => 'publish',
            'updated_datetime' => $time_gmt,
            'ip_address' => addslashes($ipaddress)
        );

        $uploadedfile = $_FILES['txt_attachment'];
        if(is_null($uploadedfile) OR $uploadedfile['name']=='') {
          // no file uploaded by contributor
        } else {
          if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH .'wp-admin/includes/file.php' );
            require_once( ABSPATH .'wp-admin/includes/image.php' );
          }

          $supported_types = array (
              'application/pdf',
              'image/jpg',
              'image/jpeg',
              'image/gif',
              'image/png'
          );
          $max_file_size = 1024 * 1024 * 2; //2MB

          // Check the type of file. We'll use this as the 'post_mime_type'.
          $filetype = wp_check_filetype( basename( $uploadedfile['name'] ) );
          $uploaded_type = $filetype['type'];

          // Check if the type is supported. If not, throw an error.
          if(!in_array($uploaded_type, $supported_types)) {
            $errors->add( 'unsupported_file', sprintf( __( 'Invalid file type.' ), get_option( 'admin_email' ) ) );
          }

          // Check maximum upload file size allowed
          $file_size = $uploadedfile['size'];
          if($file_size > $max_file_size || $file_size==0){
            $errors->add( 'unsupported_file', sprintf( __( 'File blank or larger the limit.' ), get_option( 'admin_email' ) ) );
          }

          if ( $errors->get_error_code() ){
            $result['success']	= 0;
            $result['message'] 	= $errors->get_error_message();
          } else {
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
            if ( $movefile && !isset( $movefile['error'] ) ) {
              $filename = $movefile['url'];

              // Get the path to the upload directory.
              $wp_upload_dir = wp_upload_dir();

              // Prepare an array of post data for the attachment.
              $attachment = array(
                'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
                'post_mime_type' => $uploaded_type,
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
              );

              // Insert the attachment.
              $attach_id = wp_insert_attachment( $attachment, $filename );
              if($attach_id){
    //              $attach_data = wp_generate_attachment_metadata( $attach_id, $movefile['file'] );
    //              $metaUpdated = wp_update_attachment_metadata( $attach_id, $attach_data );
                $data['attachment_url'] = $movefile['url'];
              } else {
                $result['success'] = 0;
                $result['message'] = 'Undefined error occured.';
              }
            } else {
              $result['success'] = 0;
              $result['message'] = 'Error occured while uploading.';
            }
          }
        }

        if ( $errors->get_error_code() ){
          $result['success']	= 0;
          $result['message'] 	= $errors->get_error_message();
        } else {
//          $mail_to = 'open-innovation-toolkit@mozilla.com';
          $mail_to = 'kapilj@futurescape.co';
          $subject = "[Open Innovation Toolkit] Contribute to the Toolkit";
          $message = '<p>Dear Team,</p>';
          $message .= '<p>Someone contributed to the Open Innovation Toolkit through the website. Please find the details below::</p>';

          $message .= '<p>Contribution Type: '.$data['contribution_type'] .'</p>';
          $message .= '<p>Name: '.$data['name'] .'</p>';
          $message .= '<p>Email: '.$data['email'] .'</p>';
          $message .= '<p>Message: '.$data['message'] .'</p>';
          $message .= '<p>Attachment: '.$data['attachment_url'] .'</p>';
          $message .= '<p>Http Referer: '.$data['http_referer'] .'</p>';
          $headers  = array('Content-Type: text/html; charset=UTF-8');
    //      $headers[] = 'Bcc: chandan@futurescape.co';

          add_filter( 'wp_mail_content_type', 'set_html_content_type' );
          $mailSent = wp_mail($mail_to, $subject, $message, $headers);
          remove_filter( 'wp_mail_content_type', 'set_html_content_type' );

          $result['success'] = 1;
          $result['message'] = '<h3 class="heading3">Thank you so much for contributing to the toolkit!</h3> The involvement of community members like you is what makes this project truly open source. Feel from to come back to this page at any time if you have further suggestions, ideas, or insights. We&rsquo;re always looking for ways to improve.';
        }
      } else {
        $result['success']	= 0;
        $result['message'] 	= "Invalid captcha.";
      }
    }

    header( 'content-type: application/json; charset=utf-8' );
    echo json_encode( $result );
    die();
    break;
    
    
}

function set_html_content_type() {
  return 'text/html';
}