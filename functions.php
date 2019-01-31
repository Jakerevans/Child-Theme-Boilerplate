<?php
/**
 * Vendd functions and definitions
 *
 * @package Vendd
 */

/*
 * SETUP NOTES:
 *
 * Change all filename instances from boilerplate to name of the parent theme
 *
 * Modify BOILERPLATE_VERSION constant to whatever the constant in the parent theme is that holds the version number, if there is one. If not, define your own.
 *
 * Find & Replace these 3 strings:
 * boilerplate
 * Boilerplate
 * BOILERPLATE
 *
 * Install Gulp & all Plugins listed in gulpfile.js
 *
 */

// Root child theme folder directory.
define( 'BOILERPLATECHILD_ROOT_DIR', get_stylesheet_directory() );

// Root child theme folder URL.
define( 'BOILERPLATECHILD_ROOT_URL', get_stylesheet_directory_uri() );

// Root Translations Directory.
define( 'BOILERPLATECHILD_CLASS_TRANSLATIONS_DIR', BOILERPLATECHILD_ROOT_DIR . '/includes/classes/translations/' );

// Root JS URL .
define( 'BOILERPLATECHILD_ROOT_JS_URL', BOILERPLATECHILD_ROOT_URL . '/assets/js/' );

// Nonces array.
define( 'BOILERPLATECHILD_NONCES_ARRAY',
	wp_json_encode(
		array(
			'boilerplatechildadminnonce1' => 'boilerplatechild_somethingsomething_action_callback',
		)
	)
);

add_action( 'init', 'boilerplatechild_jre_create_nonces' );
add_action( 'wp_enqueue_scripts', 'boilerplatechild_enqueue_parent_styles' );
add_filter( 'the_generator', 'boilerplatechild_remove_version' );
add_filter( 'login_errors', 'boilerplatechild_wrong_login' );
add_action( 'admin_enqueue_scripts', 'boilerplatechild_admin_style' );
add_action( 'admin_enqueue_scripts', 'boilerplatechild_jre_admin_js' );
add_action( 'wp_enqueue_scripts', 'boilerplatechild_jre_frontend_js' );

/**
 *  Here we take the Constant defined in boilerplatechild.php that holds the values that all our nonces will be created from, we create the actual nonces using wp_create_nonce, and the we define our new, final nonces Constant, called BOILERPLATECHILD_FINAL_NONCES_ARRAY.
 */
function boilerplatechild_jre_create_nonces() {

	$temp_array = array();
	foreach ( json_decode( BOILERPLATECHILD_NONCES_ARRAY ) as $key => $noncetext ) {
		$nonce              = wp_create_nonce( $noncetext );
		$temp_array[ $key ] = $nonce;
	}

	// Defining our final nonce array.
	define( 'BOILERPLATECHILD_FINAL_NONCES_ARRAY', wp_json_encode( $temp_array ) );

}

/**
 * Function to grab the parent style.
 */
function boilerplatechild_enqueue_parent_styles() {

	wp_register_style( 'parent-style', get_template_directory_uri() . '/style.css', null, BOILERPLATE_VERSION );
	wp_enqueue_style( 'parent-style' );

}

/**
 * Function to remove the version number.
 */
function boilerplatechild_remove_version() {
	return false;
}

/**
 * Function to remove incorrect login messages.
 */
function boilerplatechild_wrong_login() {
	return 'Wrong username or password.';
}

/**
 * Adding the admin css file
 */
function boilerplatechild_admin_style() {

	wp_register_style( 'boilerplatechildadminui', get_stylesheet_directory_uri() . '/boilerplatechild-main-admin.css', null, BOILERPLATE_VERSION );
	wp_enqueue_style( 'boilerplatechildadminui' );

}

/**
 * Adding the admin js file, with localization.
 */
function boilerplatechild_jre_admin_js() {

	global $wpdb;

	wp_register_script( 'boilerplatechildadminjs', BOILERPLATECHILD_ROOT_JS_URL . 'boilerplatechild_admin.min.js', array( 'jquery' ), BOILERPLATE_VERSION, true );

	// Next 4-5 lines are required to allow translations of strings that would otherwise live in the boilerplatechild-admin-js.js JavaScript File.
	require_once BOILERPLATECHILD_CLASS_TRANSLATIONS_DIR . 'class-boilerplatechild-translations.php';
	$trans = new VenddChild_Translations();

	// Localize the script with the appropriate translation array from the Translations class.
	$translation_array1 = $trans->trans_strings();

	// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
	$final_array_of_php_values = array_merge( $translation_array1, json_decode( BOILERPLATECHILD_FINAL_NONCES_ARRAY, true ) );

	/* Adding some other individual values we may need.
	$final_array_of_php_values['ROOT_IMG_ICONS_URL']   = ROOT_IMG_ICONS_URL;
	$final_array_of_php_values['ROOT_IMG_URL']   = ROOT_IMG_URL;
	$final_array_of_php_values['EDIT_PAGE_OFFSET']   = EDIT_PAGE_OFFSET;
	$final_array_of_php_values['FOR_TAB_HIGHLIGHT']    = admin_url() . 'admin.php';
	$final_array_of_php_values['SAVED_ATTACHEMENT_ID'] = get_option( 'media_selector_attachment_id', 0 );
	$final_array_of_php_values['LIBRARY_DB_BACKUPS_UPLOAD_URL'] = LIBRARY_DB_BACKUPS_UPLOAD_URL;
	$final_array_of_php_values['SOUNDS_URL'] = SOUNDS_URL;
	$final_array_of_php_values['SETTINGS_PAGE_URL'] = menu_page_url( 'WPBookList-Options-settings', false );
	$final_array_of_php_values['DB_PREFIX'] = $wpdb->prefix;
	*/

	// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'boilerplatechild_php_variables' object (like boilerplatechild_php_variables.nameofkey, like any other JavaScript object).
	wp_localize_script( 'boilerplatechildadminjs', 'boilerplatechildPhpVariables', $final_array_of_php_values );

	wp_enqueue_script( 'boilerplatechildadminjs' );

	return $final_array_of_php_values;

}

/**
 * Adding the frontend js file
 */
function boilerplatechild_jre_frontend_js() {

	wp_register_script( 'frontendjs', BOILERPLATECHILD_ROOT_JS_URL . 'boilerplatechild_frontend.min.js', array( 'jquery' ), BOILERPLATE_VERSION, true );

	// Next 4-5 lines are required to allow translations of strings that would otherwise live in the boilerplatechild-admin-js.js JavaScript File.
	require_once BOILERPLATECHILD_CLASS_TRANSLATIONS_DIR . 'class-boilerplatechild-translations.php';
	$trans = new VenddChild_Translations();

	// Localize the script with the appropriate translation array from the Translations class.
	$translation_array1 = $trans->trans_strings();

	// Now grab all of our Nonces to pass to the JavaScript for the Ajax functions and merge with the Translations array.
	$final_array_of_php_values = array_merge( $translation_array1, json_decode( BOILERPLATECHILD_FINAL_NONCES_ARRAY, true ) );

	/* Adding some other individual values we may need.
	$final_array_of_php_values['ROOT_IMG_ICONS_URL'] = ROOT_IMG_ICONS_URL;
	$final_array_of_php_values['ROOT_IMG_URL']       = ROOT_IMG_URL;
	$final_array_of_php_values['SOUNDS_URL']         = SOUNDS_URL;
	*/

	// Now registering/localizing our JavaScript file, passing all the PHP variables we'll need in our $final_array_of_php_values array, to be accessed from 'boilerplatechild_php_variables' object (like boilerplatechild_php_variables.nameofkey, like any other JavaScript object).
	wp_localize_script( 'frontendjs', 'boilerplatechildPhpVariables', $final_array_of_php_values );

	wp_enqueue_script( 'frontendjs' );

	return $final_array_of_php_values;

}

