<?php
function xml_option_setting() {
	$setting = get_option('xml_feed_settings');
	if (isset($_REQUEST['submit'])) {
		$setting = [
			'url' => $_REQUEST['url'],
        	'cron_interval' => $_REQUEST['cron_interval'],
		];
		update_option('xml_feed_settings', $setting);
	}
?>
<div class="wrap">
    <h1><?php _e('Setting'); ?></h1>                        
    <form method="post" action="">
        <table class="form-table">
            <tbody>                                            
                <tr>
                    <th scope="row"><label><?php _e('Xml Url'); ?> <span class="description">(required)</span></label></th>
                    <td>
                        <input type="url" name="url" class="regular-text code" value="<?php if(isset($setting['url'])) { echo $setting['url']; } ?>" required />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label><?php _e('Cron Interval'); ?> <span class="description">(required)</span></label></th>
                    <td>
                        <input type="text" name="cron_interval" class="regular-text code" value="<?php if(isset($setting['cron_interval'])) { echo $setting['cron_interval']; } ?>" required />
                    </td>
                </tr>
            </tbody>
        </table>
        <p><input type='submit' class='button-primary' name="submit" value="<?php _e('Save'); ?>" /></p>
    </form>
</div>

<?php }