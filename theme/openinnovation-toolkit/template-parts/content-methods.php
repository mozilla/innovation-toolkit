<?php
/**
 * The template used for displaying page content
 */

$process_slug = get_query_var('action');
$has_process_active = FALSE;

$processes = oit_process_pages();
$processList = array();
if($processes) {
  foreach($processes as $process) {
    $processList[$process->post_name] = $process->post_title;
    if( ($has_process_active===FALSE) && ($process->post_name===$process_slug) ) {
      $has_process_active = TRUE;
    }
  }
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div id="methods">
    <div class="method-filters secondary">
      <div class="filter-wrapper filter-process">
        <div class="filter-container">
          <h4 class="hidden-sm-down">Process</h4>
          <a href="#expand-process" class="filter-header hidden-md-up">Process</a>
          <ul>
          <?php
            echo '<li'.((!($has_process_active) ? " class='active'" : '')).'><a href="#">All</a></li>';
            foreach($processList as $key => $val) {
              echo '<li'.(($key===$process_slug ? " class='active'" : '')).'><a href="#'.$key.'">'.$val.'</a></li>';
            }
          ?>
          </ul>
        </div>
      </div>
      

      <?php
        $args=array(
            'post_type' => 'method',
            'post_status' => 'publish',
            'posts_per_page' => 1,
        );
        $methods = get_posts($args);
        if($methods) {
          $method_id = $methods[0]->ID;
        }
        $outcomes = get_field_object('outcomes', $method_id);
        $difficutly_levels = get_field_object('difficulty_level', $method_id);
      ?>


      <div class="filter-wrapper filter-difficulty">
        <div class="filter-container">
          <h4 class="hidden-sm-down">Difficulty</h4>
          <a href="#expand-difficulty" class="filter-header hidden-md-up">Difficulty</a>
          <ul>
            <li class="active"><a href="#">All</a></li>
            <?php
              foreach($difficutly_levels['choices'] as $key=>$val) {
                echo '<li><a href="#'.$key.'">'.$val.'</a></li>';
              }
            ?>
          </ul>
        </div>
      </div>
      
      <div class="filter-wrapper filter-duration">
        <div class="filter-container">
          <h4 class="hidden-sm-down">Time</h4>
          <a href="#expand-time" class="filter-header hidden-md-up">Time</a>
          <ul>
            <li class="active"><a href="#">All</a></li>
            <li><a href="#0-5">1-5 days</a></li>
            <li><a href="#5-10">5-10 days</a></li>
            <li><a href="#10+">10+ days</a></li>
          </ul>
        </div>
      </div>
      
      <div class="filter-wrapper filter-outcomes">
        <div class="filter-container">
          <h4 class="hidden-sm-down">Outcomes</h4>
          <a href="#expand-outcomes" class="filter-header hidden-md-up">Outcomes</a>
          <ul>
            <li class="active"><a href="#">All</a></li>
            <?php
              foreach($outcomes['choices'] as $key=>$val) {
                echo '<li><a href="#'.$key.'">'.$val.'</a></li>';
              }
            ?>
          </ul>
        </div>
      </div>
      
      <div class="clear"></div>
    </div>
    <div id="methods-content">
      <div class="heading-wrapper">
        <h1 class="heading2">Methods</h1>
        <a href="<?php echo SUBMIT_PAGE_URL;?>/method/" class="quick-link link-right">Submit a method</a>
      </div>
      
      <?php echo oit_all_methods();?>
    </div>
    <div class="clear"></div>
  </div><!-- .container -->
</article><!-- #post-## -->
