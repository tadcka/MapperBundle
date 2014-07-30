<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Tadcka\Component\Mapper\MapperItemInterface;
use Tadcka\Component\Mapper\Model\Manager\CategoryManagerInterface;
use Tadcka\Component\Mapper\Model\SourceInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/22/14 11:06 PM
 */
class MapperController extends ContainerAware
{
    public function getMappingAction(Request $request, $sourceSlug, $otherSourceSlug, $categorySlug)
    {
        $source = $this->getSource($sourceSlug);
        $otherSource = $this->getSource($otherSourceSlug);

        $mapper = $this->getProvider()->getMapper($source, $request->getLocale());

        $mapperItem = $this->getProvider()->getMapperItemByCategory($categorySlug, $mapper);
        if ($this->isValidMapperItem($mapperItem)) {
            return new Response($this->getMapperItemErrorHtml($mapperItem));
        }

        $category = $this->getCategoryManager()->findBySlugAndSource($categorySlug, $source);

        $mapperItems = array();
        if (null !== $category) {
            $mapperItems = $this->getProvider()->getMapperItemByCategory(
                $this->getProvider()->getMappingCategories($category, $otherSource),
                $mapper
            );
        }

        return new Response(
            $this->getTemplating()->render(
                'TadckaMapperBundle:Mapper:mapper.html.twig',
                array(
                    'items' => $mapperItems,
                    'source_slug' => $sourceSlug,
                    'other_source_slug' => $otherSourceSlug,
                    'category_slug' => $categorySlug,
                )
            )
        );
    }

    public function postMappingAction(Request $request, $sourceSlug, $otherSourceSlug, $categorySlug)
    {

    }

    public function addMappingAction(Request $request, $sourceSlug, $categorySlug)
    {
        $source = $this->getSource($sourceSlug);
        $mapperItem = $this->getProvider()->getMapperItemByCategory(
            $categorySlug,
            $this->getProvider()->getMapper($source, $request->getLocale())
        );

        if ($this->isValidMapperItem($mapperItem)) {
            return new Response($this->getMapperItemErrorHtml($mapperItem));
        }

        return new Response(
            $this->getTemplating()->render(
                'TadckaMapperBundle:Mapper:mapper_item.html.twig',
                array(
                    'item' => $mapperItem,
                )
            )
        );
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return $this->container->get('translator');
    }

    /**
     * @return EngineInterface
     */
    private function getTemplating()
    {
        return $this->container->get('templating');
    }

    /**
     * @return CategoryManagerInterface
     */
    private function getCategoryManager()
    {
        return $this->container->get('tadcka_mapper.manager.category');
    }

    private function getProvider()
    {
        return $this->container->get('tadcka_mapper.provider');
    }

    /**
     * Get source.
     *
     * @param string $sourceSlug
     *
     * @return null|SourceInterface
     *
     * @throws NotFoundHttpException
     */
    private function getSource($sourceSlug)
    {
        $source = $this->getProvider()->getSource($sourceSlug);
        if (null === $source) {
            throw new NotFoundHttpException('Not found source by slug: ' . $sourceSlug . '!');
        }

        return $source;
    }

    /**
     * Is valid mapper item.
     *
     * @param MapperItemInterface $mapperItem
     *
     * @return bool
     */
    private function isValidMapperItem(MapperItemInterface $mapperItem = null)
    {
        return ((null === $mapperItem) || (false === $mapperItem->canUseForMapping()));
    }

    /**
     * Get mapper item error html.
     *
     * @param MapperItemInterface $mapperItem
     *
     * @return string
     */
    private function getMapperItemErrorHtml(MapperItemInterface $mapperItem = null)
    {
        return $this->getTemplating()->render(
            'TadckaMapperBundle:Mapper:error.html.twig',
            array(
                'message' => $this->getTranslator()
                        ->trans(
                            'category_can_not_used_mapping',
                            array('%category%' => $mapperItem ? $mapperItem->getName() : null),
                            'TadckaMapperBundle'
                        ),
            )
        );
    }
}
