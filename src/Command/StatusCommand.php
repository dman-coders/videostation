<?php

namespace VideoStation\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoStation\Service\Config;

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

    $this->config = new Config();

    $section1 = $output->section();

    $section1->writeln($this->config->get('application.name') . ':' . $this->config->get('application.version'));

    $application = $output->section();
    foreach ($this->config->get('application') as $key => $value) {
      // Use var_export in case some values are structs;
      $application->writeln('  ' . $key . ': ' . var_export($value, 1));
    }

    $local = $output->section();
    foreach ($this->config->get('local') as $key => $value) {
      $local->writeln('  ' . $key . ': ' . var_export($value, 1));
    }

    return Command::SUCCESS;
  }
}