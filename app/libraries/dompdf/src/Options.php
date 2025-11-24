<?php

namespace Dompdf;

class Options
{
    /** @var array */
    private $settings = [];

    public function set($name, $value)
    {
        $this->settings[$name] = $value;
    }

    public function get($name, $default = null)
    {
        return $this->settings[$name] ?? $default;
    }
}
