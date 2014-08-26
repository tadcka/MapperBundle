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

use Tadcka\Component\Mapper\Helper\MapperHelper;
use Tadcka\Component\Mapper\MapperItem;
use Tadcka\Component\Mapper\MapperItemInterface;
use Tadcka\Component\Mapper\Provider\MapperProviderInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 8/26/14 11:03 PM
 */
class MapperExtension extends \Twig_Extension
{
    /**
     * @var MapperHelper
     */
    private $mapperHelper;

    /**
     * @var MapperProviderInterface
     */
    private $mapperProvider;

    /**
     * Constructor.
     *
     * @param MapperHelper $mapperHelper
     * @param MapperProviderInterface $mapperProvider
     */
    public function __construct(MapperHelper $mapperHelper, MapperProviderInterface $mapperProvider)
    {
        $this->mapperHelper = $mapperHelper;
        $this->mapperProvider = $mapperProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('mapper_item_full_path', array($this, 'getMapperItemFullPath')),
        );
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
    public function getMapperItemFullPath(MapperItemInterface $item, $sourceSlug, $locale)
    {
        $mapperTree = $this->mapperProvider->getMapper($this->mapperProvider->getSource($sourceSlug), $locale);
        $result = '';
        foreach ($this->mapperHelper->getMapperItemFullPath($item->getSlug(), $mapperTree) as $value) {
            $result .= $value . ' / ';
        }

        return rtrim($result, ' / ');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tadcka_mapper';
    }
}
