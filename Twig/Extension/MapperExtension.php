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

use Tadcka\Mapper\MapperSource;
use Twig_Environment as Twig;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 8/26/14 11:03 PM
 */
class MapperExtension extends \Twig_Extension
{
    /**
     * @var Twig
     */
    private $twig;

    /**
     * Constructor.
     *
     * @param Twig $twig
     */
    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('mapper_render', [$this, 'renderMapper'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
//    public function getFilters()
//    {
////        return array(
////            new \Twig_SimpleFilter('mapper_item_full_path', array($this, 'getMapperItemFullPath')),
////        );
//    }

    public function renderMapper(MapperSource $mapperSource, $flag)
    {
        $html = '';
        switch ($mapperSource->getTypeName()) {
            case 'mapper_tree':
                $html = $this->twig->render(
                    'TadckaMapperBundle:Type:tree.html.twig',
                    [
                        'flag' => $flag,
                        'mapper_source' => $mapperSource
                    ]
                );

                break;
        }

        return $html;
    }

    /**
     * Get mapper item full path.
     *
     * @param MapperItemInterface $item
     * @param string $sourceSlug
     * @param string $locale
     *
     * @return string
     */
//    public function getMapperItemFullPath(MapperItemInterface $item, $sourceSlug, $locale)
//    {
//        $mapperTree = $this->mapperProvider->getMapper($this->mapperProvider->getSource($sourceSlug), $locale);
//        $result = '';
//        foreach ($this->mapperHelper->getMapperItemFullPath($item->getSlug(), $mapperTree) as $value) {
//            $result .= $value . ' / ';
//        }
//
//        return rtrim($result, ' / ');
//    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tadcka_mapper';
    }
}
