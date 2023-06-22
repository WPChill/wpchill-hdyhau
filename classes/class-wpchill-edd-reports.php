<?php

/**
 * DLM_Logging_List_Table class.
 *
 * @extends WP_List_Table
 */
class WPChill_EDD_Reports{
	/**
	 * __construct function.
	 *
	 * @access public
	 */
	public function __construct() {
		
		add_action( 'edd_reports_init', array( $this, 'reports_init' ), 99 );
	}


	public function reports_init( $reports ){

		$tables = array(
			'sources_count_table',
			'sources_custom_table',
		);

		$charts = array(
			'sources_count_chart',
		);

		$reports->add_report( 'wpchill-hdyhau', array(
			'label'     => __( 'Sources Stats & Count', 'easy-digital-downloads' ),
			'icon'      => 'image-filter',
			'priority'  => 20,
			'endpoints' => array(
				'tables' => $tables,
				'charts' => $charts,
			),
			'filters'   => array( ),
		) );

		$reports->register_endpoint( 'sources_count_table', array(
			'label' => __( 'Sources Stats', 'easy-digital-downloads' ) ,
			'views' => array(
				'table' => array(
					'display_args' => array(
						'class_name' => 'WPChill_Sources_Stats_List_Table',
						'class_file' => plugin_dir_path( DLM_HDYHAU_FILE ) . 'classes/class-wpchill-stats-list-table.php',
					),
				),
			),
		) );

		$reports->register_endpoint( 'sources_custom_table', array(
			'label' => __( 'Custom Sources', 'easy-digital-downloads' ) ,
			'views' => array(
				'table' => array(
					'display_args' => array(
						'class_name' => 'WPChill_Custom_Sources_List_Table',
						'class_file' => plugin_dir_path( DLM_HDYHAU_FILE ) . 'classes/class-wpchill-custom-sources-list-table.php',
					),
				),
			),
		) );

		$reports->register_endpoint( 'sources_count_chart', array(
			'label' => __( 'Sources Chart', 'easy-digital-downloads' ) ,
			'views' => array(
				'chart' => array(
					'data_callback' => function(){

						$sources = $this->get_sources_data();

						return array(
							'sales' => array_values( $sources  ),
						);
					},
					'type' => 'pie',
					'options' => array(
						'cutoutPercentage' => 0,
						'datasets'         => array(
							'sales' => array(
								'label'           => __( 'Sales', 'easy-digital-downloads' ),
								'backgroundColor' => array(
									'rgb(255,0,0)',
									'rgb(9,149,199)',
									'rgb(8,189,231)',
									'rgb(137,163,87)',
									'rgb(27,98,122)',
								),
							),
						),
						'labels' => DLM_HDYHAU_OPTIONS,
					),
				),
			)
		) );
	}

	public function get_sources_data() {
		global $wpdb;
		$wpdb->edd_ordermeta = "{$wpdb->prefix}edd_ordermeta";

		$query = "SELECT `meta_value` ,COUNT(`meta_value`) as count FROM $wpdb->edd_ordermeta WHERE `meta_key`='hdyhau-reason' GROUP BY `meta_value`";
		$count = $wpdb->get_results( $query, ARRAY_A );
		$reports_data = wp_list_pluck( $count, 'count', 'meta_value' );

		return $reports_data;
	}

}
