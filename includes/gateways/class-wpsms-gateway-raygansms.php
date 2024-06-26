<?php

namespace WP_SMS\Gateway;

class raygansms extends \WP_SMS\Gateway
{
    private $wsdl_link = "http://smspanel.trez.ir/api/";
    public $tariff = "https://raygansms.com/";
    public $unitrial = true;
    public $unit;
    public $flash = "enable";
    public $isflash = false;

    /**
     * raygansms constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->validateNumber = "09000000000";
    }

    public function SendSMS()
    {

        /**
         * Modify sender number
         *
         * @param string $this ->from sender number.
         * @since 3.4
         *
         */
        $this->from = apply_filters('wp_sms_from', $this->from);

        /**
         * Modify Receiver number
         *
         * @param array $this ->to receiver number
         * @since 3.4
         *
         */
        $this->to = apply_filters('wp_sms_to', $this->to);

        /**
         * Modify text message
         *
         * @param string $this ->msg text message.
         * @since 3.4
         *
         */
        $this->msg = apply_filters('wp_sms_msg', $this->msg);

        // Get the credit.
        $credit = $this->GetCredit();

        // Check gateway credit
        if (is_wp_error($credit)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $credit->get_error_message(), 'error');

            return $credit;
        }

        $args = array(
            'headers' => array(
                'Content-Type'  => 'application/json',
                'accept'        => 'application/json',
                'authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
            ),
            'body'    => wp_json_encode(array(
                'PhoneNumber'         => $this->from,
                'Message'             => $this->msg,
                'Mobiles'             => $this->to,
                'UserGroupID'         => uniqid(),
                'SendDateInTimeStamp' => time(),
            ))
        );

        $response = wp_remote_post($this->wsdl_link . "smsAPI/SendMessage", $args);

        // Check gateway credit
        if (is_wp_error($response)) {
            // Log the result
            $this->log($this->from, $this->msg, $this->to, $response->get_error_message(), 'error');

            return new \WP_Error('send-sms', $response->get_error_message());
        }

        // Ger response code
        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $result = json_decode($response['body']);

            if ($result->Code == '0') {
                return $result->Result;
            } else {
                return new \WP_Error('send-sms', $result->Message);
            }
        } else {
            return new \WP_Error('send-sms', $response['body']);
        }
    }

    /**
     * @return int|\WP_Error
     */
    public function GetCredit()
    {
        // Check username and password
        if (!$this->username && !$this->password) {
            return new \WP_Error('account-credit', esc_html__('API username or API password is not entered.', 'wp-sms'));
        }

        $args = array(
            'timeout' => 10,
            'headers' => array(
                'accept'        => 'application/json',
                'authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
            )
        );

        $response = wp_remote_post($this->wsdl_link . "smsAPI/GetCredit", $args);

        // Check gateway credit
        if (is_wp_error($response)) {
            return new \WP_Error('account-credit', $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code == '200') {
            $result = json_decode($response['body']);

            if ($result->Code == '0') {
                return $result->Result;
            } else {
                return new \WP_Error('account-credit', $result->Message);
            }
        } else {
            return new \WP_Error('account-credit', $response['body']);
        }
    }
}