<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Tadcka\Bundle\MapperBundle\Form\Factory\MapperFormFactory;
use Tadcka\Bundle\MapperBundle\Helper\SourceHelper;
use Tadcka\Mapper\Mapping\MappingProviderInterface;
use Tadcka\Mapper\Source\SourceProvider;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/29/15 9:27 PM
 */
class FormController
{
    /**
     * @var MapperFormFactory
     */
    private $factory;

    /**
     * @var MappingProviderInterface
     */
    private $mappingProvider;

    /**
     * @var SourceHelper
     */
    private $sourceHelper;

    /**
     * @var SourceProvider
     */
    private $sourceProvider;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * Constructor.
     *
     * @param MapperFormFactory $factory
     * @param MappingProviderInterface $mappingProvider
     * @param SourceHelper $sourceHelper
     * @param SourceProvider $sourceProvider
     * @param EngineInterface $templating
     */
    public function __construct(
        MapperFormFactory $factory,
        MappingProviderInterface $mappingProvider,
        SourceHelper $sourceHelper,
        SourceProvider $sourceProvider,
        EngineInterface $templating
    ) {
        $this->factory = $factory;
        $this->mappingProvider = $mappingProvider;
        $this->sourceHelper = $sourceHelper;
        $this->sourceProvider = $sourceProvider;
        $this->templating = $templating;
    }


    public function getAction(Request $request, $itemId, $sourceMetadata, $otherSourceMetadata)
    {
        $sourceMetadata = $this->sourceHelper->getMetadata($sourceMetadata);
        $sourceData = $this->sourceProvider->getData($sourceMetadata);

        if (false === $sourceData->catMapping($itemId)) {
            return $this->renderResponse('TadckaMapperBundle:Mapper:mapping-error.html.twig', ['item_id' => $itemId]);
        }

        $otherSourceMetadata = $this->sourceHelper->getMetadata($otherSourceMetadata);

        $mappings = $this->mappingProvider->getItems(
            $itemId,
            $sourceMetadata->getName(),
            $otherSourceMetadata->getName()
        );

        $form = $this->factory->create($mappings, $sourceMetadata->getName(), $otherSourceMetadata->getName());

        return $this->renderResponse('TadckaMapperBundle:Mapper:form.html.twig', ['form' => $form->createView()]);
    }

    public function postAction(Request $request, $itemId, $sourceMetadata, $otherSourceMetadata)
    {
        return new Response();
    }

    public function validateItemAction($itemId, $sourceMetadata)
    {
        $sourceMetadata = $this->sourceHelper->getMetadata($sourceMetadata);
        $sourceData = $this->sourceProvider->getData($sourceMetadata);

        $data = [];
        if (false === $sourceData->catMapping($itemId)) {
            $data['error'] = $this->templating->render(
                'TadckaMapperBundle:Mapper:mapping-error.html.twig',
                ['item_id' => $itemId]
            );
        }

        $item = $sourceData->getItem($itemId);

        $data['item_id'] = $item->getId();
        $data['item_title'] = $item->getTitle();
        $data['source'] = $sourceMetadata->getName();

        return new JsonResponse($data);
    }


    /**
     * Render response.
     *
     * @param string $name
     * @param array $parameters
     *
     * @return Response
     */
    private function renderResponse($name, array $parameters = [])
    {
        return new Response($this->templating->render($name, $parameters));
    }
}
