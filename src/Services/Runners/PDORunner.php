<?php

namespace WorldFactory\QQ\Services\Runners;

use PDO;
use Exception;
use WorldFactory\QQ\Foundations\AbstractRunner;
use WorldFactory\QQ\Misc\RunnerOptionBag;

class PDORunner extends AbstractRunner
{
    const OPTION_DEFINITIONS = [
        'target'     => [
            'type' => 'string',
            'default' => 'default',
            'description' => "The target connexion."
        ],
        'fetch' => [
            'type' => 'string',
            'description' => "Set how to fetch data. Values : val, one, all, col"
        ],
        'dsn' => [
            'type' => 'string',
            'description' => "Set how to fetch data. Values : val, one, all, col"
        ],
        'user' => [
            'type' => 'string',
            'description' => "Set how to fetch data. Values : val, one, all, col"
        ],
        'pass' => [
            'type' => 'string',
            'description' => "Set how to fetch data. Values : val, one, all, col"
        ]
    ];

    protected const SHORT_DESCRIPTION = "Use PDO to send request to target database.";

    protected const LONG_DESCRIPTION = <<<EOT
This Runner is used to send request to target database.
Use 'target' option to set targeted connexion.
EOT;

    /** @var PDO[] */
    private $connections = [];

    /** @var array */
    private $config;

    public function __construct(array $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute(string $script) : void
    {
        $connection = $this->connect();

        /** @var RunnerOptionBag $options */
        $options = $this->getOptions();

        if (isset($options['fetch']) && !empty($options['fetch'])) {
            $result = call_user_func(array($connection, 'query'), $script);
        } else {
            $result = call_user_func(array($connection, 'exec'), $script);

            $this->getOutput()->writeln("$result affected row(s).");
        }

        $this->getBuffer()->setResult($result);
    }

    /**
     * @return PDO
     * @throws Exception
     */
    public function connect() : PDO
    {
        /** @var RunnerOptionBag $options */
        $options = $this->getOptions();

        $target = $options['target'];

        if ($this->hasConnection($target)) {
            return $this->getConnection($target);
        }

        if (isset($options['dsn'])) {
            return $this->buildConnection($target, $options['dsn'], $options['user'], $options['pass']);
        }

        if (isset($this->config['connections'][$target])) {
            $config = $this->config['connections'][$target];

            return $this->buildConnection($target, $config['dsn'], $config['user'], $config['pass']);
        }

        throw new Exception("Unable to retrieve database connection : '$target'.");
    }

    protected function hasConnection(string $target)
    {
        return isset($this->connections[$target]);
    }

    /**
     * @param string $target
     * @return PDO
     * @throws Exception
     */
    protected function getConnection(string $target)
    {
        if (!$this->hasConnection($target)) {
            throw new Exception("Target database connection not found : '$target'.");
        }

        return $this->connections[$target];
    }

    /**
     * @param string $target
     * @param string $dsn
     * @param string|null $user
     * @param string|null $pass
     * @return PDO
     * @throws Exception
     */
    protected function buildConnection(string $target, string $dsn, string $user = null, string $pass = null)
    {
        if ($user === null) {
            throw new Exception("Build database connection require USER parameter.");
        }

        $connection = new PDO($dsn, $user, $pass);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($this->isPersisted($target)) {
            $this->connections[$target] = $connection;
        }

        return $connection;
    }

    protected function isPersisted(string $target)
    {
        return (isset($this->config['connections'][$target]) && $this->config['connections'][$target]['persist']);
    }
}