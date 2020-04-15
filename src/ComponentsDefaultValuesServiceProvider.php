<?php

namespace Drupal\components_default_values;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Service provider to inject a custom derivation of `TwigEnvironment`.
 */
class ComponentsDefaultValuesServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Replace the twig environment with the GraphQL enhanced one.
    $container->getDefinition('twig')
      ->setClass(ComponentsDefaultValuesEnvironment::class);
  }

}
