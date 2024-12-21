<?php
// @codingStandardsIgnoreFile
/**
 * @file
 * Local development override configuration feature.
 *
 * To activate this feature, copy and rename it such that its path plus
 * filename is 'sites/default/settings.local.php'. Then, go to the bottom of
 * 'sites/default/settings.php' and uncomment the commented lines that mention
 * 'settings.local.php'.
 *
 * If you are using a site name in the path, such as 'sites/example.com', copy
 * this file to 'sites/example.com/settings.local.php', and uncomment the lines
 * at the bottom of 'sites/example.com/settings.php'.
 */
/**
 * Assertions.
 *
 * The Drupal project primarily uses runtime assertions to enforce the
 * expectations of the API by failing when incorrect calls are made by code
 * under development.
 *
 * @see http://php.net/assert
 * @see https://www.drupal.org/node/2492225
 *
 * If you are using PHP 7.0 it is strongly recommended that you set
 * zend.assertions=1 in the PHP.ini file (It cannot be changed from .htaccess
 * or runtime) on development machines and to 0 in production.
 *
 * @see https://wiki.php.net/rfc/expectations
 */
assert_options(ASSERT_ACTIVE, TRUE);
\Drupal\Component\Assertion\Handle::register();
/**
 * Show all error messages, with backtrace information.
 *
 * In case the error level could not be fetched from the database, as for
 * example the database connection failed, we rely only on this value.
 */
$config['system.logging']['error_level'] = 'verbose';
/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
/**
 * Allow test modules and themes to be installed.
 *
 * Drupal ignores test modules and themes by default for performance reasons.
 * During development it can be useful to install test extensions for debugging
 * purposes.
 */
# $settings['extension_discovery_scan_tests'] = TRUE;
/**
 * Enable access to rebuild.php.
 *
 * This setting can be enabled to allow Drupal's php and database cached
 * storage to be cleared via the rebuild.php page. Access to this page can also
 * be gained by generating a query string from rebuild_token_calculator.sh and
 * using these parameters in a request to rebuild.php.
 */
$settings['rebuild_access'] = TRUE;
/**
 * Skip file system permissions hardening.
 *
 * The system module will periodically check the permissions of your site's
 * site directory to ensure that it is not writable by the website user. For
 * sites that are managed with a version control system, this can cause problems
 * when files in that directory such as settings.php are updated, because the
 * user pulling in the changes won't have permissions to modify files in the
 * directory.
 */
$settings['skip_permissions_hardening'] = TRUE;
/**
 * Hast salt.
 * You can generate the salt at
 * @link http://www.sethcardoza.com/tools/random-password-generator Hast salt. @endlink
 */
$settings['hash_salt'] = 'QQQFbY1w8bXxLtM31dM_-nZe3E3GlycgSnxlbfxMxohOV9F8rTUVyd0RMfU8fXDd5rpJCxU5HA';
/**
 * Remove 'field_prefix' for new fields.
 */
$config['field_ui.settings']['field_prefix'] = '';
/**
 * Private file path
 */
$settings['file_private_path'] = DRUPAL_ROOT . '/sites/default/files/private';
/**
 * Tmp file path
 */
$settings['file_temp_path'] = DRUPAL_ROOT . '/sites/default/files/tmp';
/**
 * Enable local development services and null cache bins.
 */
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/default/development.services.yml';
// https://www.drupal.org/node/2598914 -> 'Find cache bins'.
$cache_bins = [
  'bootstrap',
  //'config',
  'data',
  'default',
  'discovery',
  // 'discovery_migration',
  'dynamic_page_cache',
  'entity',
  'factory',
  // 'jsonapi_memory',
  // 'jsonapi_normalizations',
  // 'jsonapi_resource_types',
  'lock.factory',
  // 'mailchimp',
  // 'menu',
  // 'migrate',
  'page',
  'render',
  // 'rest',
  // 'sendgrid_integration_reports',
  'settings',
  'signal',
  // 'static',
  // 'timestamp.invalidator.bin',
  // 'timestamp.invalidator.tag',
  // 'toolbar',
  // 'ultimate_cron_logger',
];
foreach ($cache_bins as $bin) {
  $settings['cache']['bins'][$bin] = 'cache.backend.null';
}
$settings['extension_discovery_scan_tests'] = TRUE;
// Prevent local builds from accidentally sending emails
// through the live SMTP server.
$config['smtp.settings']['smtp_on'] = 'false';
$config['smtp.settings']['smtp_host'] = '';
// In case we have mailsystem, prevent that too sending the emails with php.
$config['mailsystem.settings']['defaults']['sender'] = 'php_mail';
$config['mailsystem.settings']['defaults']['formatter'] = 'php_mail';

/**
 * Environment indicator.
 */
$config['environment_indicator.indicator']['bg_color'] = '#3995D8';
$config['environment_indicator.indicator']['fg_color'] = '#FFFFFF';
$config['environment_indicator.indicator']['name'] = 'Development';

// Automatically generated include for settings managed by ddev.
if (file_exists($app_root . '/' . $site_path . '/settings.ddev.php')) {
  include $app_root . '/' . $site_path . '/settings.ddev.php';
}

$config['farm_map_mapbox.settings']['api_key'] = 'pk.eyJ1IjoibmF0LWZvci1hdXRoLXVnIiwiYSI6ImNsZGlvd255YjAydDUzbmxndDB4MzU5YnEifQ.u8Tb2PvtKSsxE0AsYJM-Qg';

// GFW variables.
$settings['farm_nfa'] = [
  'gfw_api_key' => $_ENV['GFW_API_KEY'],
  'gfw_api_user' => $_ENV['GFW_API_USER'],
  'gfw_api_password' => $_ENV['GFW_API_PASSWORD'],
];
