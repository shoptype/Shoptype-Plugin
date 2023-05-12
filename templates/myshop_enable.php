<?php
/*
 * Template name: Shoptype Checkout Success template
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package shoptype
 */

get_header(null);
?>
	
	<h2 class="st-success-heading" id="chk_heading">Myshop not enabled</h2>
	<div class="st-success-txt" id="chk_txt">Kindly contact you site admin at <?php echo get_option('admin_email'); ?> </div>

<?php
get_footer();