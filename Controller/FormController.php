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
use Tadcka\Bundle\MapperBundle\Source\SourceHelper;
use Tadcka\Bundle\MapperBundle\Source\SourceProvider;
use Tadcka\Mapper\Extension\Source\Tree\MapperTreeHelper;

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
     * @var MapperTreeHelper
     */
    private $treeHelper;

    /**
     * Constructor.
     *
     * @param MapperFormFactory $factory
     * @param MapperFormHandler $handler
     * @param SourceHelper $sourceHelper
     * @param SourceProvider $sourceProvider
     * @param EngineInterface $templating
     * @param MapperTreeHelper $treeHelper
     */
    public function __construct(
        MapperFormFactory $factory,
        MapperFormHandler $handler,
        SourceHelper $sourceHelper,
        SourceProvider $sourceProvider,
        EngineInterface $templating,
        MapperTreeHelper $treeHelper
    ) {
        $this->factory = $factory;
        $this->handler = $handler;
        $this->sourceHelper = $sourceHelper;
        $this->sourceProvider = $sourceProvider;
        $this->templating = $templating;
        $this->treeHelper = $treeHelper;
    }


    public function indexAction(Request $request, $itemId, $metadata, $otherMetadata)
    {
        $sourceMetadata = $this->sourceHelper->getMetadata($metadata);
        $sourceData = $this->sourceProvider->getData($sourceMetadata);

        if (false === $sourceData->canMapping($itemId)) {
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
        if (false === $sourceData->canMapping($itemId)) {
            $data['error'] = $this->templating->render(
                'TadckaMapperBundle:Mapper:mapping-error.html.twig',
                ['item_id' => $itemId]
            );
        }

        $item = $sourceData->getItem($itemId);

        $data['item_id'] = $item->getId();
        $data['item_title'] = $item->getTitle();
        $data['source'] = $sourceMetadata->getName();

        if ('mapper_tree' === $sourceMetadata->getType()) {
            $data['item_full_path'] = implode(' / ', $this->treeHelper->getTreeItemFullPath($itemId, $sourceData->getTree()));
        }

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
