<?php

namespace Drupal\KernelTests\Core\Update;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests that extensions that are incompatible with the current core version are disabled.
 *
 * @group Update
 * @group legacy
 */
class CompatibilityFixTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['system'];

  protected function setUp(): void {
    parent::setUp();
    require_once $this->root . '/core/includes/update.inc';
  }

  /**
   * @expectedDeprecation update_fix_compatibility() is deprecated in Drupal 8.8.4 and will be removed before Drupal 9.0.0. There is no replacement. See https://www.drupal.org/node/3026100
   */
  public function testFixCompatibility() {
    $extension_config = \Drupal::configFactory()->getEditable('core.extension');

    // Add an incompatible/non-existent module to the config.
    $modules = $extension_config->get('module');
    $modules['incompatible_module'] = 0;
    $extension_config->set('module', $modules);
    $modules = $extension_config->get('module');
    $this->assertTrue(in_array('incompatible_module', array_keys($modules)), 'Added incompatible/non-existent module to the config.');

    // Add an incompatible/non-existent theme to the config.
    $themes = $extension_config->get('theme');
    $themes['incompatible_theme'] = 0;
    $extension_config->set('theme', $themes);
    $themes = $extension_config->get('theme');
    $this->assertTrue(in_array('incompatible_theme', array_keys($themes)), 'Added incompatible/non-existent theme to the config.');

    // Fix compatibility.
    update_fix_compatibility();
    $modules = $extension_config->get('module');
    $this->assertFalse(in_array('incompatible_module', array_keys($modules)), 'Fixed modules compatibility.');
    $themes = $extension_config->get('theme');
    $this->assertFalse(in_array('incompatible_theme', array_keys($themes)), 'Fixed themes compatibility.');
  }

}
