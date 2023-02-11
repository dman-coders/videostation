<?php

namespace VideoStation\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoStation\Service\Config;

#[AsCommand(
  name: 'status',
)]
class StatusCommand extends Command {

  protected static $defaultName = 'status';
  protected static $defaultDescription = 'Shows current configuration and player status.';

  protected function configure(): void {
    $this
      ->setHelp('Shows current configuration from the yaml files, and player status from the connected player.')
    ;
  }


  /**
   */
  public function __construct() {
    parent::__construct();
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->config = $this->getApplication()->getConfig();
    $output->writeln('<info>' . $this->config->get('application.name') . ':' . $this->config->get('application.version') . '</info>');

    $output->writeln('Application');
    $application = $output->section();
    foreach ($this->config->get('application') as $key => $value) {
      // Use var_export in case some values are structs;
      $application->writeln('  ' . $key . ': ' . var_export($value, 1));
    }

    $output->writeln('Local');
    $local = $output->section();
    foreach ($this->config->get('local') as $key => $value) {
      $local->writeln('  ' . $key . ': ' . var_export($value, 1));
    }

    return Command::SUCCESS;
  }
}