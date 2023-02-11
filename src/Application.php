<?php

namespace VideoStation;


use VideoStation\Service\Config;
use Symfony\Component\Console\Application as ParentApplication;
#use Symfony\Bundle\FrameworkBundle\Console\Application as ParentApplication;
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
  protected $config;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->config = new Config();
    $this->envPrefix = $this->config->get('application.env_prefix');
    parent::__construct($this->config->get('application.name'), $this->config->getVersion());
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

  /**
   * Return either the applications config object or a requested key value.
   *
   * @param null $key
   *
   * @return array|bool|string|\VideoStation\Service\Config|null
   */
  public function getConfig($key = NULL) {
    if (empty($key)) {
      return $this->config;
    }
    return $this->config->get($key);
  }
}
