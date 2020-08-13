<?php

namespace Youwe\FixturesBundle\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RearrangeFixturesCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('fixtures:rearrange')
            ->setDescription('Reorders yml fixtures');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rearranger = new Rearrange();
        $rearranger->rearrangeFixtures();

        $output->writeln('<info>Done. Your fixtures are at: "' . FixtureLoader::FIXTURE_FOLDER . '".</info>');
    }
}
