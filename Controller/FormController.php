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
use Tadcka\Bundle\MapperBundle\Form\Handler\MapperFormHandler;
use Tadcka\Bundle\MapperBundle\Helper\SourceHelper;
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
     * @var MapperFormHandler
     */
    private $handler;

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
     * @param MapperFormHandler $handler
     * @param SourceHelper $sourceHelper
     * @param SourceProvider $sourceProvider
     * @param EngineInterface $templating
     */
    public function __construct(
        MapperFormFactory $factory,
        MapperFormHandler $handler,
        SourceHelper $sourceHelper,
        SourceProvider $sourceProvider,
        EngineInterface $templating
    ) {
        $this->factory = $factory;
        $this->handler = $handler;
        $this->sourceHelper = $sourceHelper;
        $this->sourceProvider = $sourceProvider;
        $this->templating = $templating;
    }


    public function indexAction(Request $request, $itemId, $metadata, $otherMetadata)
    {
        $sourceMetadata = $this->sourceHelper->getMetadata($metadata);
        $sourceData = $this->sourceProvider->getData($sourceMetadata);

        if (false === $sourceData->catMapping($itemId)) {
            return $this->renderResponse('TadckaMapperBundle:Mapper:mapping-error.html.twig', ['item_id' => $itemId]);
        }

        $otherSourceMetadata = $this->sourceHelper->getMetadata($otherMetadata);
        $form = $this->factory->create($itemId, $sourceMetadata, $otherSourceMetadata);

        if ($this->handler->process($form, $request)) {
            $this->handler->onSuccess();

            return new Response();
        }

        return $this->renderResponse('TadckaMapperBundle:Mapper:form.html.twig', ['form' => $form->createView()]);
    }

    public function validateItemAction($itemId, $metadata)
    {
        $sourceMetadata = $this->sourceHelper->getMetadata($metadata);
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
