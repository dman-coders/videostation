<?php

namespace VideoStation;


use VideoStation\Service\Config;
use Symfony\Component\Console\Application as ParentApplication;
use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

/**
 *
 */
class Application extends ParentApplication {

  /** @var Config */
  protected $cliConfig;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->cliConfig = new Config();
    $this->envPrefix = $this->cliConfig->get('application.env_prefix');
    parent::__construct($this->cliConfig->get('application.name'), $this->cliConfig->getVersion());
    $this->addCommands($this->getCommands());
  }


  /**
   * @return \Symfony\Component\Console\Command\Command[]
   */
  protected function getCommands() {
    static $commands = [];
    if (count($commands)) {
      return $commands;
    }
    $commands = [
      new Command\StatusCommand(),
      ];
    return $commands;
  }
}
