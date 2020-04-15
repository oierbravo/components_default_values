<?php

namespace Drupal\components_default_values;

use Twig_Compiler;

/**
 * GraphQL meta information Twig node.
 *
 * A Twig node that will be attached to templates `class_end` to output the
 * collected graphql query and inheritance metadata. Not parsed directly but
 * injected by the `GraphQLNodeVisitor`.
 */
class ComponentsDefaultValuesNode extends \Twig_Node {

  /**
   * The modules default values.
   *
   * @var array
   */
  public $defaultValues = [];

  /**
   * The modules parent class.
   *
   * @var string
   */
  protected $parent = "";

  /**
   * The modules includes.
   *
   * @var array
   */
  protected $includes = [];

  /**
   * Boolean indicator if this fragment includes operations.
   *
   * @var bool
   */

  /**
   * DefaultValues constructor.
   *
   * @param string $defaultValues
   *   The query string.
   * @param string $parent
   *   The parent template identifier.
   * @param array $includes
   *   Identifiers for any included/referenced templates.
   */
  //public function __construct($defaultValues, $parent = [], $includes = []) {
  public function __construct($defaultValues, $line, $tag = null) {
    $this->defaultValues = $defaultValues;

    parent::__construct([], ['defaultValues' => $defaultValues], $line, $tag);
  }

  /**
   * {@inheritdoc}
   */
  public function compile(\Twig_Compiler $compiler)
  {
    $compiler
      ->write('$context = array_replace_recursive(')
      ->repr($this->getAttribute('defaultValues'))
     // ->write(', $context')
      ->raw(', $context);');
  }

}
