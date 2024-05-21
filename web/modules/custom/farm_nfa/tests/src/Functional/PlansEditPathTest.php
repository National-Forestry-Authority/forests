<?php

namespace Drupal\farm_nfa\Tests\Functional;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests the NFA Plan edit path.
 *
 * @group farm_nfa
 */
class PlansEditPathTest extends BrowserTestBase {

  protected $defaultTheme = 'nfa_gin_forests';
  protected $profile = 'farm';

  /**
   * {@inheritdoc}
   */
  protected $strictConfigSchema = FALSE;
  /**
   * User with content access.
   *
   * @var \Drupal\user\Entity\User|false
   */
  protected $normal_user;

  public function setUp(): void {
    parent::setUp();
    $this->container->get('theme_installer')->install(['nfa_gin_forests'], TRUE);
    $this->container->get('config.factory')->getEditable('system.theme')->set('default', 'nfa_gin_forests')->save();
    // Delete conflicting configurations.
    $this->deleteConflictingConfigurations();
    // Clear the cache to ensure changes are reflected
    $this->normal_user = $this->drupalCreateUser(['access content'], NULL, TRUE);
    $this->normal_user->addRole('administrator');
    $this->normal_user->save();
  }

  /**
   * Delete conflicting configurations before enabling the module.
   */
  protected function deleteConflictingConfigurations() {
    $output = [];
    $return_var = 0;
    exec('dev drush updb', $output, $return_var);
    // \Drupal::configFactory()->getEditable('farm_nfa_config.settings')->delete();
    // \Drupal::configFactory()->getEditable('devel.toolbar.settings')->delete();
    // \Drupal::configFactory()->getEditable('system.menu.devel')->delete();
    // $config_factory = \Drupal::configFactory();
    // //  $this->container->get('config.factory');

    // // List of configurations that might conflict.
    // $configurations = [
    //   'system.action.user_add_role_action.farm_viewer',
    // ];

    // // Delete each configuration if it exists.
    // foreach ($configurations as $config_name) {
    //   $config = $config_factory->getEditable($config_name);
    //   $config->delete();
    //   // if (!$config->isNew()) {
    //   //   $config->delete();
    //   // }
    // }

    // // Clear the cache to ensure changes are reflected.
    // $this->container->get('cache.factory')->get('config')->deleteAll();
  }

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['farm_nfa'];

  /**
   * Tests the plan permission form.
   */
  public function testPlanPermissionForm() {
    $this->drupalLogin($this->normal_user);
    $this->drupalGet('/');
    $this->assertSession()->statusCodeEquals(200);
  }

}
