<?php

namespace Drupal\components_default_values;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Template\TwigEnvironment;

/**
 * Enhanced Twig environment for Components Default Values.
 *
 * Checks for GraphQL annotations in twig templates or matching `*.json` and
 * adds them as `{% defaultvalues %}` tags before passing them to the compiler.
 *
 * This is a convenience feature and also ensures that JSON-powered templates
 * don't break compatibility with Twig processors that don't have this extension
 * (e.g. patternlab).
 */
class ComponentsDefaultValuesEnvironment extends TwigEnvironment {


  /**
   * The renderer instance.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Retrieve the renderer instance.
   *
   * @return \Drupal\Core\Render\RendererInterface
   *   The renderer instance.
   */
  public function getRenderer() {
    return $this->renderer;
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $root,
    CacheBackendInterface $cache,
    $twig_extension_hash,
    StateInterface $state,
    \Twig_LoaderInterface $loader = NULL,
    array $options = [],
    RendererInterface $renderer = NULL
  ) {

    $this->renderer = $renderer;
    parent::__construct(
      $root,
      $cache,
      $twig_extension_hash,
      $state,
      $loader,
      $options
    );
  }

  /**
   * Regular expression to find a GraphQL annotation in a twig comment.
   *
   * @var string
   */
  public static $GRAPHQL_ANNOTATION_REGEX = '/{#defaultvalues\s+(.*?)\s+#\}/s';

  /**
   * {@inheritdoc}
   */
  public function compileSource($source, $name = NULL) {
    if ($source instanceof \Twig_Source) {
      // Check if there is a `*.gql` file with the same name as the template.
      $jsonFile = str_replace('.twig' , '.json',$source->getPath());
      if (file_exists($jsonFile)) {
        $source = new \Twig_Source(
          '{% defaultvalues %}' . file_get_contents($jsonFile) . '{% enddefaultvalues %}' . $source->getCode(),
          $source->getName(),
          $source->getPath()
        );
      }
      else {
        // Else, try to find an annotation.
        $source = new \Twig_Source(
          $this->replaceAnnotation($source->getCode()),
          $source->getName(),
          $source->getPath()
        );
      }

    }
    else {
      // For inline templates, only comment based annotations are supported.
      $source = $this->replaceAnnotation($source);
    }

    // Compile the modified source.
    return parent::compileSource($source, $name);
  }

  /**
   * Replace `{#defaultvalues ... #}` annotations with `{% defaultvalues ... %}` tags.
   *
   * @param string $code
   *   The template code.
   *
   * @return string
   *   The template code with all annotations replaced with tags.
   */
  public function replaceAnnotation($code) {
    return preg_replace(static::$GRAPHQL_ANNOTATION_REGEX, '{% defaultvalues %}$1{% enddefaultvalues %}', $code);
  }

}
