<?php
/*
The Template for displaying single maker.
*/
get_header(); ?>

	<div id="main">
		<div class="entry archive">

<?php if (have_posts()) : ?>

	<header class="content-header">
		<h1 class="content-title">「<?php the_title(); ?>」の記事</h1>
	</header>

	<?php
	$param = array( 'showposts' => -1,
					'post_type' => 'post',
					'meta_query' => array(
						array(
							'key' => 'maker',
							'value' => $post->ID,
							'compare' => 'LIKE'
						)
					));

	echo '<ul class="articles">';

	// WordPressのループ処理
	$myposts = get_posts($param);
	foreach($myposts as $post){
		setup_postdata($post);  // 1件の投稿
		get_template_part( 'content' );
	}

	echo '</ul>';
	?>

<?php endif; ?>

	</div>
</div><!-- #main -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>