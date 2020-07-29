<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_theme\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests that our Tender content type render.
 */
class ContentTendersRenderTest extends BrowserTestBase {

  /**
   * The node storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'config',
    'system',
    'oe_theme_helper',
    'path',
    'oe_theme_content_tender',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Enable and set OpenEuropa Theme as default.
    \Drupal::service('theme_installer')->install(['oe_theme']);
    \Drupal::configFactory()->getEditable('system.theme')->set('default', 'oe_theme')->save();
  }

  /**
   * Tests that the Tender page renders correctly.
   */
  public function testTenderRendering(): void {
    // Create a document for Tender results.
    $file = file_save_data(file_get_contents(drupal_get_path('module', 'oe_media') . '/tests/fixtures/sample.pdf'), 'public://test.pdf');
    $file->setPermanent();
    $file->save();

    $media = $this->getStorage('media')->create([
      'bundle' => 'document',
      'name' => 'Test document',
      'oe_media_file' => [
        'target_id' => (int) $file->id(),
      ],
      'uid' => 0,
      'status' => 1,
    ]);

    $media->save();

    // Create a Project node.
    /** @var \Drupal\node\Entity\Node $node */
    $node = $this->getStorage('node')->create([
      'type' => 'oe_tender',
      'title' => 'Test tender node',
      'body' => 'Body',
      'oe_tender_deadlines' => [
        'value' => '2020-05-10',
        'end_value' => '2025-05-15',
      ],
      'oe_documents' => [
        [
          'target_id' => (int) $media->id(),
        ],
      ],
      'oe_summary' => '100',
      'oe_tender_opening_date' => [
        'value' => '2020-05-10',
        'end_value' => '2025-05-15',
      ],
      'oe_publication_date' => [
        'value' => '2020-05-10',
        'end_value' => '2025-05-15',
      ],
      'oe_reference' => '100',
      'oe_departments' => '',
      'oe_subject' => 'Project reference',
      'oe_teaser' => '',
      'uid' => 0,
      'status' => 1,
    ]);
    $node->save();

    $this->drupalGet($node->toUrl());

    // Assert in-line navigation.
    // Assert Details group.
    // Assert Description field.
    // Assert Documents field.
    // Assert status empty.
    // Assert status open.
    // Assert status closed.
    // Assert status upcoming.
    // Assert strike deadlines.
  }

}
