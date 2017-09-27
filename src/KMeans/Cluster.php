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

use \IteratorAggregate;
use \Countable;
use \SplObjectStorage;
use \LogicException;

class Cluster extends Point implements IteratorAggregate, Countable
{
    protected $space;
    protected $points;

    public function __construct(Space $space, array $coordinates)
    {
        parent::__construct($space, $coordinates);
        $this->points = new SplObjectStorage;
    }

    public function toArray()
    {
        $points = array();
        foreach ($this->points as $point) {
            $points[] = $point->toArray();
        }

        return array(
            'centroid' => parent::toArray(),
            'points' => $points,
        );
    }

    public function attach(Point $point)
    {
        if ($point instanceof self) {
            throw new LogicException("cannot attach a cluster to another");
        }

        $this->points->attach($point);
        return $point;
    }

    public function detach(Point $point)
    {
        $this->points->detach($point);
        return $point;
    }

    public function attachAll(SplObjectStorage $points)
    {
        $this->points->addAll($points);
    }

    public function detachAll(SplObjectStorage $points)
    {
        $this->points->removeAll($points);
    }

    public function updateCentroid()
    {
        if (!$count = count($this->points)) {
            return;
        }

        $centroid = $this->space->newPoint(array_fill(0, $this->dimension, 0));

        foreach ($this->points as $point) {
            for ($n = 0; $n < $this->dimension; $n++) {
                $centroid->coordinates[$n] += $point->coordinates[$n];
            }
        }

        for ($n = 0; $n < $this->dimension; $n++) {
            $this->coordinates[$n] = $centroid->coordinates[$n] / $count;
        }
    }

    public function getSSE(){
        $sse = 0;

        foreach ($this->points as $point) {
            $sse += $this->getDistanceWith($point, false);
        }

        return $sse;
    }

    public function getIterator()
    {
        return $this->points;
    }

    public function count()
    {
        return count($this->points);
    }
}