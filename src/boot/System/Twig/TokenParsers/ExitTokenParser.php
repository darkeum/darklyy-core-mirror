<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace Boot\System\Twig\TokenParsers;

use \Boot\System\Twig\Nodes\ExitNode;
use \Twig\Parser;
use \Twig\Token;
use \Twig\TokenParser\AbstractTokenParser;

/**
 * Class ExitTokenParser
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
class ExitTokenParser extends AbstractTokenParser
{
    /**
     * @inheritdoc
     */
    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        /** @var Parser $parser */
        $parser = $this->parser;
        $stream = $parser->getStream();

        $nodes = [];

        if ($stream->test(Token::NUMBER_TYPE)) {
            $nodes['status'] = $parser->getExpressionParser()->parseExpression();
        }

        $stream->expect(Token::BLOCK_END_TYPE);
        // dd('fffff');
        return new ExitNode($nodes, [], $lineno, $this->getTag());
    }

    /**
     * @inheritdoc
     */
    public function getTag()
    {
        return 'exit';
    }
}
