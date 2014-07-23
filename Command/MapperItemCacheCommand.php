<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadcka <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\MapperBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tadcka\Component\Mapper\Provider\MapperProviderInterface;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since 7/15/14 8:29 PM
 */
class MapperItemCacheCommand extends ContainerAwareCommand
{
    /**
     * @return MapperProviderInterface
     */
    private function getProvider()
    {
        return $this->getContainer()->get('tadcka_mapper.provider');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('mapper:item:generate_cache')
            ->setDescription('Mapper item cache generator')
            ->addArgument('name', InputArgument::OPTIONAL, 'Please indicate the name.')
            ->addArgument('locale', InputArgument::OPTIONAL, 'Please select the locale.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $this->getProvider()->getSource($input->getArgument('name'));
        if (null !== $source) {
            $this->getProvider()->getMapper($source, $input->getArgument('locale'), true);

            $output->writeln('Saves mapper item in the cache.');
        } else {
            $output->writeln('Mapper source ' . $input->getArgument('name') . ' not found!');
        }
    }
}
