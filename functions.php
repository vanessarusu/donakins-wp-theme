<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

/**
 * Dequeue the Storefront Parent theme core CSS
 */

function sf_child_theme_dequeue_style() {
    wp_dequeue_style( 'storefront-style' );
    wp_dequeue_style( 'storefront-woocommerce-style' );
}

add_filter( 'woocommerce_enqueue_styles', 'jk_dequeue_styles' );

function jk_dequeue_styles( $enqueue_styles ) {
    unset( $enqueue_styles['woocommerce-general'] );    // Remove the gloss
    unset( $enqueue_styles['woocommerce-layout'] );     // Remove the layout
    unset( $enqueue_styles['woocommerce-smallscreen'] );    // Remove the smallscreen optimisation
    return $enqueue_styles;
}

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ) {
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array( 'chld_thm_cfg_parent','storefront-style','storefront-icons','storefront-jetpack-widgets','storefront-woocommerce-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 20 );

// END ENQUEUE PARENT ACTION

// THEME SPECIFIC ASSETS - CSS & FONTS
function donakins_assets() {
    wp_enqueue_style( 'adobe-fonts', 'https://use.typekit.net/sbk4nsm.css', false ); 
    wp_enqueue_style( '_donakins_stylesheet', trailingslashit( get_stylesheet_directory_uri() ) . 'dist/css/bundle.css', array(), '1.0.0', 'all' );
    wp_enqueue_script( '_donakins_script', trailingslashit( get_stylesheet_directory_uri() ) . 'dist/js/scripts.js', array(), '1.0.0', 'all' );
}

add_action('wp_enqueue_scripts', 'donakins_assets');

// CUSTOMIZE HEADER - re-arrange ordering of stuff & redefine header

add_action( 'init', 'remove_header');
add_action( 'init', 'new_search_cart_module');

function remove_header() {
    remove_action( 'storefront_header','storefront_product_search', 40 );
    remove_action( 'storefront_header','storefront_header_cart', 60 );
}

function new_search_cart_module() {
    add_action( 'storefront_header','storefront_product_search', 60 );
    add_action( 'storefront_header','storefront_header_cart', 55 );
}

// custom cart function for header to add my account link
if ( ! function_exists ( 'storefront_header_cart' ) ) {
    function storefront_header_cart() {
        if ( storefront_is_woocommerce_activated() ) {
            if ( is_cart() ) {
                $class = 'current-menu-item';
            } else {
                $class = '';
            }
        ?>
        <ul id="site-header-cart" class="site-header-cart menu">
            <li class="<?php echo esc_attr( $class ); ?>">
                <?php storefront_cart_link(); ?>
            </li>
            <li>
                <?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
            </li>
            <li class="my-account-link">
                <?php if ( is_user_logged_in() ) { ?>
                    <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('My Account','woothemes'); ?>"><?php _e('','woothemes'); ?></a>
                <?php } 
                else { ?>
                    <a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" title="<?php _e('Login / Register','woothemes'); ?>"><?php _e('','woothemes'); ?></a>
                <?php } ?>
            </li>
        </ul>
        <?php
        }
    }
}

// redefine primary navigation and add markup for hamburger menu
if ( ! function_exists( 'storefront_primary_navigation' ) ) {
	/**
	 * Display Primary Navigation
	 *
	 * @since  1.0.0
	 * @return void
	 */
	function storefront_primary_navigation() {
		?>
		<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_html_e( 'Primary Navigation', 'storefront' ); ?>">
		<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span></span><span></span><span></span></button>
			<?php
			wp_nav_menu(
				array(
					'theme_location'  => 'primary',
					'container_class' => 'primary-navigation',
				)
			);

			wp_nav_menu(
				array(
					'theme_location'  => 'handheld',
					'container_class' => 'handheld-navigation',
				)
			);
			?>
		</nav><!-- #site-navigation -->
		<?php
	}
}

remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);

function loop_title() {
    global $post;

    echo '<h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . get_the_title() . '</h2>';
    
    $terms = get_the_terms( $post->ID, 'product_cat' );
    if ( $terms && ! is_wp_error( $terms ) ) :
        if ( ! empty( $terms ) ) {
            foreach($terms as $item) {
                if($item->parent) {
                    echo '<span class="sub-category-name">' . $item->name . '</span>';
                }
            }
        }?>
    <?php endif;?>
<?php }

function custom_shop_page_header() {

    if(is_shop() || is_product_category() ) {
        $shop_page_id = wc_get_page_id( 'shop' );
        $thumb = get_the_post_thumbnail_url($shop_page_id);
        ?>
            <div class="custom-shop-background-image" style="background-image: url('<?php echo $thumb;?>')"></div>
        <?php
    }   
}

add_action('woocommerce_shop_loop_item_title', 'loop_title', 10); 
add_action( 'storefront_before_content', 'custom_shop_page_header', 10 );

function custom_footer() {
    echo do_shortcode('[fl_builder_insert_layout id="173"]');
}

add_action('storefront_before_footer', 'custom_footer');

// move sidebar out of normal flow
if ( ! function_exists( 'storefront_before_content' ) ) {
    /**
     * Before Content
     * Wraps all WooCommerce content in wrappers which match the theme markup
     *
     * @since   1.0.0
     * @return  void
     */
    function storefront_before_content() {
        do_action( 'storefront_sidebar' );
        ?>
        <div id="primary" class="content-area">
            <main id="main" class="site-main" role="main">
        <?php
    }
}

// remove sidebar
if ( ! function_exists( 'storefront_after_content' ) ) {
    /**
     * After Content
     * Closes the wrapping divs
     *
     * @since   1.0.0
     * @return  void
     */
    function storefront_after_content() {
        ?>
        </main><!-- #main -->
        </div><!-- #primary -->
        <?php
    }
}

// custom colourswatch widget
function add_colorswatch() {
    $colorswatch = get_field('field_5ed15a3dd7a79');
    $label = get_field_object('field_5ed15a3dd7a79')['label'];
    $description = get_field_object('field_5ed15a3dd7a79')['instructions'];

    if( have_rows('field_5ed15a3dd7a79') ) {
        echo '<div class="colorswatch">
                <h2>' . $label . '</h2>
                <p>' . $description . '</p>
                <ul class="colorswatch-container">';
                    foreach( $colorswatch as $color ){
                        echo '<li style="background:' . $color . '"></li>';
                    }
        echo '</ul></div>';
    }
}

add_action('woocommerce_single_product_summary', 'add_colorswatch', 60);
add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

function new_loop_shop_per_page( $cols ) {
  // $cols contains the current number of products per page based on the value stored on Options -> Reading
  // Return the number of products you wanna show per page.
  $cols = 50;
  return $co;
}

// remove optional checkout form fields
function wc_remove_checkout_fields( $fields ) {

    // Billing fields
    unset( $fields['billing']['billing_company'] );
    unset( $fields['billing']['billing_phone'] );

    // Shipping fields
    unset( $fields['shipping']['shipping_company'] );
    unset( $fields['shipping']['shipping_phone'] );

    return $fields;
}

add_filter( 'woocommerce_checkout_fields', 'wc_remove_checkout_fields' );

?>


