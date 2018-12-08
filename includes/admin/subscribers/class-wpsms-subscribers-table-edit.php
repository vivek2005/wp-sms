<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

//Edit Groups Class
class WP_SMS_Subscribers_Subscribers_Table_Edit {

	/**
	 * Wordpress Database
	 *
	 * @var string
	 */
	public $db;

	/**
	 * Wordpress Table prefix
	 *
	 * @var string
	 */
	protected $tb_prefix;

	/**
	 * WP_SMS_Subscribers_Subscribers_Table_Edit constructor.
	 */
	public function __construct() {
		global $wpdb, $table_prefix;

		$this->db        = $wpdb;
		$this->tb_prefix = $table_prefix;

		add_action( 'admin_enqueue_scripts', array( &$this, 'admin_assets' ), 11 );
		add_action( 'wp_ajax_wp_sms_edit_subscriber', array( $this, 'wp_sms_edit_subscriber' ) );

	}

	function wp_sms_edit_subscriber() {
		//set Actiom Values
		$subscriber_id = isset( $_GET['subscriber_id'] ) ? $_GET['subscriber_id'] : null;

		//Load subscriber
		$subscriber = \WP_SMS\Newsletter::get_subscriber( $subscriber_id );
		$groups     = \WP_SMS\Newsletter::get_groups();

		$html = '<form action="" method="post">
					<input type="hidden" name="ID" value="' . $subscriber_id . '" />
					    <table>
					        <tr>
					            <td style="padding-top: 10px;">
					                <label for="wp_subscribe_name"
					                       class="wp_sms_subscribers_label">' . __( 'Name', 'wp-sms' ) . '</label>
					                       <input type="text" id="wp_subscribe_name" name="wp_subscribe_name"
                           value="' . $subscriber->name . '" class="wp_sms_subscribers_input_text" />
							</td>
							</tr>
					        <tr>
					            <td style="padding-top: 10px;">
					                <label for="wp_subscribe_mobile"
					                       class="wp_sms_subscribers_label">' . __( 'Mobile', 'wp-sms' ) . '</label>
                           <input type="text" name="wp_subscribe_mobile" id="wp_subscribe_mobile"
                           value="' . $subscriber->mobile . '" class="wp_sms_subscribers_input_text code" />
							</td>
							</tr>';
		if ( $groups ) {
			$html .= '<tr>
					  <td style="padding-top: 10px;">
                      <label for="wpsms_group_name"
					                       class="wp_sms_subscribers_label">' . __( 'Group', 'wp-sms' ) . '</label>
                   <select name="wpsms_group_name" id="wpsms_group_name" class="wp_sms_subscribers_input_text code">';
			foreach ( $groups as $items ) {
				if ( $subscriber->group_ID == $items->ID ) {
					$html .= '<option value="' . $items->ID . '" selected="selected">' . $items->name . '</option>';
				} else {
					$html .= '<option value="' . $items->ID . '">' . $items->name . '</option>';
				}
			}
			$html .= ' </select>
	                    </td>
	                    </tr>';
		} else {
			$html .= '<tr>
                      <td>' . sprintf( __( 'There is no group! <a href = "%s" > Add</a > ', 'wp-sms' ), 'admin.php?page=wp-sms-subscribers-group' ) . '</td>
                      </tr>';
		}

		$html .= '<tr>
                <td>
                <label for="wpsms_subscribe_status"
					                       class="wp_sms_subscribers_label">' . __( 'Status', 'wp-sms' ) . '</label>
                    <select name="wpsms_subscribe_status" id="wpsms_subscribe_status" class="wp_sms_subscribers_input_text code" >';
		if ( $subscriber->status == 0 ) {
			$html .= '<option value="1">' . __( 'Active', 'wp-sms' ) . '</option>';
			$html .= '<option value="0" selected="selected">' . __( 'Deactive', 'wp-sms' ) . '</option>';
		} else {
			$html .= '<option value="1" selected="selected">' . __( 'Active', 'wp-sms' ) . '</option>';
			$html .= '<option value="0">' . __( 'Deactive', 'wp-sms' ) . '</option>';
		}
		$html .= '</select>
                </td>
            </tr>';

		$html .= '<tr>
				    <td colspan="2" style="padding-top: 20px;" >
				        <input type="submit" class="button-primary" name="wp_update_subscribe"
				               value="' . __( 'Update', 'wp-sms' ) . '" />
				    </td>
				</tr>
				</table>
			</form>';

		echo $html;
		wp_die(); // this is required to terminate immediately and return a proper response
	}


	public function admin_assets( $hook ) {

		wp_register_script( 'wp-sms-edit-subscriber', WP_SMS_URL . 'assets/js/edit-subscriber.js', array( 'jquery' ), null, true );

		//Set Values
		if ( 'sms_page_wp-sms-subscribers' != $hook ) {
			// Only applies to WPS-Ar-Log page
			return;
		}
		wp_enqueue_script( 'wp-sms-edit-subscriber' );

		$protocol = isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://';

		$tb_show_url = add_query_arg(
			array(
				'action' => 'wp_sms_edit_subscriber'
			),
			admin_url( 'admin-ajax.php', $protocol )
		);

		$ajax_vars = array(
			'tb_show_url' => $tb_show_url,
			'tb_show_tag' => __( 'Edit Subscriber', 'wp-sms' )
		);
		wp_localize_script( 'wp-sms-edit-subscriber', 'wp_sms_edit_subscribe_ajax_vars', $ajax_vars );
	}

}

new WP_SMS_Subscribers_Subscribers_Table_Edit();
