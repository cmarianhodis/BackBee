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

namespace BackBuilder\Test\Mock;

use BackBuilder\BBApplication,
    BackBuilder\Site\Site;
use org\bovigo\vfs\vfsStream;

/**
 * @category    BackBuilder
 * @package     BackBuilder\TestUnit\Mock
 * @copyright   Lp digital system
 * @author      c.rouillon <rouillon.charles@gmail.com>
 */
class MockBBApplication extends BBApplication
{

    private $_container;
    private $_context;
    private $_debug;
    private $_isinitialized;
    private $_isstarted;
    private $_autoloader;
    private $_bbdir;
    private $_cachedir;
    private $_mediadir;
    private $_repository;
    private $_base_repository;
    private $_resourcedir;
    private $_starttime;
    private $_storagedir;
    private $_tmpdir;
    private $_bundles;
    private $_classcontentdir;
    private $_theme;
    private $_overwrite_config;

    /**
     * The mock base directory
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $_mock_basedir;

    /**
     * Mock the BBApplication class constructor
     * 
     * @param string $context
     * @param boolean $debug
     * @param boolean $overwrite_config
     */
    public function __construct($context = null, $debug = false, $overwrite_config = false, $mockConfig = null)
    {
        $this->_mockInitStructure($mockConfig);
        parent::__construct($context, $debug, $overwrite_config);
    }

    /**
     * Mock the method returning the BackBuilder directory
     * @return string
     */
    public function getBBDir()
    {
        if (null === $this->_bbdir) {
            $r = new \ReflectionClass('\BackBuilder\BBApplication');
            $this->_bbdir = dirname($r->getFileName());
        }

        return $this->_bbdir;
    }

    /**
     * Mock the merhod returning the installation directory
     * @return string
     */
    public function getBaseDir()
    {
        return vfsStream::url('basedir');
    }

    /**
     * Initilizes the mock structure
     * @return \BackBuilder\TestUnit\Mock\MockBBApplication
     */
    protected function _mockInitStructure($mockConfig = null)
    {
        if(null === $mockConfig) {
            $mockConfig = array(
                'cache' => array(
                    'default' => array()
                ),
                'log' => array(),
                'repository' => array(
                    'ClassContent' => array(),
                    'Config' => array(
                        'config.yml' => file_get_contents(__DIR__ . '/' . '..' . '/' . 'config.yml'),
                        'services.xml' => file_get_contents(__DIR__ . '/' . '..' . '/' . 'services.xml'),
                    ),
                    'Data' => array(
                        'Media' => array(),
                        'Storage' => array(),
                        'Tmp' => array()
                    ),
                    'Ressources' => array()
                )
            );
        }
        
        $this->_mock_basedir = vfsStream::setup('basedir', 0777, $mockConfig);

        return $this;
    }
    
    
    /**
     */
    public function setIsStarted($isStarted)
    {
        $this->_isstarted = $isStarted;
    }
    
    
    /**
     * @return boolean
     */
    public function isStarted()
    {
        return (true === $this->_isstarted);
    }

}