﻿jQuery(document).ready(function ($) {

    WpSmsUltimateMember.init();
    WpSmsBuddyPress.init();
    WpSmsNotifications.init();
    WpSmsJobManager.init();
    WpSmsWoocommerce.init();

    // Select2
    $('.js-wpsms-select2').select2({
        placeholder: "Please select"
    });

    // Auto submit the gateways form, after changing value
    $("#wpsms_settings\\[gateway_name\\]").on('change', function () {
        $('input[name="submit"]').click();
    });

    if ($('.repeater').length) {
        $('.repeater').repeater({
            initEmpty: false,
            show: function () {
                $(this).slideDown();
            },
            hide: function (deleteElement) {
                if (confirm('Are you sure you want to delete this item?')) {
                    $(this).slideUp(deleteElement);
                }
            },
            isFirstItemUndeletable: true
        });
    }
});

/**
 * UltimateMember
 * @type {{init: WpSmsUltimateMember.init, alreadyEnabled: ((function(): (boolean|undefined))|*), getFields: WpSmsUltimateMember.getFields, hideOrShowFields: WpSmsUltimateMember.hideOrShowFields, addEventListener: WpSmsUltimateMember.addEventListener}}
 */
let WpSmsUltimateMember = {

    getFields: function () {
        this.fields = {
            mobileNumberField: {
                element: jQuery('#wps_pp_settings\\[um_field\\]'),
                active: false,
            },
            syncOldMembersField: {
                element: jQuery('#wps_pp_settings\\[um_sync_previous_members\\]'),
                active: true,
            },
            fieldSelector: {
                element: jQuery('#wps_pp_settings\\[um_sync_field_name\\]'),
                active: true,
            }
        }

    },

    alreadyEnabled: function () {
        if (this.fields.mobileNumberField.element.is(':checked')) {
            this.fields.syncOldMembersField.active = false;
            this.fields.syncOldMembersField.element.closest('tr').hide()
            return true;
        }
    },

    hideOrShowFields: function () {

        const condition = this.fields.mobileNumberField.element.is(':checked');

        if (condition) {
            for (const field in this.fields) {
                if (this.fields[field].active) this.fields[field].element.closest('tr').show();
            }
        } else {
            for (const field in this.fields) {
                if (this.fields[field].active) this.fields[field].element.closest('tr').hide();
            }
        }
    },

    addEventListener: function () {
        this.fields.mobileNumberField.element.change(function () {
            this.hideOrShowFields();
        }.bind(this));
    },

    init: function () {
        this.getFields();
        this.alreadyEnabled();
        this.hideOrShowFields();
        this.addEventListener();
    }

}

/**
 * BuddyPress
 * @type {{init: WpSmsBuddyPress.init, alreadyEnabled: ((function(): (boolean|undefined))|*), getFields: WpSmsBuddyPress.getFields}}
 */
let WpSmsBuddyPress = {

    getFields: function () {
        this.fields = {
            mobileNumberField: {
                element: jQuery('#wps_pp_settings\\[bp_mobile_field\\]'),
            },
            fieldSelector: {
                element: jQuery('#wps_pp_settings\\[bp_mobile_field_id\\]'),
            },
            syncFields: {
                element: jQuery('#wps_pp_settings\\[bp_sync_fields\\]'),
            }
        }

    },

    hideOrShowFields: function () {
        if (this.fields.mobileNumberField.element.val() != 'used_current_field') {
            this.fields.fieldSelector.element.closest('tr').hide()
            this.fields.syncFields.element.closest('tr').hide()
        } else {
            this.fields.fieldSelector.element.closest('tr').show()
            this.fields.syncFields.element.closest('tr').show()
        }
    },

    addEventListener: function () {
        this.fields.mobileNumberField.element.change(function () {
            this.hideOrShowFields();
        }.bind(this));
    },

    init: function () {
        this.getFields();
        this.hideOrShowFields();
        this.addEventListener();
    }

}

/**
 * Notifications
 * @type {{init: WpSmsNotifications.init, alreadyEnabled: ((function(): (boolean|undefined))|*), getFields: WpSmsNotifications.getFields}}
 */
let WpSmsNotifications = {

    getFields: function () {
        this.fields = {
            receiverField: {
                element: jQuery('#wpsms_settings\\[notif_publish_new_post_receiver\\]'),
            },
            subscriberField: {
                element: jQuery('#wpsms_settings\\[notif_publish_new_post_default_group\\]'),
            },
            numbersField: {
                element: jQuery('#wpsms_settings\\[notif_publish_new_post_numbers\\]'),
            }
        }
    },

    hideOrShowFields: function () {
        if (this.fields.receiverField.element.val() == 'subscriber') {
            this.fields.subscriberField.element.closest('tr').show()
            this.fields.numbersField.element.closest('tr').hide()
        } else {
            this.fields.subscriberField.element.closest('tr').hide()
            this.fields.numbersField.element.closest('tr').show()
        }
    },

    addEventListener: function () {
        this.fields.receiverField.element.change(function () {
            this.hideOrShowFields();
        }.bind(this));
    },

    init: function () {
        this.getFields();
        this.hideOrShowFields();
        this.addEventListener();
    }

}

/**
 * Job Manager
 * @type {{init: WpSmsJobManager.init, alreadyEnabled: ((function(): (boolean|undefined))|*), getFields: WpSmsJobManager.getFields}}
 */
let WpSmsJobManager = {

    getFields: function () {
        this.fields = {
            receiverField: {
                element: jQuery('#wps_pp_settings\\[job_notify_receiver\\]'),
            },
            subscriberField: {
                element: jQuery('#wps_pp_settings\\[job_notify_receiver_subscribers\\]'),
            },
            numbersField: {
                element: jQuery('#wps_pp_settings\\[job_notify_receiver_numbers\\]'),
            }
        }
    },

    hideOrShowFields: function () {
        if (this.fields.receiverField.element.val() == 'subscriber') {
            this.fields.subscriberField.element.closest('tr').show()
            this.fields.numbersField.element.closest('tr').hide()
        } else {
            this.fields.subscriberField.element.closest('tr').hide()
            this.fields.numbersField.element.closest('tr').show()
        }
    },

    addEventListener: function () {
        this.fields.receiverField.element.change(function () {
            this.hideOrShowFields();
        }.bind(this));
    },

    init: function () {
        this.getFields();
        this.hideOrShowFields();
        this.addEventListener();
    }

}

/**
 * Woocommerce
 * @type {{init: WpSmsWoocommerce.init, alreadyEnabled: ((function(): (boolean|undefined))|*), getFields: WpSmsWoocommerce.getFields}}
 */
 let WpSmsWoocommerce = {

    getFields: function () {
        this.fields = {
            receiverField: {
                element: jQuery('#wps_pp_settings\\[wc_notify_product_receiver\\]'),
            },
            subscriberField: {
                element: jQuery('#wps_pp_settings\\[wc_notify_product_cat\\]'),
            },
            numbersField: {
                element: jQuery('#wps_pp_settings\\[wc_notify_product_roles\\]'),
            }
        }
    },

    hideOrShowFields: function () {
        if (this.fields.receiverField.element.val() == 'subscriber') {
            this.fields.subscriberField.element.closest('tr').show()
            this.fields.numbersField.element.closest('tr').hide()
        } else {
            this.fields.subscriberField.element.closest('tr').hide()
            this.fields.numbersField.element.closest('tr').show()
        }
    },

    addEventListener: function () {
        this.fields.receiverField.element.change(function () {
            this.hideOrShowFields();
        }.bind(this));
    },

    init: function () {
        this.getFields();
        this.hideOrShowFields();
        this.addEventListener();
    }

}