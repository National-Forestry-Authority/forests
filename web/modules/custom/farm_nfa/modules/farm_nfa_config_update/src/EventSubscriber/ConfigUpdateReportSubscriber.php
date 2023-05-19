<?php

namespace Drupal\farm_nfa_config_update\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ConfigUpdateReportSubscriber.
 *
 * Adds a new section to the Config Update report that lists Farm NFA overrides
 * of farmOS, core and contrib configuration that should not be reverted.
 */
class ConfigUpdateReportSubscriber implements EventSubscriberInterface {
  use StringTranslationTrait;

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new CustomPageExceptionHtmlSubscriber.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Alters the controller output.
   */
  public function onView(ViewEvent $event) {
    $request = $event->getRequest();
    $route = $request->attributes->get('_route');

    if ($route == 'config_update_ui.report') {
      $build = $event->getControllerResult();
      if (is_array($build) && isset($build['report'])) {
        // Have we listed any configuration overrides?
        $overrides = $this->configFactory->get('farm_nfa_config_update.settings')->get('config_overrides');
        if ($overrides) {
          // Create a report section for NFA overrides and move the overrides
          // out of the changed section into the new section.
          $nfa_overrides = $build['report']['different'];
          $nfa_overrides['#rows'] = [];
          $nfa_overrides['#empty'] = $this->t('None: no NFA overrides of active configuration exist');

          $items = array_filter(preg_split("/(\r\n|\n|\r)/", $overrides));
          foreach ($build['report']['different']['#rows'] as $index => $row) {
            if (in_array($row[0], $items)) {
              // Remove the revert operation so that overridden configuration
              // can't be accidentally reverted.
              unset($row[4]['data']['#links']['revert']);
              // Add the overrides to NFA overrides section and remove them
              // from the differences section.
              $nfa_overrides['#rows'][] = $row;
              unset($build['report']['different']['#rows'][$index]);
            }
          }
          $build['report']['nfa'] = [
            '#type' => 'details',
            '#title' => $this->t('NFA overrides of configuration items'),
            '#description' => $this->t('These configuration items have been overridden and should not be reverted.'),
            '#open' => FALSE,
            'children' => $nfa_overrides,
          ];
        }
        // Do we want to exclude any missing or inactive optional items?
        $exclusions = $this->configFactory->get('farm_nfa_config_update.settings')->get('config_exclusions');
        if ($exclusions) {
          // Delete excluded items from the missing and inactive reports.
          foreach (['removed', 'inactive'] as $report) {
            $items = array_filter(preg_split("/(\r\n|\n|\r)/", $exclusions));
            foreach ($build['report'][$report]['#rows'] as $index => $row) {
              if (in_array($row[0], $items)) {
                unset($build['report'][$report]['#rows'][$index]);
              }
            }
          }

        }
      }

      // Save the changes to the config update report.
      $event->setControllerResult($build);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Set priority > 0 so that it runs before the controller output is
    // rendered by \Drupal\Core\EventSubscriber\MainContentViewSubscriber.
    $events[KernelEvents::VIEW][] = ['onView', 50];
    return $events;
  }

}
