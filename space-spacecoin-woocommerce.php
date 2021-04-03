<?php
/**
 * Plugin Name: Spacecoin for WooCommerce
 * Plugin URI: https://github.com/SpaceWorksCo/WooCommerce-SPACE
 * Description: Spacecoin for WooCommerce plugin allows you to accept payments in SPACE for physical and digital products at your WooCommerce-powered online store.
 * Version: 1.0.0
 * Author: SpaceWorks
 * Author URI: https://spaceworks.co
 * License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: spacecoin-for-woocommerce
 *
 * @package WordPress
 * @since 1.0.0
 */


// Include everything
include (dirname(__FILE__) . '/space-include-all.php');

//---------------------------------------------------------------------------
// Add hooks and filters

// create custom plugin settings menu
add_action( 'admin_menu',                   'SPACE_create_menu' );

register_activation_hook(__FILE__,          'SPACE_activate');
register_deactivation_hook(__FILE__,        'SPACE_deactivate');
register_uninstall_hook(__FILE__,           'SPACE_uninstall');

add_filter ('cron_schedules',               'SPACE__add_custom_scheduled_intervals');
add_action ('BWWC_cron_action',             'SPACE_cron_job_worker');

SPACE_set_lang_file();
//---------------------------------------------------------------------------

//===========================================================================
// activating the default values
function SPACE_activate()
{
    global  $g_SPACE__config_defaults;

    $space_default_options = $g_SPACE__config_defaults;

    // This will overwrite default options with already existing options but leave new options (in case of upgrading to new version) untouched.
    $space_settings = SPACE__get_settings ();

    foreach ($space_settings as $key=>$value)
    	$space_default_options[$key] = $value;

    update_option (SPACE_SETTINGS_NAME, $space_default_options);

    // Re-get new settings.
    $space_settings = SPACE__get_settings ();

    // Create necessary database tables if not already exists...
    SPACE__create_database_tables ($space_settings);
    SPACE__SubIns ();

    //----------------------------------
    // Setup cron jobs

    if ($space_settings['enable_soft_cron_job'] && !wp_next_scheduled('SPACE_cron_action'))
    {
    	$cron_job_schedule_name = strpos($_SERVER['HTTP_HOST'], 'ttt.com')===FALSE ? $space_settings['soft_cron_job_schedule_name'] : 'seconds_30';
    	wp_schedule_event(time(), $cron_job_schedule_name, 'SPACE_cron_action');
    }
    //----------------------------------

}
//---------------------------------------------------------------------------
// Cron Subfunctions
function SPACE__add_custom_scheduled_intervals ($schedules)
{
	$schedules['seconds_30']     = array('interval'=>30,     'display'=>__('Once every 30 seconds'));     // For testing only.
	$schedules['minutes_1']      = array('interval'=>1*60,   'display'=>__('Once every 1 minute'));
	$schedules['minutes_2.5']    = array('interval'=>2.5*60, 'display'=>__('Once every 2.5 minutes'));
	$schedules['minutes_5']      = array('interval'=>5*60,   'display'=>__('Once every 5 minutes'));

	return $schedules;
}
//---------------------------------------------------------------------------
//===========================================================================

//===========================================================================
// deactivating
function SPACE_deactivate ()
{
    // Do deactivation cleanup. Do not delete previous settings in case user will reactivate plugin again...

   //----------------------------------
   // Clear cron jobs
   wp_clear_scheduled_hook ('SPACE_cron_action');
   //----------------------------------
}
//===========================================================================

//===========================================================================
// uninstalling
function SPACE_uninstall ()
{
    $space_settings = SPACE__get_settings();

    if ($space_settings['delete_db_tables_on_uninstall'])
    {
        // delete all settings.
        delete_option(SPACE_SETTINGS_NAME);

        // delete all DB tables and data.
        SPACE__delete_database_tables ();
    }
}
//===========================================================================

//===========================================================================
function SPACE_create_menu()
{

    // create new top-level menu
    // http://www.fileformat.info/info/unicode/char/e3f/index.htm
    add_menu_page (
        __('Woo Commerce', SPACE_I18N_DOMAIN),                    // Page title
        __('Spacecoin', SPACE_I18N_DOMAIN),                        // Menu Title - lower corner of admin menu
        'administrator',                                        // Capability
        'space-settings',                                        // Handle - First submenu's handle must be equal to parent's handle to avoid duplicate menu entry.
        'SPACE__render_general_settings_page',                   // Function

        plugins_url('/images/space_16x.png', __FILE__)      // Icon URL
        );

    add_submenu_page (
        'space-settings',                                        // Parent
        __("Spacecoin for WooCommerce", SPACE_I18N_DOMAIN),                   // Page title
        __("General Settings", SPACE_I18N_DOMAIN),               // Menu Title
        'administrator',                                        // Capability
        'space-settings',                                        // Handle - First submenu's handle must be equal to parent's handle to avoid duplicate menu entry.
        'SPACE__render_general_settings_page'                    // Function
        );
}
//===========================================================================

//===========================================================================
// load language files
function SPACE_set_lang_file()
{
    # set the language file
    $currentLocale = get_locale();
    if(!empty($currentLocale))
    {
        $moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
        if (@file_exists($moFile) && is_readable($moFile))
        {
            load_textdomain(SPACE_I18N_DOMAIN, $moFile);
        }

    }
}
//===========================================================================
/*
function tl_save_error() {
    update_option( 'plugin_error',  ob_get_contents() );
}
add_action( 'activated_plugin', 'tl_save_error' );

echo get_option( 'plugin_error' );

file_put_contents( 'C:\errors' , ob_get_contents() );
*/
