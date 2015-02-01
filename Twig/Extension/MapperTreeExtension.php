<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Twig\Extension;

use JMS\Serializer\SerializerInterface;
use Tadcka\Bundle\MapperBundle\Frontend\Icons;
use Tadcka\JsTreeBundle\Model\Node;
use Tadcka\Mapper\Extension\Source\Tree\MapperTree;
use Tadcka\Mapper\Extension\Source\Tree\MapperTreeHelper;
use Tadcka\Mapper\Extension\Source\Tree\MapperTreeItemInterface;
use Tadcka\Mapper\Mapping\MappingProviderInterface;
use Tadcka\Mapper\Source\Source;
use Tadcka\Mapper\Source\SourceMetadata;
use Tadcka\Mapper\Source\SourceProvider;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/27/15 11:13 PM
 */
class MapperTreeExtension extends \Twig_Extension
{

    /**
     * @var MappingProviderInterface
     */
    private $mappingProvider;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var SourceProvider
     */
    private $sourceProvider;

    /**
     * @var MapperTreeHelper
     */
    private $treeHelper;

    /**
     * @var array
     */
    private $count;

    /**
     * Constructor.
     *
     * @param MappingProviderInterface $mappingProvider
     * @param SerializerInterface $serializer
     * @param SourceProvider $sourceProvider
     * @param MapperTreeHelper $treeHelper
     */
    public function __construct(
        MappingProviderInterface $mappingProvider,
        SerializerInterface $serializer,
        SourceProvider $sourceProvider,
        MapperTreeHelper $treeHelper
    ) {
        $this->mappingProvider = $mappingProvider;
        $this->serializer = $serializer;
        $this->sourceProvider = $sourceProvider;
        $this->treeHelper = $treeHelper;
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('mapper_tree_data', [$this, 'getTreeData']),
            new \Twig_SimpleFunction('mapper_tree_full_path', [$this, 'getTreeItemFullPath']),
        ];
    }

    /**
     * Get tree data.
     *
     * @param Source $source
     * @param Source $otherSource
     *
     * @return string
     */
    public function getTreeData(Source $source, Source $otherSource)
    {
        $this->count = $this->mappingProvider->getCountOfEachItem($source->getName(), $otherSource->getName());

        return $this->serializer->serialize($this->getNode($source->getData()->getTree()), 'json');
    }

    /**
     * {@inheritdoc}
     */
    private function getNode(MapperTreeItemInterface $treeItem)
    {
        $children = [];
        foreach ($treeItem->getChildren() as $child) {
            $children[] = $this->getNode($child);
        }

        $icon = ($treeItem->isActive() ? Icons::CAN_USE_FOR_MAPPING : Icons::CAN_NOT_USE_FOR_MAPPING);
        $title = $treeItem->getTitle();

        if (isset($this->count[$treeItem->getId()])) {
            $icon = Icons::IS_MAPPING;
            $title .= ' (' . $this->count[$treeItem->getId()] . ')';
        }

        return new Node($treeItem->getId(), $title, $children, $icon);
    }

    public function getTreeItemFullPath($itemId, SourceMetadata $metadata)
    {
        $data = $this->sourceProvider->getData($metadata);

        $path = $this->treeHelper->getTreeItemFullPath($itemId, $data->getTree());

        return implode(' / ', $path);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tadcka_mapper_tree';
    }
}
