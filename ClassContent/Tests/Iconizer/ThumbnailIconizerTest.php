<?php

/*
 * Copyright (c) 2011-2015 Lp digital system
 *
 * This file is part of BackBee.
 *
 * BackBee is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * BackBee is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with BackBee. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Charles Rouillon <charles.rouillon@lp-digital.fr>
 */

namespace BackBee\ClassContent\Tests\Iconizer;

use BackBee\ClassContent\AbstractClassContent;
use BackBee\ClassContent\Element\Image;
use BackBee\ClassContent\Tests\Mock\MockContent;
use BackBee\Tests\BackBeeTestCase;
use BackBee\ClassContent\Iconizer\ThumbnailIconizer;
use BackBee\ApplicationInterface;
use Faker\Provider\Image as ImgFake;
use BackBee\DependencyInjection\Container;
use BackBee\Tests\Mock\ManualBBApplication;
use BackBee\DependencyInjection\ContainerBuilder;

/**
 * @category    BackBee
 *
 * @copyright   Lp digital system
 */
class ThumbnailIconizerTest extends BackBeeTestCase
{

    private $content;
    private $thumbhnail;
    private $mockApp;
    private $thumbnail2;

    public function setUp()
    {
        $this->content = new MockContent();
        $this->content->load();
	$this->thumbhnail = new ThumbnailIconizer(self::$app);
	$this->mockApp = new ManualBBApplication();
	$this->thumbnail2 = new ThumbnailIconizer($this->mockApp);
	
    }
    
    
    public function testGetIcon()
    {
	$icon1 = $this->thumbhnail->getIcon($this->content);
	$this->assertTrue('/resources/img/contents/default_thumbnail.png' === $icon1);
	$this->content
	     ->mockedDefineData(
		'image',
		'BackBee\ClassContent\Element\Image',
		array('')
	    );
	 $this->content
	     ->mockedDefineProperty('iconized-by', 'image');
	
	$icon2 = $this->thumbhnail->getIcon($this->content);
	$this->assertTrue('/resources/img/contents/default_thumbnail.png' == $icon2);

    }
    
    
    public function testGetBaseFolder(){
	 $baseFolder = $this->invokeMethod($this->thumbhnail, 'getBaseFolder');
	 $this->assertNotNull($baseFolder);
	 $this->assertTrue($baseFolder == self::$app->getContainer()->getParameter('classcontent_thumbnail.base_folder'));
	 
//	 $container_builder = new ContainerBuilder(self::$app);
//	 $container = $container_builder->getContainer();
//	 $container->setParameter('classcontent_thumbnail.base_folder', 'img/extnd-x');
//	 var_dump('begin');
//	 var_dump($container->getParameter('classcontent_thumbnail.base_folder'));
//	 var_dump('fin');
//	 $baseFolder2 = $this->invokeMethod($this->thumbnail, 'getBaseFolder');

    }
    
    public function testResolveResourceThumbnail(){
	$resource1 = $this->invokeMethod($this->thumbhnail,'resolveResourceThumbnail', array('imagePath'));
	$this->assertNotNull($resource1);
	$this->assertTrue($resource1 == 'img/contents/default_thumbnail.png');
	
	$resource2 = $this->invokeMethod($this->thumbhnail,'resolveResourceThumbnail', array('picto-extnd.png'));
	$this->assertNotNull($resource2);
	$this->assertTrue($resource2 == 'img/contents/picto-extnd.png');
    }
    
    public function testGetThumbnailBaseFolderPaths(){
	$base1 = $this->invokeMethod($this->thumbhnail,'getThumbnailBaseFolderPaths');
	$this->assertTrue(in_array(self::$app->getContainer()->getParameter('classcontent_thumbnail.base_folder'), $base1));

    }
    

}