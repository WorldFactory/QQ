<?php

namespace WorldFactory\QQ\Misc;

use Symfony\Component\Yaml\Yaml;

class ConfigLoader
{
    private $commands = [];

    private $parameters = [];

    public function loadConfigFile($src)
    {
        $config = Yaml::parse(file_get_contents($src));

        if (array_key_exists('imports', $config)) {
            foreach ($config['imports'] as $import) {
                $this->loadConfigFile($import['resource']);
            }
        }

        if (array_key_exists('parameters', $config)) {
            foreach ($config['parameters'] as $parameterName => $parameterValue) {
                $this->parameters[$parameterName] = $parameterValue;
            }
        }

        if (array_key_exists('commands', $config)) {
            foreach ($config['commands'] as $taskName => $taskConfig) {
                $this->addCommand($taskName, $taskConfig);
            }
        }
    }

    /**
     * @param string $taskName
     * @param array|string $taskConfig
     */
    private function addCommand(string $taskName, $taskConfig)
    {
        if (!is_array($taskConfig)) {
            $taskConfig = ['run' => $taskConfig];
        }

        if (array_key_exists($taskName, $this->commands)) {
            $this->commands[$taskName] = array_merge($this->commands[$taskName], $taskConfig);
        } else {
            $this->commands[$taskName] = array_merge($taskConfig, ['name' => $taskName]);
        }
    }

    /**
     * @param string $name
     * @return array
     */
    public function getCommand(string $name)
    {
        return $this->commands[$name];
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function dump()
    {
        var_dump($this->commands);
    }
}