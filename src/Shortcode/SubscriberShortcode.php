<?php

namespace WP_SMS\Shortcode;

class SubscriberShortcode
{
    public function register()
    {
        add_shortcode('wp_sms_subscriber_form_shortcode', array($this, 'registerSubscriberShortcodeCallback'));
    }

    public function registerSubscriberShortcodeCallback($attributes)
    {
        isset($attributes['fields']) ? $attrs = $this->retrieveData($attributes) : $attrs = $attributes;

        return wp_sms_render_subscriber_form($attrs);
    }

    public function retrieveData($attrs)
    {
        $fields        = array();
        $custom_fields = explode('|', $attrs['fields']);

        foreach ($custom_fields as $custom_field) {
            $field          = explode(':', $custom_field);
            $label          = strtolower(ltrim($field[0]));
            $fields[$label] = array(
                'label'       => $label,
                'type'        => 'text',
                'description' => isset($field[1]) ? $field[1] : '',
            );
        }

        $attrs['fields'] = $fields;

        return $attrs;
    }
}