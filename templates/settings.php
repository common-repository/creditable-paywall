<?php
namespace creditablepaywall;

// Fetch all settings. Assuming get_settings() returns an associative array of all plugin settings.
$settings = Creditablepaywall::get_settings();

// Extract the specific setting value. Ensure to provide a default value if the setting isn't set.
$api_key = isset( $settings['api_key'] ) ? $settings['api_key'] : '';
$paywall_title = isset( $settings['paywall_title'] ) ? $settings['paywall_title'] : 'Pay-per-article to continue reading';
$paywall_description = isset( $settings['paywall_description'] ) ? $settings['paywall_description'] : 'Pay for this article to access to the rest of this post.';
$aff_id = isset( $settings['aff_id'] ) ? $settings['aff_id'] : '';
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form method="post">
		<?php echo wp_nonce_field( 'creditablepaywall_settings_save', 'creditablepaywall_settings_nonce' ); ?>
        <h2>Creditable</h2>
        <div style="padding: 0 10px;">
            <p>To use the Creditable Paywall plugin and monetize your posts, follow these steps:</p>
            <ul style="list-style: numeric; margin-left: 30px;">
                <li><strong>Sign up for a free Creditable Partner Account:</strong> Visit <a href="https://partner.creditable.news" target="_blank">Creditable Partner</a> to create your account.</li>
				<li><strong>Log in to your dashboard:</strong> Access your account at <a href="https://partner.creditable.news" target="_blank">Creditable Partner</a> and add your WordPress website as a mediatitle.</li>
				<li><strong>Generate an API key:</strong> An API key will be generated for each mediatitle you add.</li>
				<li><strong>Insert the API key:</strong> Enter the API key into the Creditable Paywall settings below.</li>
				<li><strong>Start monetizing:</strong> Add the Creditable Paywall block to your posts to begin monetizing.</li>
            </ul>
            <p>For more information, please visit <a href="https://www.creditable.news" target="_blank">Creditable</a>.</p>
        </div>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Api Key</th>
                <td>
                    <input type="text" id="api_key" name="creditablepaywall_settings[api_key]"
                           class="regular-text" value="<?php echo esc_textarea( $api_key ); ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Paywall title</th>
                <td>
                    <input type="text" id="paywall_title" name="creditablepaywall_settings[paywall_title]"
                           class="large-text" value="<?php echo esc_textarea( $paywall_title ); ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Paywall description</th>
                <td>
                    <input type="text" id="paywall_description" name="creditablepaywall_settings[paywall_description]"
                           class="large-text" value="<?php echo esc_textarea( $paywall_description ); ?>">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">Affiliate</th>
                <td>
                    <input type="text" id="aff_id" name="creditablepaywall_settings[aff_id]" maxlength="5"
                           class="" value="<?php echo esc_textarea( $aff_id ); ?>" readonly>
                </td>
            </tr>
        </table>

		<?php submit_button(); ?>
    </form>
</div>
