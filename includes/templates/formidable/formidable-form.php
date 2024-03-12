<div id="formidable-wpsms" class="contact-form-editor-wpsms">
    <form method="post">
        <input name="submit_action" type="hidden" value="formidable_form_sms_data">
        <?php wp_nonce_field() ?>
        <fieldset>
            <table class="form-table">
                <tbody>

                    <tr id="wp-sms-recipient-numbers">
                        <th scope="row"><label for="formidable-sms-recipient-number"><?php _e('Numbers', 'wp-sms'); ?>:</label></th>
                        <td>
                            <input type="text" value="<?php echo $sms_data['phone'] ?? ''; ?>" size="70" class="large-text code" name="formidable-sms[phone]" id="formidable-sms-recipient-number">
                            <p class="description"><?php _e('<b>Note:</b> When sending multiple numbers, please separate them with a comma. for example: 10000000001, 10000000002.', 'wp-sms'); ?></p>
                        </td>
                    </tr>


                    <tr id="wp-sms-cf7-message-body">
                        <th scope="row"><label for="formidable-sms-message"><?php _e('Message body', 'wp-sms'); ?>:</label></th>
                        <td>
                            <textarea class="large-text" rows="4" cols="100" name="formidable-sms[message]" id="formidable-sms-message"><?php echo $sms_data['message'] ?? ''; ?></textarea>
                            <p class="description"><?php _e('<b>Note:</b> Use %% Instead of [], for example: <code>%your-mobile%</code>', 'wp-sms'); ?><br>
                                <?= $fieldGroup ?>
                            </p>
                        </td>
                    </tr>
            </table>

            <h3><?php _e('Send to form', 'wp-sms'); ?></h3>
            <legend><?php _e('After submitting the form, you have the option to send an SMS message to the specified field:', 'wp-sms'); ?><br></legend>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="formidable-sms-sender-form"><?php _e('Send to field', 'wp-sms'); ?>:</label>
                    </th>
                    <td>
                        <select class="large-select code" name="formidable-sms[field][phone]" id="formidable-sms-sender-form">
                            <?php

                            foreach ($formFields as $field) {
                                $selected = $sms_data['field']['phone'] == $field ? 'selected="selected"' : '';
                                echo "<option value=" . $field . " $selected> " . $field . " </option>";
                            }
                            ?>
                        </select>
                        <p class="description"><?php _e('<b>Note:</b> Use %% Instead of [], for example: <code>%your-mobile%</code>', 'wp-sms'); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="formidable-sms-message-form"><?php _e('Message body', 'wp-sms'); ?>:</label>
                    </th>
                    <td>
                        <textarea class="large-text" rows="4" cols="100" name="formidable-sms[field][message]" id="formidable-sms-message-form"><?php echo $sms_data['field']['message'] ?? ''; ?></textarea>
                        <p class="description"><?php _e('<b>Note:</b> Use %% Instead of [], for example: <code>%your-mobile%</code>', 'wp-sms'); ?><br>
                            <?= $fieldGroup ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="formidable-sms-message-form"></label>
                    </th>
                    <td>
                        <button type="submit" class="button button-primary">submit</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </fieldset>
    </form>
</div>

<style>
    .display-none {
        display: none;
    }

    .head-with-back {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
</style>

<script>
    jQuery('#formidable-sms-recipient').on('change', function(e) {
        var number = document.getElementById('wp-sms-recipient-numbers');
        var subscriber = document.getElementById('wp-sms-recipient-groups');
        if (this.value == 'subscriber') {
            number.style.display = "none";
            subscriber.style.display = "table-row";
        }
        if (this.value == 'number') {
            number.style.display = "table-row";
            subscriber.style.display = "none";
        }

    })
</script>