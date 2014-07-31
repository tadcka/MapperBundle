<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Handler;

use Symfony\Component\HttpFoundation\Request;
use Tadcka\Component\Mapper\MapperItemInterface;
use Tadcka\Component\Mapper\Model\CategoryInterface;
use Tadcka\Component\Mapper\Model\Manager\CategoryManagerInterface;
use Tadcka\Component\Mapper\Model\Manager\MappingManagerInterface;
use Tadcka\Component\Mapper\Model\MappingInterface;
use Tadcka\Component\Mapper\Model\SourceInterface;
use Tadcka\Component\Mapper\Provider\MapperProviderInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/31/14 11:21 PM
 */
class MappingHandler
{
    /**
     * @var MapperProviderInterface
     */
    private $provider;

    /**
     * @var CategoryManagerInterface
     */
    private $categoryManager;

    /**
     * @var MappingManagerInterface
     */
    private $mappingManager;

    /**
     * Constructor.
     *
     * @param MapperProviderInterface $provider
     * @param CategoryManagerInterface $categoryManager
     * @param MappingManagerInterface $mappingManager
     */
    public function __construct(
        MapperProviderInterface $provider,
        CategoryManagerInterface $categoryManager,
        MappingManagerInterface $mappingManager
    ) {
        $this->provider = $provider;
        $this->categoryManager = $categoryManager;
        $this->mappingManager = $mappingManager;
    }

    /**
     * Process.
     *
     * @param Request $request
     * @param SourceInterface $source
     * @param SourceInterface $otherSource
     * @param string $categorySlug
     *
     * @return bool
     */
    public function process(Request $request, SourceInterface $source, SourceInterface $otherSource, $categorySlug)
    {
        $itemSlugs = $request->get('mapper_item', array());

        $mapper = $this->provider->getMapper($otherSource, $request->getLocale());
        if ($this->validateCategorySlugs($itemSlugs, $mapper)) {
            $mappings = $this->mappingManager->findManyMappings($categorySlug, $source->getSlug());
            $this->removeNonExistingMappings($categorySlug, $mappings, $itemSlugs);

            $category = $this->categoryManager->findBySlugAndSource($categorySlug, $source);
            $mappingCategories = array();
            if (null !== $category) {
                $mappingCategories = $this->provider->getMappingCategories($category, $otherSource);
            } else {
                $category = $this->categoryManager->create();
                $category->setSlug($categorySlug);
                $category->setSource($source);

                $this->categoryManager->add($category);
            }

            $newCategories = $this->createNonExistingCategories($itemSlugs, $mappingCategories, $otherSource);
            $newMappings = $this->createNonExistingMappings($category, $newCategories);

            $this->setMainMapping($request->get('main_mapper_item', null), array_merge($mappings, $newMappings));

            return true;
        }

        return false;
    }

    /**
     * Set main mapping
     *
     * @param null|string $categorySlug
     * @param array|MappingInterface[] $mappings
     */
    private function setMainMapping($categorySlug, array $mappings)
    {
        foreach ($mappings as $mapping) {
            $mapping->setMain(false);
            if (null !== $categorySlug) {
                if ($categorySlug === $mapping->getLeft()->getSlug()) {
                    $mapping->setMain(true);
                } elseif ($categorySlug === $mapping->getRight()->getSlug()) {
                    $mapping->setMain(true);
                }
            }
        }

        if ((null === $categorySlug) && isset($mappings[0])) {
            $mappings[0]->setMain(true);
        }
    }

    /**
     * Remove non existing mappings.
     *
     * @param string $categorySlug
     * @param array|MappingInterface[] $mappings
     * @param array $slugs
     */
    private function removeNonExistingMappings($categorySlug, array $mappings, array $slugs)
    {
        foreach ($mappings as $mapping) {
            $remove = true;
            if ($categorySlug === $mapping->getLeft()->getSlug()) {
                if ($this->hasCategorySlug($mapping->getRight(), $slugs)) {
                    $remove = false;
                }
            } elseif ($categorySlug === $mapping->getRight()->getSlug()) {
                if ($this->hasCategorySlug($mapping->getLeft(), $slugs)) {
                    $remove = false;
                }
            }

            if ($remove) {
                $this->mappingManager->remove($mapping);
            }
        }
    }

    /**
     * Validate category slugs.
     *
     * @param array $slugs
     * @param MapperItemInterface $mapperItem
     *
     * @return bool
     */
    private function validateCategorySlugs(array $slugs, MapperItemInterface $mapperItem)
    {
        $isValid = true;
        foreach ($slugs as $slug) {
            if (null === $this->provider->getMapperItemByCategory($slug, $mapperItem)) {
                $isValid = false;

                break;
            }
        }

        return $isValid;
    }

    /**
     * Create non existing mappings.
     *
     * @param CategoryInterface $category
     * @param array|CategoryInterface[] $otherCategories
     *
     * @return array|MappingInterface[]
     */
    private function createNonExistingMappings(CategoryInterface $category, array $otherCategories)
    {
        $mappings = array();
        foreach ($otherCategories as $otherCategory) {
            $mapping = $this->mappingManager->create();
            $mapping->setLeft($category);
            $mapping->setRight($otherCategory);

            $this->mappingManager->add($mapping);
            $mappings[] = $mapping;
        }

        return $mappings;
    }

    /**
     * Create non existing categories.
     *
     * @param array $slugs
     * @param array|CategoryInterface[] $existingCategories
     * @param SourceInterface $source
     *
     * @return array|CategoryInterface[]
     */
    private function createNonExistingCategories(array $slugs, array $existingCategories, SourceInterface $source)
    {
        $categories = array();
        foreach ($slugs as $slug) {
            $create = true;
            foreach ($existingCategories as $existingCategory) {
                if ($slug === $existingCategory->getSlug()) {
                    $create = false;

                    break;
                }
            }

            if ($create) {
                $category = $this->categoryManager->create();
                $category->setSlug($slug);
                $category->setSource($source);

                $this->categoryManager->add($category);
                $categories[] = $category;
            }
        }

        return $categories;
    }

    /**
     * Has category slug.
     *
     * @param string $categorySlug
     * @param array $slugs
     *
     * @return bool
     */
    private function hasCategorySlug($categorySlug, array $slugs)
    {
        foreach ($slugs as $slug) {
            if ($slug === $categorySlug) {
                return true;
            }
        }

        return false;
    }
}
