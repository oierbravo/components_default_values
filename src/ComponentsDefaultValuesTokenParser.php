<?php

namespace Drupal\components_default_values;

use Twig_Error_Syntax;
use Twig_Token;
use Drupal\Component\Serialization\Json;

/**
 * Parse the `defaultvalues` twig tag.
 *
 * Parses the `{% defaultvalues %}` twig tag. Only allowed on template root level.
 */
class ComponentsDefaultValuesTokenParser extends \Twig_TokenParser {

  /**
   * {@inheritdoc}
   */
  public function parse(Twig_Token $token) {
    $stream = $this->parser->getStream();
    if (!$this->parser->isMainScope()) {
      throw new Twig_Error_Syntax(
        'GraphQL queries cannot be defined in blocks.',
        $token->getLine(),
        $stream->getSourceContext()
      );
    }

    $stream->expect(Twig_Token::BLOCK_END_TYPE);
    $values = $this->parser->subparse([$this, 'decideBlockEnd'], TRUE);
    $stream->expect(Twig_Token::BLOCK_END_TYPE);
    if ($values instanceof \Twig_Node_Text) {
     try {
        return new ComponentsDefaultValuesNode(Json::decode($values->getAttribute('data')), $token->getLine(), $this->getTag());
      }
      catch (SyntaxError $error) {
        throw new Twig_Error_Syntax(
          $error->getMessage(),
          $token->getLine(),
          $stream->getSourceContext()
        );
      }

    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function decideBlockEnd(Twig_Token $token) {
    return $token->test('enddefaultvalues');
  }

  /**
   * {@inheritdoc}
   */
  public function getTag() {
    return 'defaultvalues';
  }

}
