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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 1/31/15 2:07 PM
 */
class MapperType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'mappings',
            'collection',
            [
                'type' => 'tadcka_mapper_mapping',
                'allow_add' => true,
                'allow_delete' => true,
                'label' => false,
                'options' => ['source_metadata' => $options['other_source_metadata']]
            ]
        );

        $builder->add('itemId', 'hidden');

        $builder->add('source', 'hidden');

        $builder->add('otherSource', 'hidden');
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['source_metadata'] = $options['source_metadata'];
        $view->vars['other_source_metadata'] = $options['other_source_metadata'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['source_metadata', 'other_source_metadata']);

        $resolver->setDefaults(
            [
                'label' => false,
            ]
        );
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tadcka_mapper';
    }
}
