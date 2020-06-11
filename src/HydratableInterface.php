<?php

/*
 *  @copyright (c) 2019 Mendel <mendel@zzzlab.com>
 *  @license see license.txt
 */

namespace drycart\data;

/**
 *
 * @author mendel
 */
interface HydratableInterface
{
    public function hydrate(array $data) : void;
    public function dehydrate(): array;
}
