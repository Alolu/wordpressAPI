<?php

/*
 * This file is part of the Base62 package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/base62
 *
 */

namespace Tuupola;

class Base62
{
    const GMP = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    const INVERTED = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

    private $encoder;
    private $options = [];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, (array) $options);
        if (function_exists("gmp_init")) {
            $this->encoder = new Base62\GmpEncoder($this->options);
        }
        $this->encoder = new Base62\PhpEncoder($this->options);
    }

    public function encode($data)
    {
        return $this->encoder->encode($data);
    }

    public function decode($data, $integer = false)
    {
        return $this->encoder->decode($data, $integer);
    }
}
