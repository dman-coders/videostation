<?php

namespace VideoStation\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends Command {

  // The name of the command
  protected static $defaultName = 'status';

  protected function configure(): void {
    $this
      ->setDescription('Shows current configuration and player status.')
      ->setHelp('Shows current configuration from the yaml files, and player status from the connected player.')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    // ... put here the code to display status
    $section1 = $output->section();
    $section2 = $output->section();

    $section1->writeln('Hello');
    $section2->writeln('World!');

    return Command::SUCCESS;
    // return Command::FAILURE;
  }
}