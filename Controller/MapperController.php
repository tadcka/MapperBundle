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
use Tadcka\Component\Mapper\Model\Manager\CategoryManagerInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/22/14 11:06 PM
 */
class MapperController extends ContainerAware
{
    public function getMappingAction(Request $request, $sourceSlug, $otherSourceSlug, $categorySlug)
    {
        $source = $this->getProvider()->getSource($sourceSlug);
        if (null === $source) {
            throw new NotFoundHttpException('Not found source!');
        }

        $otherSource = $this->getProvider()->getSource($otherSourceSlug);
        if (null === $otherSource) {
            throw new NotFoundHttpException('Not found other source!');
        }

        $mapperItem = $this->getProvider()->getMapper($source, $request->getLocale());

        if (false === $this->getProvider()->canUseForMapping($categorySlug, $mapperItem)) {
            return new Response("Category can't use for mapping!");
        }

        $category = $this->getCategoryManager()->findBySlugAndSource($categorySlug, $source);


        $items = array();
        if (null !== $category) {
            $items = $this->getProvider()->getMappingCategories(
                $this->getProvider()->getMappingCategories($category, $otherSource),
                $mapperItem
            );
        }

        return new Response(
            $this->getTemplating()->render(
                'TadckaMapperBundle:Mapper:mapper.html.twig',
                array(
                    'items' => $items,
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

    public function addMappingAction(Request $request, $sourceSlug, $otherSourceSlug, $categorySlug)
    {
        $source = $this->getProvider()->getSource($sourceSlug);
        if (null === $source) {
            throw new NotFoundHttpException('Not found source!');
        }

        $otherSource = $this->getProvider()->getSource($otherSourceSlug);
        if (null === $otherSource) {
            throw new NotFoundHttpException('Not found other source!');
        }

        $item = $this->getProvider()->getMapperItemByCategory(
            $categorySlug,
            $this->getProvider()->getMapper($source, $request->getLocale())
        );

        if (null === $item) {
            return new Response("Category can't use for mapping!");
        }

        return new Response(
            $this->getTemplating()->render(
                'TadckaMapperBundle:Mapper:mapper_item.html.twig',
                array(
                    'item' => $item,
                )
            )
        );
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
}
