<?php

declare(strict_types=1);

namespace Drupal\Tests\config_split\Kernel;

use Drupal\config_split\Config\ConfigPatch;
use Drupal\Core\Config\StorageCopyTrait;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\config_filter\Kernel\ConfigStorageTestTrait;
use Drupal\user\Entity\Role;

/**
 * Test how roles are split as a proxy for other config with sequences.
 *
 * @group config_split
 */
class RolesSplittingTest extends KernelTestBase {

  use ConfigStorageTestTrait;
  use SplitTestTrait;
  use StorageCopyTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'config_split',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // We need the roles to play with.
    $this->installEntitySchema('user');
  }

  /**
   * Test splitting a module where a role has a permission from it.
   */
  public function testRoleSplit() {
    // We use permissions from this module and split it.
    $this->enableModules(['shortcut']);

    // Create a role with permissions from different modules.
    $role = Role::create([
      'id' => 'test_role',
      'label' => $this->randomString(),
      'permissions' => [
        // Permissions from the user module.
        'administer account settings',
        'administer permissions',

        // Permissions from the shortcut module in the middle.
        'customize shortcut links',

        // More permissions from the user module.
        'select account cancellation method',
        'view user email addresses',
      ],
    ]);
    $role->save();

    // Create a role which only depends on shortcut to test empty elements.
    $empty = Role::create([
      'id' => 'test_role_empty',
      'label' => $this->randomString(),
      'permissions' => [
        'customize shortcut links',
      ],
    ]);
    $empty->save();
    $empty->toArray();

    // Create a split for the shourtcut module.
    $this->createSplitConfig('test_split', [
      // We use the collection storage so that we can read the patch directly.
      'storage' => 'collection',
      'module' => ['shortcut' => 0],
    ]);

    // Run the export by accessing the export storage.
    $storage = $this->getExportStorage();

    // Permissions remain a sorted sequence aka list.
    $expectedPermissions = [
      'administer account settings',
      'administer permissions',
      'select account cancellation method',
      'view user email addresses',
    ];
    $exported = $storage->read('user.role.test_role');
    self::assertEquals($expectedPermissions, $exported['permissions']);

    // The patch should just contain shortcut things.
    $expectedPatch = ConfigPatch::fromArray([
      'adding' => [
        'dependencies' => ['module' => ['shortcut']],
        'permissions' => ['customize shortcut links'],
      ],
      'removing' => [],
    ]);
    foreach (['test_role', 'test_role_empty'] as $id) {
      // The patch looks the same for both.
      $patch = $storage->createCollection('split.test_split')->read('config_split.patch.user.role.' . $id);
      self::assertEquals($expectedPatch->toArray(), $patch);
    }

    $expectedRole = $empty->toArray();
    $expectedRole['permissions'] = [];
    $expectedRole['dependencies'] = [];
    self::assertEquals($expectedRole, $storage->read('user.role.test_role_empty'));
  }

}
