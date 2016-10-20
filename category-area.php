<?php
/*
The template for displaying Category-Area pages.
*/
get_header(); ?>

	<div id="main">
		<div class="entry">

<?php if (have_posts()) : ?>

	<header class="page-header">
		<?php
			the_archive_title( '<h1 class="page-title">', '</h1>' );
		?>
	</header>

<?php
$html = '';
$cat_area = get_category_by_slug("area");

$categories = get_categories('child_of=' .$cat_area->cat_ID ."&orderby=ID");
foreach($categories as $cat){
	$url_category =  get_category_link($cat->cat_ID);

	echo '<h2 class="area-title"><a href="' .$url_category ,'">' .$cat->cat_name .'限定 (' ,$cat->count .'件)</a></h2>';
	echo '<ul class="articles">';
	query_posts('&category_name="' .$cat->slug .'&showposts=5"');
	while (have_posts()) : the_post();
		get_template_part( 'content', get_post_format() );
	endwhile;

	echo '</ul>';
	echo '<p><a href="' .$url_category .'" class="more-link">' .$cat->cat_name .'限定のお菓子をもっと見る</a></p>';

}

?>

<?php endif; ?>

	</div>
</div><!-- #main -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>