<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
use EDD\Admin\List_Table;

/**
 * Top_Five_Customers_List_Table class.
 *
 * @since 3.0
 */
class Custom_sources extends List_Table {

	/**
	 * Get things started
	 *
	 * @since 1.5
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		parent::__construct( array(
			'singular' => 'report-gateway',
			'plural'   => 'report-gateways',
			'ajax'     => false,
		) );
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 2.5
	 * @access protected
	 *
	 * @return string Name of the primary column.
	 */
	protected function get_primary_column_name() {
		return 'source';
	}

	/**
	 * Render each column.
	 *
	 * @since 1.5
	 *
	 * @param array $item Contains all the data of the downloads
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Column names.
	 *
	 * @since 3.0
	 *
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		return array(
			'source'  => __( 'Source', 'easy-digital-downloads' ),
		);
	}

	/**
	 * Build all the reports data
	 *
	 * @since 1.5
	 * @return array All the data for customer reports
	 */
	public function get_data() {
        global $wpdb;
		$reports_data = array();
		$sources      = DLM_HDYHAU_OPTIONS;
		$placeholders = implode( ', ', array_fill( 0, count( $sources ), '%s' ) );

		$query = "SELECT meta_id, meta_value FROM $wpdb->edd_ordermeta WHERE meta_value NOT IN ($placeholders)";
		$sources = $wpdb->get_results( $wpdb->prepare( $query, $sources ), ARRAY_A  );

		foreach ( $sources as $source ) {
			
			$reports_data[] = array(
				'ID'           => $source['meta_id'],
				'source'  => $source['meta_value'],
			);
		}

		return $reports_data;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @since 1.5
	 * @uses EDD_Gateway_Reports_Table::get_columns()
	 * @uses EDD_Gateway_Reports_Table::get_sortable_columns()
	 * @uses EDD_Gateway_Reports_Table::reports_data()
	 * @return void
	 */
	public function prepare_items() {
		$columns               = $this->get_columns();
		$hidden                = array(); // No hidden columns
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $this->get_data();
	}

	/**
	 * Return empty array to disable sorting.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		return array();
	}

	/**
	 * Return empty array to remove bulk actions.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array();
	}

	/**
	 * Hide pagination.
	 *
	 * @since 3.0
	 *
	 * @param string $which
	 */
	protected function pagination( $which ) {

	}

	/**
	 * Hide table navigation.
	 *
	 * @since 3.0
	 *
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {

	}
}