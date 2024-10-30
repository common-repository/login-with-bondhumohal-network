<?php
/**
 * Class bondhumohalAdmin
 *
 * This class creates a very simple Options page
 */
class bondhumohalAdmin 
{
 
  /**
   * The security nonce
   *
   * @var string
   */
  private $_nonce = 'bondhumohal_admin';
 
  /**
   * bondhumohalAdmin constructor.
   */
  public function __construct() 
{
    add_action( 'admin_menu', array( $this, 'addBondhumohalAdminMenu' ) );
    add_action( 'wp_ajax_bondhumohal_admin_settings', array( $this, 'bondhumohalAdminSettings' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'addBondhumohalAdminScripts' ) );
 
  }
 /**
 * Adds bondhumohal to WordPress Admin Sidebar Menu
 */
public function addBondhumohalAdminMenu() {
	$icon = BONDHUMOHAL_URL . '/assets/images/bondhumohal-icon.png';
    add_menu_page(
        __( 'Login with bondhumohal', 'bondhumohal' ),
        __( 'Login with bondhumohal', 'bondhumohal' ),
        'manage_options',
        'bondhumohal_login',
        array( $this, 'bondhumohaladminlayout' ),
        $icon
    );
}
/**
 * Outputs the Admin Dashboard layout
 */
public function bondhumohaladminlayout() {
	$icon1 = '<img src="'. BONDHUMOHAL_URL. '/assets/images/bondhumohal.png" alt="bondhumohal logo">';
	$btwrapper = '<img src="'. BONDHUMOHAL_URL. '/assets/images/button-wrapper.png" alt="bondhumohal logo">';
    $bondhumohal_settings = bondhumohal::getBondhumohalCredentials();
    $bondhumohal_app_id = (isset($bondhumohal_settings['app_id']) && !empty($bondhumohal_settings['app_id'])) ? $bondhumohal_settings['app_id'] : '';
    $bondhumohal_app_secret = (isset($bondhumohal_settings['app_secret']) && !empty($bondhumohal_settings['app_secret'])) ? $bondhumohal_settings['app_secret'] : '';
	$bondhumohal_callback_url = (isset($bondhumohal_settings['callback_url']) && !empty($bondhumohal_settings['callback_url'])) ? $bondhumohal_settings['callback_url'] : '';
	$bondhumohal_button_type = (isset($bondhumohal_settings['button_type']) && !empty($bondhumohal_settings['button_type'])) ? $bondhumohal_settings['button_type'] : '';
	
    ?>

    <div class="wrap">
        <h3><?php _e( 'Connect With Bondhumohal App <a href="https://bondhumohal.com/apps" target="_blank">Create & Manage Bondhumohal App</a>', 'bondhumohal' ); ?></h3>

        <table class="form-table">
            <tbody>
			<tr>
                <td scope="row">
                    <h3><?php _e( 'bondhumohal App ID', 'bondhumohal' ); ?></h3>
                </td>
                <td>
                    <input id="bondhumohal-app-id" placeholder="App ID Like: 65f25798f4de2790870b" class="regular-text" value="<?php echo $bondhumohal_app_id; ?>"/>
                </td>
            </tr>            
            <tr>
                <td>
                    <h3><?php _e( 'bondhumohal App Secret', 'bondhumohal' ); ?></h3>
                </td>
                <td>
                    <input id="bondhumohal-app-secret" placeholder="App Secret Like: d8ac156e0b4cd677f69585b3b9f7f5c256f4a39" class="regular-text" value="<?php echo $bondhumohal_app_secret; ?>"/>
                </td>
            </tr>
            <tr>
                <td>
                    <h3><?php _e( 'Your Callback Url', 'bondhumohal' ); ?></h3>
                </td>
                <td>
                    <input id="bondhumohal-callback-url" placeholder="Callback URL of this website Like: <?php echo site_url(); ?>" class="regular-text" value="<?php echo $bondhumohal_callback_url; ?>"/>
                </td>
            </tr>
			<tr>
			    <td>
                    <h3><?php _e( 'Login Button Style', 'bondhumohal' ); ?></h3>
                </td>
                <td>
                    <select name="bondhumohal-button-type" id="bondhumohal-button-type" value="<?php echo $bondhumohal_settings['button_type']; ?>">
					<?php if($bondhumohal_settings['button_type']==2) {		?>				
					<option value="1"><?php _e( 'Icon', 'bondhumohal' ); ?></option>
					<option value="2" selected="selected"><?php _e( 'Button Wrapper', 'bondhumohal' ); ?></option>	
					<?php } else { ?>
					<option value="1" selected="selected"><?php _e( 'Icon', 'bondhumohal' ); ?></option>
					<option value="2"><?php _e( 'Button Wrapper', 'bondhumohal' ); ?></option>	
					<?php } ?>					
					</select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button class="button button-primary" id="bondhumohal-details"><?php _e( 'Submit', 'bondhumohal' ); ?></button>
                </td>
            </tr>
            </tbody>
        </table>		
		<h1><?php _e( 'How To Integrate bondhumohal Plugin With Your Wordpress Webiste or Blog:', 'bondhumohal' ); ?></h1>
		<h2><?php _e( 'Create app', 'bondhumohal' ); ?><h2>
		<?php $icon = BONDHUMOHAL_URL . '/assets/images/login-with-bondhumohal-wordpress-plugin.png'; ?>
		<img src="<?php echo $icon ?>">
		<h2><?php _e( 'Copy your app id and app secret keys and paste above and save!', 'bondhumohal' ); ?></h2>
		<h2><?php _e( 'To enable login or register button use shortcode [bondhumohal_login]', 'bondhumohal' ); ?><br><?php _e( 'To enable bondhumohal share button use shortcode [bondhumohal_share]', 'bondhumohal' ); ?></h2>
		<h1><?php _e( 'bondhumohal Login Button Styles', 'bondhumohal' ); ?></h1>
		<h3><?php _e( 'Icon', 'bondhumohal' ); ?></h3>
		<?php echo $icon1 ?>
		<h3><?php _e( 'Button Wrapper', 'bondhumohal' ); ?></h3>
		<?php echo $btwrapper ?>		
    </div>

    <?php

}
/**
 * Adds Admin Scripts for the Ajax call
 */
public function addBondhumohalAdminScripts() {

    wp_enqueue_script( 'bondhumohal-admin', BONDHUMOHAL_URL. 'assets/js/admin.js', array(), 1.0 );

    $admin_options = array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        '_nonce'   => wp_create_nonce( $this->_nonce ),
    );
    wp_localize_script( 'bondhumohal-admin', 'bondhumohal_admin', $admin_options );

}
/**
 * Callback for the Ajax request
 *
 * Updates the bondhumohal App ID and App Secret options
 */
public function bondhumohalAdminSettings() {

    if ( wp_verify_nonce( sanitize_text_field($_POST['security']), $this->_nonce ) === false ) {
        die( 'Invalid Request!' );
    }
	$wappid=sanitize_text_field($_POST['app_id']);
	$wappsct=sanitize_text_field($_POST['app_secret']);
    $wurl=esc_url(($_POST['callback_url']));
	$wbt=sanitize_text_field($_POST['button_type']);

    if (
            (isset($wappid) && !empty($wappid))
			&&
            (isset($wurl) && !empty($wurl))
            &&
            (isset($wappsct) && !empty($wappsct))
			&&
            (isset($wbt) && !empty($wbt))
    ) {
          update_option( 'bondhumohal_login', array(
            'app_id'     => $wappid,
            'app_secret' => $wappsct,
            'callback_url' => $wurl,
			'button_type' => $wbt,
        ) );
		echo __('Saved!', 'bondhumohal');
		die();
    } else {
		echo __('Please enter complete details!', 'bondhumohal');
		die();
	}

    


}



}
 
/*
 * Starts our admin class!
 */
new bondhumohalAdmin();