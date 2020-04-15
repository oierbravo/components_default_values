<?php

namespace Drupal\components_default_values;

/**
 * Simple Twig extension to integrate GraphQL.
 */
class ComponentsDefaultValuesTwigExtension extends \Twig_Extension {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return get_class($this);
  }

  /**
   * {@inheritdoc}
   */
  public function getTokenParsers() {
    return [new ComponentsDefaultValuesTokenParser()];
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeVisitors() {
    return [new ComponentsDefaultValuesNodeVisitor()];
  }

}
