<?php

namespace WorldFactory\QQ\Components;

use LogicException;

class Hosts
{
    const SEPARATOR = '### QQ managed hosts ###';

    private $prefix = '';
    private $suffix = '';

    private $mapping = [];

    private $target;

    private $cachedContent;

    public function __construct(string $target)
    {
        $this->target = $target;

        $this->loadTarget();
    }

    /**
     * @return mixed
     */
    public function getCachedContent()
    {
        return $this->cachedContent;
    }

    private function loadTarget()
    {
        $this->cachedContent = file_get_contents($this->target);

        $tokens = explode(self::SEPARATOR, $this->cachedContent);
        $count = count($tokens);

        if ($count > 3) {
            $nb = $count - 3;
            throw new LogicException("Inconsistent hosts file. $nb separators found in overtime.");
        } elseif ($count === 2) {
            throw new LogicException("Inconsistent hosts file. Only one separator found.");
        }

        $this->prefix = rtrim($tokens[0], PHP_EOL);

        if ($count === 3) {
            $this->suffix = ltrim($tokens[2], PHP_EOL);
            $this->parseContent($tokens[1]);
        }
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    private function parseContent(string $content)
    {
        $lines = explode(PHP_EOL, $content);

        foreach($lines as $line) {
            $tokens = preg_split('/\s+/', $line);
            $ip = array_shift($tokens);

            if (!empty($ip)) {
                foreach($tokens as $token) {
                    $this->addHost($ip, $token);
                }
            }
        }
    }

    public function addHost(string $ip, string $host)
    {
        if(!array_key_exists($ip, $this->mapping)) {
            $this->mapping[$ip] = [];
        }

        $this->removeHost($host);

        $this->mapping[$ip][] = $host;

        asort($this->mapping[$ip]);
        asort($this->mapping);
    }

    public function removeHost(string $host)
    {
        foreach($this->mapping as $ip => $hosts) {
            if (in_array($host, $hosts)) {
                $id = array_search($host, $hosts);
                unset($hosts[$id]);

                if (empty($hosts)) {
                    unset($this->mapping[$ip]);
                } else {
                    $this->mapping[$ip] = $hosts;
                }
            }
        }
    }

    public function hasHost(string $host)
    {
        foreach($this->mapping as $ip => $hosts) {
            if (in_array($host, $hosts)) {
                return true;
            }
        }

        return false;
    }

    public function buildContent()
    {
        $content = $this->prefix;

        if (substr($content, -1, 1) !== PHP_EOL) {
            $content .= PHP_EOL;
        }

        if (substr($content, -1, 2) !== str_repeat(PHP_EOL, 2)) {
            $content .= PHP_EOL;
        }

        if (!empty($this->mapping)) {
            $content .= self::SEPARATOR . PHP_EOL . PHP_EOL;

            foreach ($this->mapping as $ip => $hosts) {
                $content .= "$ip\t" . implode(' ', $hosts) . PHP_EOL;
            }

            $content .= PHP_EOL . self::SEPARATOR . PHP_EOL;

            if (substr($this->suffix, 0, 1) !== PHP_EOL) {
                $content .= PHP_EOL;
            }
        }

        $content .= $this->suffix;

        return $content;
    }
}