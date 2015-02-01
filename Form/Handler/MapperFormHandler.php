<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Tadcka\Mapper\Model\Manager\MappingItemManagerInterface;
use Tadcka\Mapper\Model\Manager\MappingManagerInterface;
use Tadcka\Mapper\Model\Manager\MappingSourceManagerInterface;
use Tadcka\Mapper\Model\MappingInterface;
use Tadcka\Mapper\Model\MappingItemInterface;
use Tadcka\Mapper\Model\MappingSourceInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/31/15 7:41 PM
 */
class MapperFormHandler
{
    /**
     * @var MappingManagerInterface
     */
    private $mappingManager;

    /**
     * @var MappingItemManagerInterface
     */
    private $itemManager;

    /**
     * @var MappingSourceManagerInterface
     */
    private $sourceManager;

    /**
     * Constructor.
     *
     * @param MappingManagerInterface $mappingManager
     * @param MappingItemManagerInterface $itemManager
     * @param MappingSourceManagerInterface $sourceManager
     */
    public function __construct(
        MappingManagerInterface $mappingManager,
        MappingItemManagerInterface $itemManager,
        MappingSourceManagerInterface $sourceManager
    ) {
        $this->mappingManager = $mappingManager;
        $this->itemManager = $itemManager;
        $this->sourceManager = $sourceManager;
    }


    /**
     * Process mapper form.
     *
     * @param FormInterface $form
     * @param Request $request
     *
     * @return bool
     */
    public function process(FormInterface $form, Request $request)
    {
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $source = $this->getMappingSource($data['source']);
                $otherSource = $this->getMappingSource($data['otherSource']);
                $item = $this->getMappingItem($data['itemId'], $source);

                $mappings = $this->getCleanMappings(
                    $data['mappings'],
                    $item->getSlug(),
                    $source->getSlug(),
                    $otherSource->getSlug()
                );


                $hasMain = false;
                foreach ($data['mappings'] as $postMapping) {
                    if (false === isset($mappings[$postMapping['item']])) {
                        $mappings[$postMapping['item']] = $this->createMapping(
                            $item,
                            $this->getMappingItem($postMapping['item'], $otherSource)
                        );
                    }

                    if (false === $hasMain) {
                        $mappings[$postMapping['item']]->setMain($postMapping['main']);
                    } else {
                        $mappings[$postMapping['item']]->setMain(false);
                    }

                    $hasMain = $hasMain || $postMapping['main'];
                }

                if (false === $hasMain && $mapping = reset($mappings)) {
                    $mapping->setMain(true);
                }

                return true;
            }
        }

        return false;
    }

    public function onSuccess()
    {
        $this->mappingManager->save();
    }

    /**
     * Get mapping source.
     *
     * @param string $slug
     *
     * @return null|MappingSourceInterface
     */
    private function getMappingSource($slug)
    {
        $source = $this->sourceManager->findBySlug($slug);
        if (null === $source) {
            $source = $this->sourceManager->create();
            $source->setSlug($slug);

            $this->sourceManager->add($source);
        }

        return $source;
    }

    /**
     * Get mapping item.
     *
     * @param string $slug
     * @param MappingSourceInterface $source
     *
     * @return null|MappingItemInterface
     */
    private function getMappingItem($slug, MappingSourceInterface $source)
    {
        $item = $this->itemManager->findBySlugAndSource($slug, $source);
        if (null === $item) {
            $item = $this->itemManager->create();
            $item->setSlug($slug);
            $item->setSource($source);

            $this->itemManager->add($item);
        }

        return $item;
    }

    /**
     * Get clean mappings.
     *
     * @param array $postMappings
     * @param string $itemId
     * @param string $sourceSlug
     * @param string $otherSourceSlug
     *
     * @return array|MappingInterface
     */
    private function getCleanMappings(array $postMappings, $itemId, $sourceSlug, $otherSourceSlug)
    {
        $mappings = $this->mappingManager
            ->findBySourceItemId($itemId, $sourceSlug, $otherSourceSlug);

        $data = [];
        foreach ($mappings as $mapping) {
            $deleted = true;

            if ($otherSourceSlug === $mapping->getLeftItem()->getSource()->getSlug()) {
                $item = $mapping->getLeftItem();
            } else {
                $item = $mapping->getRightItem();
            }
            foreach ($postMappings as $postMapping) {
                if ($item->getSlug() === $postMapping['item']) {
                    $deleted = false;
                }
            }

            if ($deleted) {
                $this->mappingManager->remove($mapping);
            } else {
                $data[$item->getSlug()] = $mapping;
            }
        }

        return $data;
    }

    /**
     * Create mapping.
     *
     * @param MappingItemInterface $leftItem
     * @param MappingItemInterface $rightItem
     *
     * @return MappingInterface
     */
    private function createMapping(MappingItemInterface $leftItem, MappingItemInterface $rightItem)
    {
        $mapping = $this->mappingManager->create();
        $mapping->setLeftItem($leftItem);
        $mapping->setRightItem($rightItem);

        $this->mappingManager->add($mapping);

        return $mapping;
    }
}
