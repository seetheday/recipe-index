<?php
/**
Functions to retrieve the posts, get the Title, tbe first image and 
to generate thumbnails.
Author : Simon Austin (simon@kremental.com)
Inspired by the Category Grid View Plugin by Anshul Sharma
 */
 
 if ( ! defined( 'ABSPATH' ) ) {
     exit;
 }

 require_once VRI_PLUGIN_DIR . 'includes/RecipeIndexData.php';
 
 class RecipeIndexView{
 	
    private $params = array();
    private $rioutput;
	private $riposts;
	private $ridata;
	private $size = array();
	
	
	public function __construct($atts) {
        $this->params = $atts;
        $this->ridata = new RecipeIndexData($atts);
		$this->ri_build_output();

    }
	
	 private function ri_build_output(){
	 	global $paginateVal;
                $scheme = sanitize_html_class( get_ri_option('color_scheme'), 'light' );
                $this->rioutput='<div class="riview '.$scheme.'">';
		$this->rioutput.= '<ul id="ri-ul">'."\n"; 
        //Posts loop
        foreach ($this->ridata->ri_get_posts() as $single):
                $this->rioutput .= $this->ri_build_item($single)."\n";
        endforeach;
		$this->rioutput.= '</ul>';
//		if(get_ri_option('credits')){ $this->rioutput.= '<div id="ri-credits">Powered by <a href="'.PLUGIN_URI.'" target="_blank">CGView</a></div>'; }
		$this->rioutput.= '</div>'."\n";
		$paginateVal = $this->params['paginate'];
    }

    /*
	Build each item
     */
    private function ri_build_item($single){
        $size=array();
        $size=$this->ri_get_size();
        /* Simon - Add 60px to height to allow for space below image for text */
        $size[1] += 60;

        $width  = absint( $size[0] );
        $height = absint( $size[1] );

        $riitem='<li id="ri-'.absint( $single->ID ).'" style="width:'.$width.'px;height:'.$height.'px;">';
		
        $riitem.= $this->ri_get_image($single);
		
		if(((int)$size[0]>=100||(int)$size[1]>=100))
		$riitem.= $this->ri_get_title($single);
		
		$riitem.= '</li>';
		
		$this->ri_active_post = $single->post_content;
		
        return $riitem;
    }
	
        private function ri_get_image($single){
                $ri_img = '';
                ob_start();
                ob_end_clean();
                if(get_ri_option('image_source')=='featured'){
                        if (has_post_thumbnail($single->ID )){
                                $image = wp_get_attachment_image_src(get_post_thumbnail_id( $single->ID ), 'single-post-thumbnail' );
                                $ri_img = isset( $image[0] ) ? $image[0] : '';
                        }
                        else {
                                $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $single->post_content, $matches);
                                if ( ! empty( $matches[1][0] ) ) {
                                        $ri_img = $matches [1] [0];
                                }
                        }
                }
                else {
                        $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $single->post_content, $matches);
                        if ( ! empty( $matches[1][0] ) ) {
                                $ri_img = $matches [1] [0];
                        }
                }

                if(empty($ri_img)){ //Defines a default image
                        $ri_img = get_ri_option('custom_image');
                }

                $size=array();
                $size=$this->ri_get_size();

                        if((!is_numeric($this->params['quality']))||(int)$this->params['quality']>100)
                                $this->params['quality']='75';
                //uses TimThumb to generate thumbnails on the fly
                $returnlink = ($this->params['lightbox'])? add_query_arg( array( 'ID' => absint( $single->ID ) ), VRI_PLUGIN_URL . 'includes/RecipeIndexPost.php' ) : get_permalink($single->ID);
                $thumb_url = sprintf(
                        '%1$sincludes/timthumb.php?src=%2$s&amp;h=%3$d&amp;w=%4$d&amp;zc=1&amp;q=%5$d',
                        esc_url( VRI_PLUGIN_URL ),
                        rawurlencode( $ri_img ),
                        absint( $size[1] ),
                        absint( $size[0] ),
                        absint( $this->params['quality'] )
                );
                return '<a href="'.esc_url( $returnlink ).'"'.( $this->params['lightbox'] ? ' class="ripost"' : '' ).'><img src="'.esc_url( $thumb_url ).'" alt="'.esc_attr( $single->post_title ).'" title="'.esc_attr( $single->post_title ).'"/></a>';


        }
	
        private function ri_get_title($single){
                if($this->params['title']){
                        $title_array = get_post_meta($single->ID, $this->params['title']);
                        $title = isset( $title_array[0] ) ? $title_array[0] : '';
                        if(!$title){$title = $single->post_title;}
                }
                else { $title = $single->post_title;}
                $returnlink = ($this->params['lightbox'])? add_query_arg( array( 'ID' => absint( $single->ID ) ), VRI_PLUGIN_URL . 'includes/RecipeIndexPost.php' ) : get_permalink($single->ID);
                $rifontsize=$this->ri_get_font_size();
                $line_height = $rifontsize ? 1.2 * $rifontsize : 0;
                $showtitle = esc_attr( $this->params['showtitle'] );
                $rititle='<div class="riback rinojs '.$showtitle.'"></div><div class="rititle rinojs '.$showtitle.'"><p style="font-size:'.$rifontsize.'px;line-height:'.$line_height.'px;"><a href="'.esc_url( $returnlink ).'"'.( $this->params['lightbox'] ? ' class="ripost"' : '' ).'>'.esc_html( $title ).'</a></p></div>';
                return $rititle;
        }
	
	public function display(){
        return $this->rioutput;
    }
	
	public function ri_get_size(){
		$size=array();
		switch ($this->params['size']) {
			case 'medium':
				$size=array('180','180');
				break;
			case 'large':
				$size=array('300','300');
				break;
			case 'thumbnail':
				$size=array('140','140');
				break;
			default:
				if(preg_match('/\b[0-9]{1,4}[xX][0-9]{1,4}\b/',$this->params['size']))
					$size = preg_split('/[xX]+/',$this->params['size'],-1,PREG_SPLIT_NO_EMPTY);	
				else
					$size=array('140','140');					
			}
	
	return $size;
	}
	
//Adjust fontsize according to the thumbnail size. Dont show title if either height or width < 100px	
	public function ri_get_font_size(){
		$size=array();
		$size=$this->ri_get_size();
		$rifontsize = (4/50)*(int)$size[1];
		if ($rifontsize>16)
			return 16;
		if ($rifontsize<8)
			return NULL;
		else
			return $rifontsize;
	}
	
}

 function ri_init_js(){
         if ( is_admin() ) {
                 return;
         }

         global $paginateVal;
         if ( ! isset( $paginateVal ) ) {
                 return;
         }
    echo '<script type="text/javascript">';
    echo 'var paginateVal = '.absint( $paginateVal ).';';
    echo '</script>';
    do_action('ri_init_js');
}

function get_ri_option($option) {
  return get_riview_option($option);
}

