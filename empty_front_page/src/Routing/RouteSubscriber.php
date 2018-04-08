<?php

/**
 * @file
 * Contains \Drupal\empty_front_page\Routing\RouteSubscriber.
 */

namespace Drupal\empty_front_page\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Subscriber for Empty Front page routes.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    //
    $front_page = \Drupal::config('system.site')->get('page.front');
    foreach ($collection as $route) {
      if ($route->getPath() == $front_page) {
        $route->setDefaults(['_controller' => [$this, 'content']]);
        break;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = parent::getSubscribedEvents();
    // Ensure to run after the views route subscriber.
    // @see \Drupal\views\EventSubscriber
    $events[RoutingEvents::ALTER] = ['onAlterRoutes', -180];
    return $events;
  }

  /**
   * Content callback.
   */
  public static function content() {
    return ['#markup' => ''];
  }

}
