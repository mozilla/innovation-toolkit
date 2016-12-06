<?php
/**
 * The template used for displaying contribution page content
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-xs-12 col-md-offset-1">
        <header class="entry-header">
          <h1 class="entry-title heading1"><?php echo get_custom_post_title();?></h1>
        </header><!-- .entry-header -->
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-5 col-xs-12 col-md-offset-1">
        <?php echo oit_contribute_form_recaptcha();?>
      </div>
      <div class="col-md-4 col-xs-12 col-md-offset-1">
        <div class="entry-content">
          <?php
          the_content();
          ?>
        </div><!-- .entry-content -->
      </div>
    </div>
  </div><!-- .container -->
</article><!-- #post-## -->

<?php
function oit_contribute_form_recaptcha() {
  global $post;
  
  $return = '';
  $contribute_type = get_query_var('action');
  $contribute_types = array(
      'method' => 'Submit a method',
      'question' => 'Submit a question',
      'example' => 'Submit an example',
      'resource' => 'Submit a resource'
  );
  
  $return .= '<div class="form-wrapper">';
    $return .= '<div class="custom-form">';
      $return .= '<form name="frmContribute" enctype="multipart/form-data" method="post" action="'.SITE_URL.'/ajax/contribute" class="ajax-form">';

        $return .= '<div class="ajax-msg"></div>';

        $return .= '<div class="form-fields">';
        $return .= '<div class="field-group">';
          $return .= '<label for="cmb_type" class="hidden-sm-down hidden-xs-up">Contribution Type</label>';
          $return .= '<select type="text" id="cmb_type" name="cmb_type" class="custom-dropdown">';
          
          foreach($contribute_types as $key=>$val) {
            $return .= '<option value="'.$key.'"'.(($key==$contribute_type) ? ' selected="selected"' : '').'>'.$val.'</option>';
          }
          $return .= '</select>';
        $return .= '</div>';
      
      
        $return .= '<div class="field-group">';
          $return .= '<label for="txt_name">Name</label>';
          $return .= '<input type="text" id="txt_name" name="txt_name" class="field-input" />';
        $return .= '</div>';
        
        $return .= '<div class="field-group">';
          $return .= '<label for="txt_email">Email Address (required)</label>';
          $return .= '<input type="text" id="txt_email" name="txt_email" class="field-input required email" />';
        $return .= '</div>';
        
        $return .= '<div class="field-group">';
          $return .= '<label for="txt_message">Message (required)</label>';
          $return .= '<textarea id="txt_message" name="txt_message" class="field-input required"></textarea>';
        $return .= '</div>';
        
        $return .= '<div class="field-group">';
          $return .= '<label for="txt_attachment">Image Attachment</label>';

          $return .= '<input type="text" id="txt_filename" name="txt_filename" readonly="readonly" class="field-input" />';
          $return .= '<div class="input-file-wrapper">';
            $return .= '<input type="file" id="txt_attachment" name="txt_attachment" />';
            $return .= 'Browse';
          $return .= '</div>';
          $return .= '<div class="clear"></div>';
        $return .= '</div>';
        
        $return .= '<div class="field-group field-group-terms">';
          $return .= '<input type="checkbox" id="chk_terms" name="chk_terms" class="required" /> &nbsp; I agree to the <a href="#" data-toggle="modal" data-target="#modalSubmissionTerms">Toolkit Submission Terms</a>';
        $return .= '</div>';
        
        $return .= '<div id="captcha_container" class="field-group field-group-recaptcha" data-sitekey="6LcuJSUTAAAAAGfyCSFI4zN_o0TKkPTokmvH0qt3"></div>';
        
        $return .= '<div class="field-group field-group-action">';
          $return .= '<input type="hidden" name="http_referer" value="'.$_SERVER['HTTP_REFERER'].'" />';
          $return .= '<input type="submit" class="field-submit" value="Submit" />';
        $return .= '</div>';
        $return .= '</div><!-- .form-fields -->';
      $return .= '</form>';
    $return .= '</div>';
  $return .= '</div>';
  
  $return .= '<div id="modalSubmissionTerms" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Toolkit Submission Terms</h4>
      </div>
      <div class="modal-body">'.apply_filters('the_content', get_field('submission_terms', $post->ID)).'</div>
    </div>
  </div>
</div>';
  
  return $return;
}
?>