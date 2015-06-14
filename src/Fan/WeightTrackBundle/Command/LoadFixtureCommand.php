<?php

namespace Fan\WeightTrackBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\ReferenceRepository;

class LoadFixtureCommand extends ContainerAwareCommand
{

  protected function configure() {
    $this->setName('fan:load-fixtures')
      ->setDescription('load fixtures')
      ->addArgument('path', InputArgument::OPTIONAL, 'Fixtures directory')
      ->addOption('append', '-a', InputOption::VALUE_OPTIONAL, 'append to existing data');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $path = $input->getArgument('path');
    if (! $path) {
      $path = realpath(__DIR__ . '/../Tests/Fixtures/');
    }

    if ($input->getOption('append')) {
      // TBD
    }

    $loader = new Loader();
    $loader->loadFromDirectory($path);

    $em = $this->getContainer()->get('doctrine.orm.entity_manager');
    $referenceRepo = new ReferenceRepository($em);

    foreach ( $loader->getFixtures() as $fixture ) {
      $fixture->setReferenceRepository($referenceRepo);
      $fixture->load($em);
    }

    $em->clear();
  }
}