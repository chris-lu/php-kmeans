<?php
/**
 * This file is part of PHP K-Means
 *
 * Copyright (c) 2014 Benjamin Delespierre
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace KMeans;

use \SplObjectStorage;
use \LogicException;
use \InvalidArgumentException;

class EarthSpace extends Space
{

    protected $scaling = 1;

    public function __construct($scaling = 1)
    {
        parent::__construct(2);
        $this->scaling = $scaling;
    }

    public function getDistance(Point $point1, Point $point2, $precise = true)
    {

        if ($point1->getSpace() !== $this || $point2->getSpace() !== $this) {
            throw new LogicException("can only calculate distances from points in the same space");
        }

        $rad = M_PI / 180;

        $distance = $this->scaling * (acos(min(max(sin($point2[0] * $rad) * sin($point1[0] * $rad) + cos($point2[0] * $rad) * cos($point1[0] * $rad) * cos($point2[1] * $rad - $point1[1] * $rad), -1.0), 1.0)) * 6371); // Kilometers
        return $precise ? $distance : pow($distance, 2);
    }

}