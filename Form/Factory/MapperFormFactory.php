<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Form\Factory;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Tadcka\Mapper\Mapping\MappingProviderInterface;
use Tadcka\Mapper\Source\SourceMetadata;
use Tadcka\Mapper\Source\SourceProvider;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/31/15 2:11 PM
 */
class MapperFormFactory
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var MappingProviderInterface
     */
    private $mappingProvider;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SourceProvider
     */
    private $sourceProvider;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param MappingProviderInterface $mappingProvider
     * @param RouterInterface $router
     * @param SourceProvider $sourceProvider
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        MappingProviderInterface $mappingProvider,
        RouterInterface $router,
        SourceProvider $sourceProvider,
        TranslatorInterface $translator
    ) {
        $this->formFactory = $formFactory;
        $this->mappingProvider = $mappingProvider;
        $this->router = $router;
        $this->sourceProvider = $sourceProvider;
        $this->translator = $translator;
    }

    /**
     * Create mapper form.
     *
     * @param string $itemId
     * @param SourceMetadata $sourceMetadata
     * @param SourceMetadata $otherSourceMetadata
     *
     * @return FormInterface
     */
    public function create($itemId, SourceMetadata $sourceMetadata, SourceMetadata $otherSourceMetadata)
    {
        return $this->formFactory->create(
            'tadcka_mapper',
            $this->getData($itemId, $sourceMetadata, $otherSourceMetadata),
            [
                'source' => $sourceMetadata->getName(),
                'other_source' => $otherSourceMetadata->getName(),
                'action' => $this->router->getContext()->getPathInfo(),
            ]
        );
    }

    /**
     * Get form data.
     *
     * @param string $itemId
     * @param SourceMetadata $sourceMetadata
     * @param SourceMetadata $otherSourceMetadata
     *
     * @return array
     */
    private function getData($itemId, SourceMetadata $sourceMetadata, SourceMetadata $otherSourceMetadata)
    {
        $data = [
            'itemId' => $itemId,
            'source' => $sourceMetadata->getName(),
            'otherSource' => $otherSourceMetadata->getName()
        ];

        $mappings = $this->mappingProvider
            ->getMappings($itemId, $sourceMetadata->getName(), $otherSourceMetadata->getName());

        if (count($mappings)) {
            $sourceData = $this->sourceProvider->getData($otherSourceMetadata);

            foreach ($mappings as $mapping) {
                if ($otherSourceMetadata->getName() === $mapping->getLeftItem()->getSource()->getSlug()) {
                    $item = $mapping->getLeftItem();
                } else {
                    $item = $mapping->getRightItem();
                }

                $sourceItem = $sourceData->getItem($item->getSlug());

                $data['mappings'][$item->getSlug()] = [
                    'item' => $item->getSlug(),
                    'main' => $mapping->isMain(),
                    'title' => null !== $sourceItem
                            ? $sourceItem->getTitle()
                            : $this->translator->trans('title_not_found', [], 'TadckaMapperBundle')
                ];
            }
        }



        return $data;
    }
}
