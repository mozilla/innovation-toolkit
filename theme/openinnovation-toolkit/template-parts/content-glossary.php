<?php
/**
 * The template used for displaying page content
 */

$terms = oit_get_terms();
?>

<header class="glossary-header">
  <div class="container">
    <div class="row">
      <div class="col-md-3 col-xs-12 col-md-offset-1">
        <h1 class="heading1">Glossary</h1>
      </div>
      <div class="col-md-7 col-xs-12">
        <div class="glossary-letters-wrapper">
          <?php echo oit_term_letters($terms);?>
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-10 col-xs-12 col-md-offset-1">
        <div class="separator"></div>
      </div>
    </div>
    
  </div>
</header><!-- .entry-header -->

<div class="glossary-terms">
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-xs-12 col-md-offset-1">
        <?php
          echo oit_terms_list($terms);
        ?>
      </div>
    </div>
  </div>
</div>