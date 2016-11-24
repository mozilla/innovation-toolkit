<?php
/**
 * The template used for displaying page content
 */

$questions = oit_get_questions();
?>

<header class="questions-header">
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-xs-12 col-md-offset-1">
        <h4 class="hidden-sm-down">Commonly Asked Questions</h4>
        <a href="<?php echo SUBMIT_PAGE_URL;?>/question/" class="quick-link hidden-sm-down">Submit a question</a>
        <?php echo oit_questions_dropdown($questions);?>
      </div>
    </div>
    
  </div>
</header><!-- .entry-header -->

<div class="questions-list">
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-xs-12 col-md-offset-1">
        <?php
          echo oit_questions_list($questions);
        ?>
      </div>
    </div>
  </div>
</div>