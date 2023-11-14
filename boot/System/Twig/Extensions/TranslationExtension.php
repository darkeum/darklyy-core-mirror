<?php

namespace Boot\System\Twig\Extensions;

use InvalidArgumentException;
// use \Boot\Services\Translation;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Twig translation extension.
 */
final class TranslationExtension extends AbstractExtension
{
    /**
     * The translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * The constructor.
     *
     * @param TranslatorInterface $translator The translator
     *
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->translator = app('translation');
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('__', [$this, '__']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $twigFunction = new TwigFunction('__', [$this, '__']);
        $twigFunction->setArguments([]);

        return [$twigFunction];
    }

    /**
     * Translate callback.
     *
     * @return mixed
     */
    public function __()
    {
        $args = func_get_args();
        $parameters = array_slice($args, 1);

        return $this->translator->trans($args[0], $parameters);
    }
}
