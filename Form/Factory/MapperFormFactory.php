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
use Tadcka\Mapper\Model\MappingInterface;

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
     * Constructor.
     *
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Create mapper form.
     *
     * @param array|MappingInterface[] $mappings
     * @param string $source
     * @param string $otherSource
     *
     * @return FormInterface
     */
    public function create(array $mappings, $source, $otherSource)
    {
        return $this->formFactory->create(
            'tadcka_mapper',
            $mappings,
            [
                'source' => $source,
                'other_source' => $otherSource
            ]
        );
    }
}
