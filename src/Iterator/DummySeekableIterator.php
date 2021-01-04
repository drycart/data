<?php

/*
 *  @copyright (c) 2021 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data\Iterator;

/**
 * DummySeekableIterator - just wrapper for limitIterator
 * for mark what it implement SeekableIterator 
 *
 * @author mendel
 */
class DummySeekableIterator extends \LimitIterator implements \SeekableIterator
{
}
