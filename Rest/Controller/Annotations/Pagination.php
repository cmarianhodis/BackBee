<?php

/*
 * Copyright (c) 2011-2013 Lp digital system
 * 
 * This file is part of BackBuilder5.
 *
 * BackBuilder5 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * BackBuilder5 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with BackBuilder5. If not, see <http://www.gnu.org/licenses/>.
 */

namespace BackBuilder\Rest\Controller\Annotations;


/**
 * Pagination properties annotation
 *
 * @Annotation
 * 
 * @category    BackBuilder
 * @package     BackBuilder\Rest
 * @copyright   Lp digital system
 * @author      k.golovin
 */
class Pagination
{
    /**
     *
     * @var string
     */
    public $startName = 'start';
    
    /**
     *
     * @var string
     */
    public $limitName = 'limit';
    
    /** 
     * @var int
     */
    public $limitDefault = 100;
    
    /** 
     * @var int
     */
    public $limitMax = 1000;
    
    /** 
     * @var int
     */
    public $limitMin = 1;
    
}