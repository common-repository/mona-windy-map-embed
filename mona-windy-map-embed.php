<?php
/*
*
* Plugin Name: Mona Windy map Embed
* Plugin URI: https://mona-media.com/project/mona-windy-embed-wordpress-plugin/
* Description: Embed map for windy.com service.
* Author: Mona Media
* Author URI: https://mona-media.com/
* Version: 1.0
* Text Domain: mona-windy-map-embed
*
*/
defined('ABSPATH') or die('No script kiddies please!');

if (!class_exists('Mona_Windy_Embed')){
	class Mona_Windy_Embed {	
		protected $api_default = 'default',
		$api_url = 'https://embed.windy.com/embed2.html';
	
		/**
		 * Class Construct
		 */
		public function __construct(){	
			$this->url = trailingslashit(plugin_dir_url(__FILE__));
			$this->dir = trailingslashit(plugin_dir_path(__FILE__));
			
			//shortcode
			add_shortcode('mona_windy', array($this, 'mona_shortcode_windy'));	
		}
		
		/**
		 * Function settings
	 	 */	
		function get_wind_type($data = ''){
			$default = array(
				'kt',
				'm/s',
				'km/h',
				'mph',
				'bft',
			);
			
			return (in_array($data, $default)) ? $data : $this->api_default;
		}
		
		function get_temp_type($data = ''){
			$default = array(
				'°C',
				'°F',
			);
			
			return (in_array($data, $default)) ? $data : $this->api_default;
		}
		
		function get_windy_time($data = ''){
			$default = array(
				'12',
				'24',
			);
			
			return (in_array($data, $default)) ? $data : false;
		}
		
		/**
		 * Shortcode
	  	 */
		function mona_shortcode_windy($atts = array()){
			ob_start();
			
			extract( 
				shortcode_atts( 
					array(
						'lat' => 10.77707,
						'lng' => 106.65482,
						'zoom' => '6',
						'height' => '100px',
						'width' => '100%',
						'wind' => $this->api_default,
						'temp' => $this->api_default,
						'time' => false,
						'marker' => false,
						'pressure' => false,
						'detail' => false,
					), 
					$atts, 
					'mona_windy'
				)			
			);
			
			$this->mona_render_windy(
				array(
					'lat' => $lat,
					'lng' => $lng,
					'zoom' => $zoom,
					'height' => $height,
					'width' => $width,
					'wind' => $wind,
					'temp' => $temp,
					'time' => $time,
					'marker' => $marker,
					'pressure' => $pressure,
					'detail' => $detail,
				)
			);
			
			return ob_get_clean();
		}
		
		/**
		 * Render
		 */
		function mona_render_windy($args = array()){
			$lat = (float) @$args['lat'];
			$lng = (float) @$args['lng'];
			$zoom = (int) @$args['zoom'];
			$width = @$args['width'];
			$height = @$args['height'];
			$wind = $this->get_wind_type(@$args['wind']);
			$temp = $this->get_temp_type(@$args['temp']);
			$time = $this->get_windy_time(@$args['time']);
			$marker = (bool) @$args['marker'];
			$pressure = (bool) @$args['pressure'];
			$detail = (bool) @$args['detail'];
			
			$queries = array(
				'lat' => $lat,
				'lon' => $lng,
				'detailLat' => $lat,
				'detailLon' => $lng,
				'zoom' => $zoom,
				'radarRange' => -1,
				'level' => 'surface',
				'overlay' => 'wind',
				'menu' => '',
				'type' => 'map',
				'location' => 'coordinates',
				'message' => true,
				'calendar' => $time,
				'metricWind' => $wind,
				'metricTemp' => $temp,
			);
			
			if($pressure){
				$queries['pressure'] = true;
			}
			
			if($detail){
				$queries['detail'] = true;
			}
			
			if($marker){
				$queries['marker'] = true;
			}
			
			$url = $this->api_url.'?'.http_build_query($queries);
			
			echo '<div class="mona-windy-map" style="width: '.$width.';height: '.$height.';">';
			echo '<iframe width="100%" height="100%" src="'.$url.'" frameborder="0"></iframe>';
			echo '</div>';
		}
	}
	new Mona_Windy_Embed();
}