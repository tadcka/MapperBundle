<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Twig\Extension;

use JMS\Serializer\SerializerInterface;
use Tadcka\Mapper\Source\Source;
use Tadcka\Mapper\Source\SourceMetadataFactory;
use Twig_Environment as Twig;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 8/26/14 11:03 PM
 */
class MapperExtension extends \Twig_Extension
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * Constructor.
     *
     * @param SerializerInterface $serializer
     * @param Twig $twig
     */
    public function __construct(SerializerInterface $serializer, Twig $twig)
    {
        $this->serializer = $serializer;
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('mapper_source_render', [$this, 'sourceRender'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('mapper_source_metadata', [$this, 'getSourceMetadata']),
        ];
    }

    /**
     * Get mapper source metadata.
     *
     * @param Source $source
     *
     * @return string
     */
    public function getSourceMetadata(Source $source)
    {
        return $this->serializer->serialize(SourceMetadataFactory::create($source), 'json');
    }

    /**
     * Source render.
     *
     * @param Source $source
     * @param Source $otherSource
     *
     * @return string
     */
    public function sourceRender(Source $source, Source $otherSource)
    {
        $html = '';
        switch ($source->getTypeName()) {
            case 'mapper_tree':
                $html = $this->twig->render(
                    'TadckaMapperBundle:Type:tree.html.twig',
                    [
                        'source' => $source,
                        'other_source' => $otherSource,
                    ]
                );

                break;
        }

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tadcka_mapper';
    }
}
