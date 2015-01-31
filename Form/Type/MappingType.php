<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/31/15 1:57 PM
 */
class MappingType extends AbstractType
{
    /**
     * @var string
     */
    private $mappingClass;

    /**
     * Constructor.
     *
     * @param string $mappingClass
     */
    public function __construct($mappingClass)
    {
        $this->mappingClass = $mappingClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('item', 'hidden', ['mapped' => false]);

        $builder->add('main', 'checkbox', ['label' => false, 'required' => false]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['source', 'other_source']);

        $resolver->setDefaults(['data_class' => $this->mappingClass]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tadcka_mapper_mapping';
    }
}
