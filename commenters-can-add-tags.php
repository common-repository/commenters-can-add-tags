<?php
/*
Plugin Name: Commenters can add tags
Plugin URI: http://wordpress.org/extend/plugins/XXXXXXXXX
Description: Allow commenters to add tags (It will be considered so each word preceded by #).

Version: 0.2
Author: Raúl Antón Cuadrado
Author URI: http://comunicacionextendida.com
Text Domain: commenters-can-add-tags
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
*/
/*  Copyright 2015 Raúl Antón Cuadrado  (email : raulanton@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! function_exists('antonat_install') ) :
function antonat_install() {
	$opt_name = 'wp_antonat_prefix';  
	add_option($opt_name, "#");

}

register_activation_hook(__FILE__,'antonat_install');

endif;


function antonat_load_plugin_textdomain() {
    load_plugin_textdomain( 'commenters-can-add-tags', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'antonat_load_plugin_textdomain' );

/*
*
* ES: 
* EN: 
* FR: 
*
* TO FIX: 
*/
if ( ! function_exists('antonat_add_tags') ) :


function antonat_add_tags($comment_id, $approval_status=" ")
{
$comment = get_comment( $comment_id )->comment_content;
$semilla_id=get_comment( $comment_id )->comment_post_ID;

$prefix_name = 'wp_antonat_prefix'; /*Recupera el qualifier o prefijo de tag*/
$qualifier = get_option( $prefix_name );



//Is there any new tag in the comment?
if (preg_match_all('/'.$qualifier.'(\w+)/',$comment,$tags)>0) {


	//Get current tags in a comma separated list

	$posttags = get_the_tags($semilla_id);
	if ($posttags) {
  	
	foreach($posttags as $tag) {
    	$keywords .= $tag->name . ", "; 
  	}
	}



	//Add new tags to the tag list

	$i=0;


        foreach ($tags[1] as $tag) {
            $count = count($tags[1]);
            $keywords .= "$tag";
            $i++;
            if ($count > $i) $keywords .= ", ";
        }


	//update post with the new tag list
	$post = array(
	'ID' => $semilla_id,
	'tags_input' => $keywords
         ); 
	wp_update_post($post);

}
}	

add_action('comment_post', 'antonat_add_tags');

endif;

/*
*
* ES: Adminstración y opciones 
* EN: 
* FR: 
* 
* 
*/
if ( ! function_exists('antonat_admin') ) :
function antonat_admin() {
    add_options_page( 
	'Opciones de Commenters can add tags', 
	'Commenters can add tags', 
	'manage_options', 
	'anton_commenterscanaddtags', 
	'antonat_admin_options' );
}



add_action( 'admin_menu', 'antonat_admin' );

endif;


/*
*
*
*
*/

function antonat_admin_options(){
if(!current_user_can('manage_options')) {
	wp_die( "Pequeño padawan... debes utilizar la fuerza para entrar aquí." );
}


    $hidden_field_name = 'wp_antonat_hidden';
 
    $prefix_name = 'wp_antonat_prefix';
    $prefix_field_name = 'wp_antonat_prefix';
    $prefix_val = get_option( $prefix_name ); 

 
    if( isset($_POST[ $hidden_field_name ]) 
		&& 
	$_POST[ $hidden_field_name ] == 'antonat_updated') {


 	$prefix_val = $_POST[ $prefix_field_name ];
        update_option( $prefix_name, $prefix_val );


	        echo "<div class='updated'><p><strong>";
		echo "¡Ok esos cambios!"; 	
	  	echo "</strong></p></div>";

         } ?>

        <div class="wrap">
        <h2> Commenters can add tags Menu</h2>
 
        
 
        <form name="form1" method="post" action="">

            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="antonat_updated">
            <p>
                <?php _e('What do you want to use as tag prefix? (It is # par default, like in Twitter or Instagram) : ', 'commenters-can-add-tags'); ?>
                <input type="text" name="<?php echo $prefix_field_name; ?>" value="<?php echo $prefix_val; ?>" size="20" />
            </p>

		

            <p class="submit">
                <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save changes', 'commenters-can-add-tags');?>" />
            </p>

        </form>
    </div>
<?php } ?>