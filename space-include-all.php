<?php
/**
Spacecoin for WooCommerce
https://github.com/SpaceWorksCo/WooCommerce-SPACE
 */

//---------------------------------------------------------------------------
// Global definitions
if (!defined('SPACE_PLUGIN_NAME'))
  {
  define('SPACE_VERSION',           '1.0.0');

  //-----------------------------------------------
  define('SPACE_EDITION',           'Standard');


  //-----------------------------------------------
  define('SPACE_SETTINGS_NAME',     'SPACE-Settings');
  define('SPACE_PLUGIN_NAME',       'Spacecoin for WooCommerce');


  // i18n plugin domain for language files
  define('SPACE_I18N_DOMAIN',       'space');

  if (extension_loaded('gmp') && !defined('USE_EXT'))
    define ('USE_EXT', 'GMP');
  else if (extension_loaded('bcmath') && !defined('USE_EXT'))
    define ('USE_EXT', 'BCMATH');
  }
//---------------------------------------------------------------------------

//------------------------------------------
// Load wordpress for POSTback, WebHook and API pages that are called by external services directly.
if (defined('SPACE_MUST_LOAD_WP') && !defined('WP_USE_THEMES') && !defined('ABSPATH'))
   {
   $g_blog_dir = preg_replace ('|(/+[^/]+){4}$|', '', str_replace ('\\', '/', __FILE__)); // For love of the art of regex-ing
   define('WP_USE_THEMES', false);

   // Force-elimination of header 404 for non-wordpress pages.
   header ("HTTP/1.1 200 OK");
   header ("Status: 200 OK");
   }
//------------------------------------------


// This loads necessary modules and selects best math library
if (!class_exists('bcmath_Utils')) require_once (dirname(__FILE__) . '/libs/util/bcmath_Utils.php');
if (!class_exists('gmp_Utils')) require_once (dirname(__FILE__) . '/libs/util/gmp_Utils.php');
if (!class_exists('CurveFp')) require_once (dirname(__FILE__) . '/libs/CurveFp.php');
if (!class_exists('Point')) require_once (dirname(__FILE__) . '/libs/Point.php');
if (!class_exists('NumberTheory')) require_once (dirname(__FILE__) . '/libs/NumberTheory.php');
require_once (dirname(__FILE__) . '/libs/SPACEElectroHelper.php');

require_once (dirname(__FILE__) . '/space-cron.php');
require_once (dirname(__FILE__) . '/space-mpkgen.php');
require_once (dirname(__FILE__) . '/space-utils.php');
require_once (dirname(__FILE__) . '/space-admin.php');
require_once (dirname(__FILE__) . '/space-render-settings.php');
require_once (dirname(__FILE__) . '/space-spacecoin-gateway.php');
