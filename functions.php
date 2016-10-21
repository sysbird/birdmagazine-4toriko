<?php

//////////////////////////////////////////////////////
// Child Theme Initialize
function birdmagazine_4toriko_init() {

	// add post type
	$labels = array(
		'name'		=> 'メーカー',
		'all_items'	=> 'メーカー一覧',
		);

	$args = array(
		'labels'		=> $labels,
		'supports'		=> array( 'title','editor', 'custom-fields' ),
		'public'		=> true,	// 公開するかどうが
		'show_ui'		=> true,	// メニューに表示するかどうか
		'menu_position'	=> 5,		// メニューの表示位置
		'has_archive'	=> true,	// アーカイブページの作成
		);

	register_post_type( 'maker', $args );

}
add_action( 'init', 'birdmagazine_4toriko_init', 0 );

//////////////////////////////////////////////////////
// Filter at main query
function birdmagazine_4toriko_query( $query ) {
	if ( !is_admin() && $query->is_main_query() && ( $query->is_archive() || $query->is_search() ) ) {
		$query->set( 'posts_per_page', 3 );
	}
}
add_action( 'pre_get_posts', 'birdmagazine_4toriko_query' );

//////////////////////////////////////////////////////
// Enqueue Scripts
function birdmagazine_4toriko_scripts() {

	wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
}
add_action( 'wp_enqueue_scripts', 'birdmagazine_4toriko_scripts' );


//////////////////////////////////////////
//  display maker
function birdmagazine_4toriko_the_maker($ID, $before, $after, $link = true ) {

	$my_posts = get_field( 'maker', $ID );
	if( $my_posts && is_array( $my_posts )):
		foreach( $my_posts as $p ):

			echo $before;
			if( $link ){
				echo '<a href="' .get_the_permalink($p->ID) .'">';
			}

			echo get_the_title($p->ID);

			if( $link ){
				echo '</a>';
			}
			echo $after;

			return;
		endforeach;
		wp_reset_postdata();
	endif;
}

//////////////////////////////////////////
//  Show price
function  birdmagazine_4toriko_the_price( $ID, $before, $after ) {

	$price = get_field( 'price', $ID );
	if( !empty( $price ) ){
		echo $before .$price .$after;
	}
}

//////////////////////////////////////////////////////
// entry footer
function birdmagazine_4toriko_the_info() {

	echo '<dl>';
	echo '<dt>投稿日</dt><dd><time class="postdate" datetime="' .get_the_time( 'Y-m-d' ) .'">' .get_post_time( get_option( 'date_format' ) ) .'</time></dd>';
	echo '<dt>種類</dt><dd>';
	the_category(', ');
	echo  '</dd>';
	the_tags('<dt>キーワード</dt><dd>', ', ', '</dd>');
	birdstar_the_maker( get_the_ID(), '<dt>メーカー</dt><dd>', '</dd>' );
	birdstar_the_price( get_the_ID(), '<dt>価格</dt><dd>', ' 円</dd>' );
	echo '</dl>';
}

//////////////////////////////////////////////////////
// attachment photos exept eyecatch
function  birdmagazine_4toriko_the_photos( $ID ) {

	$html = '';
	$attachments = get_children( array('post_parent' => $ID, 'post_type' => 'attachment', 'post_mime_type' => 'image' ));
	$thumbnail_id = get_post_meta( $ID, "_thumbnail_id", true );
	if( is_array( $attachments ) ){
		foreach( $attachments as $attachment ){
			if( $thumbnail_id <> $attachment->ID ){
				$thumbnail = wp_get_attachment_url( intval( $attachment->ID ));
				$html .= '<img src="' .$thumbnail .'" alt="写真">';
			}
		}
	}

	if( !empty( $html ) ){
		$html = '<div class="photos">' .$html .'</div>' ."\r\n";
	}

	echo $html;
}

//////////////////////////////////////////////////////
// Widget Yaerly
class birdmagazine_4toriko_yaerly_widgets extends WP_Widget {

	function __construct() {
		parent::__construct( false, $name = '年代別記事' );
	}

	function widget( $args, $instance ) {

		if( !is_year() ){
			return;
		}

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		$output = '';

		if( !empty( $html ) ){
			$html = '<ul class="yearly">' .$html .'</ul>';
		}

		$home = home_url( '/' );
		$year = date( "Y" );

		for($y = $year; $y >=1996; $y--){
$output .= <<<EOD
	<li><a href="$home/$y">{$y}年</a></li>
EOD;
		}

		if( $output ) {
			$output = '<ul class="yearly">' . $output . '</ul>';
		}

		?>
		<div class="widget">
			<?php if ( $title ) ?>
			<?php echo $before_title . $title . $after_title; ?>
			<?php echo $output; ?>
		</div>
		<?php
	}

	function update($new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['body'] = trim( $new_instance['body'] );
		return $instance;
	}

	function form($instance) {
		$title = esc_attr( $instance['title'] );
		$body = esc_attr( $instance['body'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
			<?php _e( 'タイトル:' ); ?>
			</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<?php
	}
}
add_action( 'widgets_init', create_function( '', 'register_widget( "birdmagazine_4toriko_yaerly_widgets" );' ) );

//////////////////////////////////////////////////////
// Display entry meta information
function birdmagazine_entry_meta() {
?>
	<?php if( is_archive() ) : // archive ?>
		<?php birdmagazine_4toriko_the_maker( get_the_ID(), '<div class="meta">', '</div>', false ); ?>
	<?php elseif( is_home() ): // home ?>
		<?php birdmagazine_4toriko_the_maker( get_the_ID(), '<span>', '</span>' ); ?>
		<?php birdmagazine_4toriko_the_price( get_the_ID(), '<span>', '円</span>' ); ?>

		<?php if ( comments_open() || get_comments_number() ): ?>
			<span class="icon comment"><?php comments_number( '0', '1', '%' ); ?></span>
		<?php endif; ?>
	<?php elseif( is_singular( 'maker' ) ): // single maker ?>
		<?php birdmagazine_4toriko_the_price( get_the_ID(), '<div class="meta">', ' 円</div>' ); ?>
	<?php elseif( is_single() ): // single post ?>
		<dl>
		<dt>登録日</dt><dd><time datetime="<?php the_time( 'Y-m-d' ); ?>"><?php echo get_post_time(get_option('date_format')); ?></time></dd>
		<?php birdmagazine_4toriko_the_maker( get_the_ID(), '<dt>メーカー</dt><dd>', '</dd>' ); ?>
		<?php birdmagazine_4toriko_the_price( get_the_ID(), '<dt>価格</dt><dd>', ' 円</dd>' ); ?>
		<dt>種類</dt><dd><?php the_category(', '); ?></dd>
		<?php the_tags('<dt>キーワード</dt><dd>', ', ', '</dd>'); ?>
		</dl>

		<?php if( !wp_is_mobile()){
			birdmagazine_4toriko_the_photos( get_the_ID() );
		} ?>

	<?php endif; ?>

<?php
}

//////////////////////////////////////////////////////
// Yearly Archive
function birdmagazine_4toriko_yearly ( $atts ) {

	$output = '';

	$home = home_url( '/' );
	$last_year = date("Y");

	$first_year = $last_year;
	$args = array(
		'numberposts'	=> 1,
		'orderby'	=> 'post_date',
		'order'		=> 'ASC',
	);
	$posts = get_posts( $args );
	if($posts) {

		$output .= '<div class="archive"><ul class="articles">';

		foreach ( $posts as $post ) {
			$first_year = mysql2date( 'Y', $post->post_date, true );
		}

		for($y = $last_year; $y >= $first_year; $y--){

$output .= <<<EOD
		<li><a href="$home/$y">{$y}年</a></li>
EOD;
		}
		wp_reset_postdata();

		$output .= '</ul></div>';
	}

	return $output;
}
add_shortcode( 'birdmagazine_4toriko_yearly', 'birdmagazine_4toriko_yearly' );

//////////////////////////////////////////////////////
// the attachment image at single page
function birdmagazine_4toriko_yearly_content ( $content ) {

	if( is_single() ){
		$eyecatch = get_the_post_thumbnail( get_the_ID() );
		if( $eyecatch ){
			$eyecatch = '<div class="entry-eyecatch">' .$eyecatch .'</div>';
		}

		$content = $eyecatch .$content;
	}

	return $content;
}
add_filter( 'the_content', 'birdmagazine_4toriko_yearly_content');

//////////////////////////////////////////////////////
// google analytics
function birdmagazine_4toriko_google_analytics(){
	if( !is_user_logged_in() ){
		include('google_analytics.php');
	}
}
add_action('wp_head', 'birdmagazine_4toriko_google_analytics');