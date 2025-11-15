<?php
/**
 * @ Recipe_Index
 * @version 1.3.1
 */
/*
Plugin Name: Visual Recipe Index
Plugin URI: http://wordpress.org/extend/plugins/recipe_index/
Description: This plugin allows for the easy creation of a recipe index.
Inspired by the Category Grid View Plugin by Anshul Sharma
Author: Kremental
Version: 1.3.0
Author URI: http://strawberriesforsupper.com/recipe-index
*/


/* Copyright 2012 Original Author: Anshul Sharma  (email : contact@anshulsharma.in)
   Copyright 2015 Author: Kremental/Simon Austin (email: simon@kremental.com)

This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'PLUGIN_VERSION' ) ) {
    define( 'PLUGIN_VERSION', '1.3.0' );
}

define( 'VRI_PLUGIN_FILE', __FILE__ );
define( 'VRI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'VRI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once VRI_PLUGIN_DIR . 'includes/RecipeIndexView.php';
require_once VRI_PLUGIN_DIR . 'includes/Settings.php';
require_once VRI_PLUGIN_DIR . 'includes/Options.php';

if ( is_admin() ) {
    add_action( 'admin_menu', 'add_ri_settings' );
}

function add_ri_settings(){
add_options_page('Settings - Visual Recipe Index','Visual Recipe Index', 'manage_options', 'visual-recipe-index', 'recipe_index_options');
}

define('PLUGIN_AUTHOR', 'Kremental');
define('AUTHOR_URI','http://strawberriesforsupper.com/');
define('PLUGIN_URI','http://strawberriesforsupper.com/recipe-index');

class RecipeIndex{
    /**
     * Render the Visual Recipe Index shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @param string|null $content Content passed to the shortcode.
     *
     * @return string
     */
    public static function recipe_index($atts, $content = null) {
            $atts = shortcode_atts(array(
                            'id' => '0',
                            'name' => '',
                            'orderby' => 'date',
                            'order' => 'desc',
                            'num' => '-1',
                            'excludeposts' => '0',
                            'offset' => '0',
                            'tags' => '',
                            'size' => 'thumbnail',
                            'quality' => '100',
                            'showtitle' => 'hover',
                            'lightbox' => '1',
                            'paginate' => '0',
                            'customfield' => '',
                            'customfieldvalue' => '',
                            'title' => ''
                    ), $atts, 'riview');

                $ri_output = new RecipeIndexView($atts);
                return $ri_output->display();



    }

}


function enqueue_ri_styles() {
        if ( is_admin() ) {
            return;
        }

        $style_path = VRI_PLUGIN_DIR . 'css/style.css';
        if ( file_exists( $style_path ) ) {
            wp_enqueue_style( 'visual-recipe-index', VRI_PLUGIN_URL . 'css/style.css', array(), filemtime( $style_path ) );
        }

 }

function enqueue_ri_scripts() {
        if ( is_admin() ) {
            return;
        }

        $scripts = array(
            'visual-recipe-index-view' => 'js/riview.js',
            'visual-recipe-index-colorbox' => 'js/jquery.colorbox-min.js',
            'visual-recipe-index-paginate' => 'js/easypaginate.min.js',
        );

        foreach ( $scripts as $handle => $relative_path ) {
            $script_path = VRI_PLUGIN_DIR . $relative_path;
            if ( file_exists( $script_path ) ) {
                wp_enqueue_script(
                    $handle,
                    VRI_PLUGIN_URL . $relative_path,
                    array( 'jquery' ),
                    filemtime( $script_path ),
                    true
                );
            }
        }
}

add_action( 'wp_enqueue_scripts', 'enqueue_ri_scripts' );
add_action( 'wp_enqueue_scripts', 'enqueue_ri_styles' );
add_action( 'wp_footer', 'ri_init_js' );
add_action( 'admin_enqueue_scripts', 'riview_admin_assets' );

function riview_admin_assets( $hook ) {
        if ( 'settings_page_visual-recipe-index' !== $hook ) {
                return;
        }

        $admin_style = VRI_PLUGIN_DIR . 'css/cgview-settings.css';
        if ( file_exists( $admin_style ) ) {
                wp_enqueue_style( 'visual-recipe-index-admin', VRI_PLUGIN_URL . 'css/cgview-settings.css', array(), filemtime( $admin_style ) );
        }

        $admin_script = VRI_PLUGIN_DIR . 'js/recipe_index_options.js';
        if ( file_exists( $admin_script ) ) {
                wp_enqueue_script( 'visual-recipe-index-admin', VRI_PLUGIN_URL . 'js/recipe_index_options.js', array( 'jquery' ), filemtime( $admin_script ), true );
        }
}

add_shortcode( 'riview', array('RecipeIndex', 'recipe_index') );

/** Create Admin Menu options - function not in Cat Grid View */
function recipe_index_options() {

        if ( !current_user_can( 'manage_options' ) )  {
                wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }


?>
        <div class="wrap">
        <table>
        <tr><td>
        <h1>Visual Recipe Index</h1>This plugin will create an automatically updating recipe index with pictures.<br>Note that this plugin requires the feature image is set for each post.  once you have created your visual recipe index it is easy to identify which posts do not have a feature image set.
        <h2>Step 1: </h2>create a blank page where you want to post your automatically updating visual recipe index.  <a href="<?php echo esc_url( admin_url('post-new.php?post_type=page&post_title=Recipe Index#content-html') ); ?>" target="_blank">Click here</a> to open up a new window to do this
<h2>Step 2: </h2>Choose your options below
        </td>
        <?php $signup_image = VRI_PLUGIN_DIR . 'includes/Sign-up.png'; ?>
        <?php if ( file_exists( $signup_image ) ) : ?>
        <td><a href="<?php echo esc_url( 'http://kremental.com/visual-recipe-index' ); ?>"><img src="<?php echo esc_url( VRI_PLUGIN_URL . 'includes/Sign-up.png' ); ?>" alt="Sign up for updates and exclusive early release pricing for the pro version"></a></td>
        <?php endif; ?>
        </tr>
        </table>

        </div>

    <!-- SHORTCODE GENERATOR -->
    <div id="ri_sc">
    <form name="ri_sc" onsubmit="return false;">
        <table id="shortcode_options" class="form-table">
            <tr>
                <td><?php _e("Choose Categories to display in Visual Recipe Index","riview"); /* need to create category chooser here from list above */ ?><br />
	<select name="categories" id="ri_categories" multiple="multiple">
<?php
	/** Choose from a list of categories to include in recipe index */	
	$args = array(
	  'orderby' => 'name',
	  'order' => 'ASC'
	  );
	$categories = get_categories($args);
	  foreach($categories as $category) { 
	    echo '<option value='.$category->term_id.'>'. $category->name."</option>\n";
	  } 
?>
		</select>
<script type="text/javascript">
var idcat=new Array();
<?php // pass in id and category name into javascript array so I can access the lookup table on the page
          foreach($categories as $category) {
	    echo "idcat[$category->term_id] = '".addslashes($category->name)."';\n";
	  }
?>
</script>
                    <br />
                               <span> <?php _e("Choose multiple categories by holding down the Ctrl key","riview"); ?></span></td>
                               
</div>
                <td><?php _e("Thumbnail Size","riview"); ?><br />
                <input type="text" size="4" name="sizew" value=""/><?php _e(" X ","riview"); ?><input type="text" size="4" name="sizeh" value=""/><br />
                <span><?php _e("Width (px) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Height (px)","riview"); ?></span><br />
                <select name='sizes'>
                                                        <option value='thumbnail'><?php _e("Thumbnail (Default)","riview"); ?></option>
                                                        <option value='medium'><?php _e("Medium","riview"); ?></option>
                                                        <option value='large'><?php _e("Large","riview"); ?></option>
                                                        <option value='other'><?php _e("Other (Please Specify)","riview"); ?></option>
                                </select>

                                <span><?php _e("Specify the dimensions of the generated thumbnails for each post","riview"); ?><br></span>
				<span>Support the Author with a link:<input type="checkbox" name="credit" id="credit" class="checkbox" checked></span>
		</td>
		<br /></div>
            </tr>
         </table>
<h2>Step 3:</h2> Click "Generate Visual Recipe Index code", highlight the generated code, and copy it into your clipboard.  Important - you may have to scroll within the box to get all of the code.
        <table class="form-table">
        <tr>
                        <th scope="row">
             <p class="controls alignright">
                        <input type="submit" class="button-secondary" name="submit_shortcode" value="<?php _e("Generate Visual Recipe Index code","riview"); ?>"/>
            </p>
            <p class="controls alignright">
                        <input type="submit" class="button-secondary" name="reset_shortcode" value="<?php _e("Reset","riview"); ?>" />
            </p></th>
                        <td>
                                <textarea id="riview_shortcode" rows="4" cols="60" name="riview_shortcode" class="code">Your automatically updating recipe index code will be generated here.  After it is generated, be sure to scroll down to copy all of it.</textarea>
                        </td>
                </tr>
        </table>
    </form>
        </div>
	<div class="wrap">
<h2>Step 4: </h2>Return to the window you created in Step 1, ensure you are in text mode, and paste the code you created in Step 3.  Publish the page and note the URL.
<h2>Step 5: </h2>Check your new recipe index page for posts that don't have an image showing.  Edit those pages and set the feature image.
<h2>Step 6:</h2>Modify the <a href="<?php echo esc_url( admin_url('nav-menus.php') ); ?>" target="_blank">Apperance->Menu</a> to make a link to your new menu appear in the nav
	</div>
<?php

}
?>
