<?php

namespace WP_SMS\Api\V1;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_SMS\Option;
use WP_SMS\RestApi;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * @category   class
 * @package    WP_SMS_Api
 * @version    1.0
 *
 */
class Newsletter extends RestApi
{
    public function __construct()
    {
        // Register routes
        add_action('rest_api_init', array($this, 'register_routes'));

        parent::__construct();
    }

    /**
     * Register routes
     */
    public function register_routes()
    {
        register_rest_route($this->namespace . '/v1', '/newsletter', array(
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array($this, 'subscribe_callback'),
                'args'                => array(
                    'name'          => array(
                        'required' => true,
                    ),
                    'mobile'        => array(
                        'required' => true,
                    ),
                    'group_id'      => array(
                        'required' => false,
                        'type'     => array('integer', 'array'),
                    ),
                    'custom_fields' => array(
                        'required' => false
                    )
                ),
                'permission_callback' => '__return_true'
            ),
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array($this, 'get_subscribers_callback'),
                'args'                => array(
                    'group_id' => array(
                        'required' => false,
                    )
                ),
                'permission_callback' => array($this, 'getSubscribersPermission')
            )
        ));

        register_rest_route($this->namespace . '/v1', '/newsletter/unsubscribe', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'unsubscribe_callback'),
            'args'                => array(
                'name'   => array(
                    'required' => true,
                ),
                'mobile' => array(
                    'required' => true,
                )
            ),
            'permission_callback' => '__return_true'
        ));

        register_rest_route($this->namespace . '/v1', '/newsletter/verify', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array($this, 'verify_subscriber_callback'),
            'args'                => array(
                'name'       => array(
                    'required' => true,
                ),
                'mobile'     => array(
                    'required' => true,
                ),
                'activation' => array(
                    'required' => true,
                )
            ),
            'permission_callback' => '__return_true'
        ));
    }

    /**
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function subscribe_callback(WP_REST_Request $request)
    {
        // Get parameters from request
        $params       = $request->get_params();
        $number       = self::convertNumber($params['mobile']);
        $customFields = $request->get_param('custom_fields');

        $group_id = isset($params['group_id']) ? $params['group_id'] : false;

        $group_id_validation = $this->validate_group_id($group_id);
        if ($group_id_validation !== true) {
            return $group_id_validation;
        }

        if (is_array($group_id)) {
            foreach ($group_id as $item) {
                $result = self::subscribe($params['name'], $number, $item, $customFields);
            }
        } else {
            $result = self::subscribe($params['name'], $number, $group_id, $customFields);
        }

        if (is_wp_error($result)) {
            return self::response($result->get_error_message(), 400);
        }

        return self::response($result);
    }

    /**
     * Validates group_id parameter
     */
    public function validate_group_id($group_id)
    {
        $groups_enabled = Option::getOption('newsletter_form_groups');

        //  If admin enabled groups and user did not select any group, then return error
        if ($groups_enabled && !$group_id) {
            return self::response(__('Please select a specific group.', 'wp-sms'), 400);
        }

        // If group_id is array, check for each item
        // to see if the item is in the list of enabled groups.
        if (is_array($group_id)) {
            foreach ($group_id as $item) {
                if ($this->check_subscribe_group($item) !== true) {
                    return $this->check_subscribe_group($item);
                }
            }

            // If group_id is not array, check
            // to see if the group_id is in the list of enabled groups.     
        } elseif ($this->check_subscribe_group($group_id) !== true) {
            return $this->check_subscribe_group($group_id);
        }

        return true;
    }

    /**
     * Returns Error if this group is not in the list of allowed groups
     */
    public function check_subscribe_group($group_id)
    {
        $allowed_groups = Option::getOption('newsletter_form_specified_groups');

        if ($group_id && $allowed_groups && !in_array($group_id, $allowed_groups)) {
            return self::response(__('Not allowed.', 'wp-sms'), 400);
        }
        return true;
    }

    /**
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_subscribers_callback(WP_REST_Request $request)
    {
        return \WP_SMS\Newsletter::getSubscribers(false, false, [
            'name',
            'mobile'
        ]);
    }

    /**
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function unsubscribe_callback(WP_REST_Request $request)
    {
        // Get parameters from request
        $params = $request->get_params();
        $number = self::convertNumber($params['mobile']);

        $group_id = isset($params['group_id']) ? $params['group_id'] : 0;

        $group_ids_array = array();

        if (is_array($group_id) && $group_id) {
            $group_ids_array = $group_id;
            foreach ($group_ids_array as $item) {
                $result = self::unSubscribe($params['name'], $number, $item);
            }
        } else {
            $result = self::unSubscribe($params['name'], $number, $group_id);
        }

        if (is_wp_error($result)) {
            return self::response($result->get_error_message(), 400);
        }

        return self::response(__('Your mobile number has been successfully unsubscribed.', 'wp-sms'));
    }

    /**
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function verify_subscriber_callback(WP_REST_Request $request)
    {
        // Get parameters from request
        $params = $request->get_params();
        $number = self::convertNumber($params['mobile']);

        $group_id = isset($params['group_id']) ? $params['group_id'] : 0;
        $result   = self::verifySubscriber($params['name'], $number, $params['activation'], $group_id);

        if (is_wp_error($result)) {
            return self::response($result->get_error_message(), 400);
        }

        return self::response(__('Your mobile number has been successfully subscribed.', 'wp-sms'));
    }

    /**
     * Check user permission
     *
     * @param $request
     *
     * @return bool
     */
    public function getSubscribersPermission($request)
    {
        return current_user_can('wpsms_subscribers');
    }
}

new Newsletter();
