<?php

declare(strict_types = 1);

namespace Drupal\oe_theme_content_tender\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\extra_field\Plugin\ExtraFieldDisplayFormattedBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use DateTime;

/**
 * Display Call for tenders status.
 *
 * @ExtraFieldDisplay(
 *   id = "oe_tender_status",
 *   label = @Translation("Call for tenders status"),
 *   bundles = {
 *     "node.oe_tender",
 *   },
 *   visible = true
 * )
 */
class TenderStatusExtraField extends ExtraFieldDisplayFormattedBase implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /**
   * The entity view builder.
   *
   * @var \Drupal\Core\Entity\EntityViewBuilder
   */
  protected $viewBuilder;

  /**
   * TenderStatusExtraField constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->viewBuilder = $entity_type_manager->getViewBuilder('node');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->t('Status');
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(ContentEntityInterface $entity) {
    $now = new DateTime();
    // Get opening date.
    if ($entity->get('oe_tender_opening_date')->isEmpty()) {
      $opening_date = '';
    }
    else {
      $opening_date = new DateTime($entity->get('oe_tender_opening_date')->value);
    }
    // Get the latest closing date, this field is mandatory.
    $closing_dates = $entity->get('oe_tender_deadlines')->getValue();
    $closing_date = array_pop($closing_dates);
    foreach ($closing_dates as $item) {
      $date = new DateTime($item['value']);
      if ($date > $closing_date) {
        $closing_date = $date;
      }
    }
    $build = [];
    if ($now < $opening_date) {
      $build = [
        '#markup' => '<div class="ecl-u-text-uppercase">' . $this->t('upcoming') . '</div>',
      ];
    }
    if ($opening_date < $now && $now < $closing_date) {
      $build = [
        '#markup' => '<div class="ecl-u-text-uppercase">' . $this->t('open') . '</div>',
      ];
    }
    if ($now > $closing_date) {
      $build = [
        '#markup' => '<div class="ecl-u-text-uppercase">' . $this->t('closed') . '</div>',
      ];
    }

    return $build;
  }

}
