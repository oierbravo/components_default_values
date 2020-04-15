<?php
namespace Drupal\components_default_values;


use Twig_Environment;
use Twig_Node;

use Drupal\components_default_values\ComponentsDefaultValuesModuleNode;


class ComponentsDefaultValuesNodeVisitor extends \Twig_BaseNodeVisitor {

  /**
   * The query string.
   *
   * @var array
   */
  protected $defaultValues = [];


  /**
   * A list of referenced templates (include, embed).
   *
   * @var string[][]
   */
  protected $includes = [];

  /**
   * {@inheritdoc}
   */
  public function getPriority() {
    return 0;
  }
  /**
   * {@inheritdoc}
   */
  protected function doEnterNode(Twig_Node $node, Twig_Environment $env) {

    if ($node instanceof \Twig_Node_Module) {


      // Recurse into embedded templates.
      foreach ($node->getAttribute('embedded_templates') as $embed) {
        $this->doEnterNode($embed, $env);
      }
    }

    // Store identifiers of any static includes.
    // There is no way to make this work for dynamic includes.
    if ($node instanceof \Twig_Node_Include && !($node instanceof \Twig_Node_Embed)) {
      $ref = $node->getNode('expr');
      if ($ref instanceof \Twig_Node_Expression_Constant) {
        $this->includes[$node->getTemplateName()][] = $ref->getAttribute('value');
      }
    }

    if ($defaultValues = $this->getDefaultValues($node)) {
      // this is a CollectorNode, so collect the data
      $this->defaultValues = array_merge($this->defaultValues, $defaultValues);
      $a= '';
    }

    return $node;
  }
  /**
   * {@inheritdoc}
   */
  protected function doLeaveNode(Twig_Node $node, Twig_Environment $env) {
    if ($node instanceof \Twig_Node_Module) {

      $includes = isset($this->includes[$node->getTemplateName()]) ? $this->includes[$node->getTemplateName()] : [];
      $node->setAttribute('defaultValues', $this->defaultValues);
      $node->setAttribute('includes', $includes);
      // Reset query information for the next module.


      $this->defaultValues = [];
      $this->includes = [];
    }
    return $node;
  }
  private function getDefaultValues(\Twig_NodeInterface $node)
  {
    if ($node instanceof ComponentsDefaultValuesNode) {
      return $node->getAttribute('defaultValues');
    }

    return null;
  }
}
