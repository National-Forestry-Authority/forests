<?php

/**
 * DB.
 */
$databases['default']['default'] = [
  'host' => $_SERVER['DB_HOST'],
  'port' => $_SERVER['DB_PORT'],
  'database' => $_SERVER['DB_NAME'],
  'username' => $_SERVER['DB_USER'],
  'password' => $_SERVER['DB_PASS'],
  'prefix' => $_SERVER['DB_PREFIX'] ?? '',
  'driver' => $_SERVER['DB_DRIVER'],
  'namespace' => sprintf('Drupal\Core\Database\Driver\%s', $_SERVER['DB_DRIVER']),
];

/**
 * Hash Salt if not defined.
 */
if (!isset($settings['hash_salt']) || $settings['hash_salt'] == '') {
  $settings['hash_salt'] = 'zii4nt68cpEuiAh8lkgBO_ih9SJLXkVmH1kuQw3yhIKFV_W2C08AHojniV9kMGC6170wvgQRnw';
}

/**
 * Trusted hosts.
 */
$settings['trusted_host_patterns'] = [
  sprintf('^%s$', str_replace('.', '\.', $_SERVER['APP_DOMAIN'])),
  sprintf('^.+\.%s$', str_replace('.', '\.', $_SERVER['APP_DOMAIN'])),
];

$trusted_hosts = $_SERVER['TRUSTED_HOSTS'] ?? '';
$trusted_hosts = explode(',', $trusted_hosts);
$trusted_hosts = array_filter($trusted_hosts);
foreach ($trusted_hosts as $host) {
  $settings['trusted_host_patterns'][] = sprintf('^%s$', str_replace('.', '\.', $host));
}

/**
 * Paths.
 */
$settings['file_chmod_directory'] = 02775;

$docroot_base = realpath(DRUPAL_ROOT . '/..');

$settings['file_public_path'] = "sites/default/files";
$settings['file_private_path'] = $docroot_base . '/private';
$settings['file_temp_path'] = $docroot_base . '/tmp';

/**
 * Mapbox.
 */
$config['farm_map_mapbox.settings']['api_key'] = 'pk.eyJ1IjoibmF0LWZvci1hdXRoLXVnIiwiYSI6ImNsZGlvd255YjAydDUzbmxndDB4MzU5YnEifQ.u8Tb2PvtKSsxE0AsYJM-Qg';

/**
 * Environment indicator.
 */
if ($_SERVER['APP_DOMAIN'] == 'forests.nfa.go.ug') {
  $config['environment_indicator.indicator']['bg_color'] = '#EF5621';
  $config['environment_indicator.indicator']['fg_color'] = '#FFFFFF';
  $config['environment_indicator.indicator']['name'] = 'Production';
}
elseif ($_SERVER['APP_DOMAIN'] == 'forests.stg.envs.utils.nfa.go.ug') {
  $config['environment_indicator.indicator']['bg_color'] = '#F8A519';
  $config['environment_indicator.indicator']['fg_color'] = '#FFFFFF';
  $config['environment_indicator.indicator']['name'] = 'Staging';
}

// GFW variables.
$settings['farm_nfa'] = [
  'gfw_api_key' => $_ENV['GFW_API_KEY'],
  'gfw_api_user' => $_ENV['GFW_API_USER'],
  'gfw_api_password' => $_ENV['GFW_API_PASSWORD'],
];
