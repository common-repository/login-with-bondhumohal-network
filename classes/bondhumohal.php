<?php
/**
 * Class bondhumohal
 *
 * This class operates all user end functions
 */
class bondhumohal{

    /**
     * Login with bondhumohal | Wordpress Social constructor.
     */
    public function __construct()
    {
		add_shortcode( 'bondhumohal_login', array($this, 'bondhumohallogin') );
		add_shortcode( 'bondhumohal_share', array($this, 'bondhumohalShare') );        
		add_action( 'wp_enqueue_scripts', array($this, 'addBondhumohalButtonCSS'));
		// Callback URL
        add_action( 'init', array($this, 'bondhumohalApiCallback'));
    }

	private $access_token;

	/**
	 * Where we redirect our user after the process
	 *
	 * @var string
	 */
	private $redirect_url;
	
    /**
     * Callback URL used by the API
     *
     * @var string
     */

/**
 * Returns the bondhumohal credentials as an array containing app_id and app_secret
 *
 * @return array
 */
static function getBondhumohalCredentials() {
   return get_option( 'bondhumohal_login', array() );
}

/**
 * Returns the callback URL
 *
 * @return string
 */
static function getCallbackUrl() {
   return get_admin_url( null, 'admin-ajax.php?action=bondhumohal_login' );
}

/**
 * Render the shortcode [bondhumohal_login]
 *
 * It displays our Login / Register button
 */
public function bondhumohallogin() {

    // No need for the button is the user is already logged
    if(is_user_logged_in())
        return;
    // Different labels according to whether the user is allowed to register or not
    if (get_option( 'users_can_register' )) {
        $button_label = __('Login or Register with bondhumohal', 'bondhumohal');
    } else {
        $button_label = __('Login with bondhumohal', 'bondhumohal');
    }

	$credentials = self::getBondhumohalCredentials();

	   // Only if we have some credentials, ideally an Exception would be thrown here
	  
	   if ($credentials['button_type']==2) {
		  // Button markup
			$logo = '<img src="'. BONDHUMOHAL_URL. '/assets/images/bondhumohal.png" alt="bondhumohal logo">';
			$html = '<div id="bondhumohal-wrapper">';
			$html .= '<a href="'.$this->getBondhumohalLoginUrl().'" class="btn" id="bondhumohal-button">'. $logo . $button_label .'</a>';
			$html .= '</div>';	  
	   } else {		   
			$html = '<a href="'.$this->getBondhumohalLoginUrl().'" class="content-share__item lovez buzz-share-button">';
			$html .= '<p class="bondhumohal-share">';
			$html .= '</p>';
			$html .= '</a>';
	   }

    // Write it down
    return $html;
}
public function bondhumohalShare() {
	$credentials = self::getBondhumohalCredentials();
	$web_url="https://bondhumohal.com/";
    // Button markup
	$logo = '<img src="'. BONDHUMOHAL_URL. '/assets/images/bondhumohal-icon.png" alt="bondhumohal logo">';
	global $wp;
	$full_url=home_url($wp->request);
	ob_start();
	?>
		<a class="content-share__item lovez buzz-share-button">
			<p class="bondhumohal-share" onclick="window.open('<?php echo $web_url ?>sharer?url=<?php echo $full_url ?>', 'Share on bondhumohal', 'height=600,width=800');">
			</p>
		</a>
	<?php
	$content=ob_get_clean();

    
	return $content;
}
/**
 * Login URL to bondhumohal API
 *
 * @return string
 */
private function getBondhumohalLoginUrl() {
	$credentials = self::getBondhumohalCredentials();
	$web_url="https://bondhumohal.com/";

   // Only if we have some credentials, ideally an Exception would be thrown here
   if(!isset($credentials['app_id']))
      return null;


    $url = $web_url . 'oauth?app_id=' .$credentials['app_id'];

    return esc_url($url);

}
/**
 * Get user details through the bondhumohal API
 *
 * @link https://bondhumohal.com/developers
 */
private function getBondhumohalUserDetails($bondhumohal)
{  
		$credentials = self::getBondhumohalCredentials();
		$web_url="https://bondhumohal.com/";
		$type = "get_user_data"; // or posts_data
		//$response = file_get_contents("{$web_url}app_api?access_token={$bondhumohal}&type={$type}");
		$response = wp_remote_get("{$web_url}app_api?access_token={$bondhumohal}&type={$type}");
		$body = wp_remote_retrieve_body( $response );
		       
    return $body;

}
/**
 * Login an user to WordPress
 *
 * @link https://codex.wordpress.org/Function_Reference/get_users
 * @return bool|void
 */
private function loginBondhumohalUser($bondhumohal_user) {

	$credentials = self::getBondhumohalCredentials();

    // We look for the `bondhumohal_login_email` to see if there is any match
    $wp_users = get_users(array(
        'meta_key'     => 'bondhumohal_login_email',
        'meta_value'   => $bondhumohal_user['email'],
        'number'       => 1,
        'count_total'  => false,
        'fields'       => 'email',
    ));

    if(empty($wp_users[0])) {
        return false;
    }

    // Log the user ?
    wp_set_auth_cookie( $wp_users[0] );
	header("Location: ".$credentials['callback_url'], true);
    die();

}
/**
 * Create a new WordPress account using bondhumohal Details
 */
private function createBondhumohalUser($bondhumohal_user) {

    // Create an username
    $bondhumohalemail = $bondhumohal_user['email'];
	$parts = explode("@", $bondhumohalemail);
	$username = $parts[0];
    // Creating our user
    $new_user = wp_create_user($username, wp_generate_password(), $bondhumohal_user['email']);

    if(is_wp_error($new_user)) {
       
	   echo "Error while creating user!";
	   header("Location: ".$this->redirect_url, true);
       die();
   }
    // Setting the meta
    update_user_meta( $new_user, 'first_name', $bondhumohal_user['first_name'] );
    update_user_meta( $new_user, 'last_name', $bondhumohal_user['last_name'] );
	update_user_meta( $new_user, 'bondhumohal_login_email', $bondhumohal_user['email'] );

    // Log the user ?
    wp_set_auth_cookie( $new_user );

}
public function bondhumohalApiCallback() {
	if ( isset($_GET['code']) ) {	
		$credentials = self::getBondhumohalCredentials();
		$found = sanitize_text_field($_GET['code']);    
		// Only if we have some credentials, ideally an Exception would be thrown here    
		if( !isset($credentials['app_id']) || !isset($credentials['app_secret']) ) {
			exit();
		} else {
			$web_url="https://bondhumohal.com/";		
			$get_bondhumohal_data = wp_remote_get("{$web_url}authorize?app_id={$credentials['app_id']}&app_secret={$credentials['app_secret']}&code={$found}");
			$body = wp_remote_retrieve_body( $get_bondhumohal_data );
			
			$json = json_decode($body, true);
			
			if (!empty($json['access_token'])) {
				$access_token = $json['access_token']; // your access token
				$wl_data = $this->getBondhumohalUserDetails($access_token);
				$enc_wl_data=json_decode($wl_data, true);
				$bondhumohal_details=$enc_wl_data['user_data'];
			// We first try to login the user
				$this->loginBondhumohalUser($bondhumohal_details);

			// Otherwise, we create a new account
				$this->createBondhumohalUser($bondhumohal_details);

			// Redirect the user succesful login
			header("Location: ".$credentials['callback_url'], true);
			die();

			}
		}
	} else {
		return null;
	}
	
}
public function addBondhumohalButtonCSS() {
    wp_enqueue_style( 'bondhumohal-button', BONDHUMOHAL_URL. '/assets/css/button-style.css' );
}
}
/*
 * Starts our plugins!
 */
 new bondhumohal();