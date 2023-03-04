<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elementor Properties Widget.
 *
 * @since 2.0
 */
class Plugin_Devs_Elementor_Booking_Map_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve widget name.
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'pd_houzez_elementor_booking-map-widget';
	}

	/**
	 * Get widget title.
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Booking Map', 'houzez-theme-functionality' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * @since 2.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'houzez-elements' );
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Settings', 'houzez-theme-functionality' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'booking_id',
			array(
				'label'       => esc_html__( 'Booking ID', 'houzez-theme-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'description' => 'Enter your Booking ID',
				'default'     => '2092457',
			)
		);

		/*
		$this->add_control(
			'latitude',
			[
				'label'     => esc_html__( 'Latitude', 'houzez-theme-functionality' ),
				'type'      => Controls_Manager::TEXT,
				'description' => 'Enter Latitude',
				'default' => '',
			]
		);

		$this->add_control(
			'longitude',
			[
				'label'     => esc_html__( 'longitude', 'houzez-theme-functionality' ),
				'type'      => Controls_Manager::TEXT,
				'description' => 'Enter Longitude',
				'default' => '',
			]
		);*/

		$this->add_control(
			'map_zoom',
			array(
				'label'       => esc_html__( 'Map Zoom', 'houzez-theme-functionality' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => 'Enter Map Zoom. (e.g.: 10). Default is 18',
				'default'     => '18',
			)
		);

		$this->add_control(
			'map_width',
			array(
				'label'       => esc_html__( 'Map Width', 'houzez-theme-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'description' => 'Enter Map Width. (e.g.: 100% or 590). Default is 100%',
				'default'     => '100%',
			)
		);

		$this->add_control(
			'map_height',
			array(
				'label'       => esc_html__( 'Map Height', 'houzez-theme-functionality' ),
				'type'        => Controls_Manager::TEXT,
				'description' => 'Enter Map Height. (e.g.: 100% or 590). Default is 590',
				'default'     => '590',
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 2.0
	 * @access protected
	 */
	protected function render() {

		$settings   = $this->get_settings_for_display();
		$latitude   = $longitude = $booking_id = $address = $landmark = '';
		$map_width  = '100%';
		$map_height = '590';
		$map_zoom   = '18';

		$landmark  = get_post_meta( get_the_ID(), 'fave_property_address', true );
		$latitude  = get_post_meta( get_the_ID(), 'houzez_geolocation_lat', true );
		$longitude = get_post_meta( get_the_ID(), 'houzez_geolocation_long', true );

		if ( ! empty( $settings['booking_id'] ) ) {
			$booking_id = $settings['booking_id'];
		}

		if ( ! empty( $settings['map_width'] ) ) {
			$map_width = $settings['map_width'];
		}

		if ( ! empty( $settings['map_height'] ) ) {
			$map_height = $settings['map_height'];
		}

		if ( ! empty( $settings['map_zoom'] ) ) {
			$map_zoom = intval( $settings['map_zoom'] );
		}

		if ( get_field( 'acfp_project_name' ) ) {
			$address = get_field( 'acfp_project_name' );
		}
		?>
		<ins
			class="bookingaff"
			data-aid="<?php echo $booking_id; ?>"
			data-target_aid="<?php echo $booking_id; ?>"
			data-prod="map"
			data-width="<?php echo $map_width; ?>"
			data-height="<?php echo $map_height; ?>"
			data-lang="ualng"
			data-dest_id="0"
			data-dest_type="landmark"
			data-latitude="<?php echo $latitude; ?>"
			data-longitude="<?php echo $longitude; ?>"
			data-landmark_name="<?php echo $landmark; ?>"
			data-mwhsb="0"
			data-address="<?php echo $address; ?>"
			data-zoom="<?php echo $map_zoom; ?>"
		>
			<!-- Anything inside will go away once widget is loaded. -->
				<a href="//www.booking.com?aid=<?php echo $booking_id; ?>">Booking.com</a>
		</ins>
		<script type="text/javascript">
			(function(d, sc, u) {
			var s = d.createElement(sc), p = d.getElementsByTagName(sc)[0];
			s.type = 'text/javascript';
			s.async = true;
			s.src = u + '?v=' + (+new Date());
			p.parentNode.insertBefore(s,p);
			})(document, 'script', '//aff.bstatic.com/static/affiliate_base/js/flexiproduct.js');
		</script>
		<?php

	}

}

Plugin::instance()->widgets_manager->register_widget_type( new Plugin_Devs_Elementor_Booking_Map_Widget() );
