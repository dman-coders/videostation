<?php

namespace VideoStation\Service;

use VideoStation\Component\Utility\NestedArray;
use Symfony\Component\Yaml\Yaml;

/**
 * Configuration used throughout the CLI.
 */
class Config {

  private $config;

  private $defaultsFile;

  private $env;

  private $version;

  private $homeDir;

  /**
   * @param array|null $env
   * @param string|null $defaultsFile
   */
  public function __construct(array $env = NULL, $defaultsFile = NULL) {
    $this->env = $env !== NULL ? $env : $this->getDefaultEnv();

    $this->defaultsFile = $defaultsFile ?: CLI_ROOT . '/config/config.yaml';
    $this->config = $this->loadConfigFromFile($this->defaultsFile);

    #$this->applyUserConfigOverrides();
    #$this->applyEnvironmentOverrides();
  }

  /**
   * Find all current environment variables.
   *
   * @return array
   */
  private function getDefaultEnv() {
    return PHP_VERSION_ID >= 70100 ? getenv() : $_ENV;
  }

  /**
   * Check if a configuration value is defined.
   *
   * @param string $name The configuration name (e.g. 'application.name').
   * @param bool $notNull Set false to treat null configuration values as
   *                        defined.
   *
   * @return bool
   */
  public function has($name, $notNull = TRUE) {
    $value = NestedArray::getValue($this->config, explode('.', $name), $exists);
    return $exists && (!$notNull || $value !== NULL);
  }

  /**
   * Get a configuration value.
   *
   * @param string $name The configuration name (e.g. 'application.name').
   *
   * @return null|string|bool|array
   * @throws \RuntimeException if the configuration is not defined.
   *
   */
  public function get($key) {
    $parts = explode('.', $key);
    if (count($parts) == 1) {
      return isset($this->config[$key]) ? $this->config[$key] : NULL;
    }
    else {
      $value = NestedArray::getValue($this->config, $parts, $key_exists);
      if (!$key_exists) {
        throw new \RuntimeException('Configuration not defined: ' . $key);
      }
      return $key_exists ? $value : NULL;
    }
  }

  /**
   * Get a configuration value, specifying a default if it does not exist.
   *
   * @param string $name
   * @param mixed $default
   *
   * @return mixed
   */
  public function getWithDefault($key, $default) {
    $parts = explode('.', $key);
    $value = NestedArray::getValue($this->config, $parts, $key_exists);
    if (!$key_exists) {
      return $default;
    }
    return $value;
  }

  /**
   * Returns the user's home directory.
   *
   * @param bool $reset Reset the static cache.
   *
   * @return string The absolute path to the user's home directory
   */
  public function getHomeDirectory($reset = FALSE) {
    if (!$reset && isset($this->homeDir)) {
      return $this->homeDir;
    }
    $prefix = isset($this->config['application']['env_prefix']) ? $this->config['application']['env_prefix'] : '';
    $envVars = [$prefix . 'HOME', 'HOME', 'USERPROFILE'];
    foreach ($envVars as $envVar) {
      $value = getenv($envVar);
      if (array_key_exists($envVar, $this->env)) {
        $value = $this->env[$envVar];
      }
      if (is_string($value) && $value !== '') {
        if (!is_dir($value)) {
          throw new \RuntimeException(
            sprintf('Invalid environment variable %s: %s (not a directory)', $envVar, $value)
          );
        }
        $this->homeDir = realpath($value) ?: $value;
        return $this->homeDir;
      }
    }

    throw new \RuntimeException(sprintf('Could not determine home directory. Set the %s environment variable.', $prefix . 'HOME'));
  }

  /**
   * Get the directory where the CLI is normally installed and configured.
   *
   * @param bool $absolute Whether to return an absolute path. If false,
   *                       the path will be relative to the home directory.
   *
   * @return string
   */
  public function getUserConfigDir($absolute = TRUE) {
    $path = $this->get('application.user_config_dir');
    return $absolute ? $this->getHomeDirectory() . DIRECTORY_SEPARATOR . $path : $path;
  }

  /**
   * @param string $filename
   *
   * @return array
   */
  private function loadConfigFromFile($filename) {
    $contents = file_get_contents($filename);
    if ($contents === FALSE) {
      throw new \RuntimeException('Failed to read config file: ' . $filename);
    }
    return (array) Yaml::parse($contents);
  }


  /**
   * Get an environment variable
   *
   * @param string $name
   *   The variable name. The configured prefix will be prepended.
   *
   * @return mixed|false
   *   The value of the environment variable, or false if it is not set.
   */
  private function getEnv($name) {
    $prefix = isset($this->config['application']['env_prefix']) ? $this->config['application']['env_prefix'] : '';
    if (array_key_exists($prefix . $name, $this->env)) {
      return $this->env[$prefix . $name];
    }

    return getenv($prefix . $name);
  }

  /**
   * @return array
   */
  private function getUserConfig() {
    $userConfigFile = $this->getUserConfigDir() . '/config.yaml';
    if (file_exists($userConfigFile)) {
      return $this->loadConfigFromFile($userConfigFile);
    }

    return [];
  }


  /**
   * Returns this application version.
   *
   * @return string
   */
  public function getVersion() {
    if (isset($this->version)) {
      return $this->version;
    }
    $version = $this->get('application.version');
    if (substr($version, 0, 1) === '@' && substr($version, -1) === '@') {
      // Silently try getting the version from Git.
      $tag = (new Shell())->execute(['git', 'describe', '--tags'], CLI_ROOT);
      if ($tag !== FALSE && substr($tag, 0, 1) === 'v') {
        $version = trim($tag);
      }
    }
    $this->version = $version;

    return $version;
  }

}
