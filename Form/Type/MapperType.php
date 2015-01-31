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
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['source'] = $options['source'];
        $view->vars['other_source'] = $options['other_source'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['source', 'other_source']);

        $resolver->setDefaults(
            [
                'type' => 'tadcka_mapper_mapping',
                'allow_add' => true,
                'allow_delete' => true,
                'options' => [
                    'source' => function (Options $options) {
                        return $options['source'];
                    },
                    'other_source' => function (Options $options) {
                        return $options['other_source'];
                    },
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'collection';
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tadcka_mapper';
    }
}
