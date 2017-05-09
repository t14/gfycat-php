<?php
namespace bbcworldwide\gfycat\Test;

use bbcworldwide\gfycat\GfyCatAuth;
use bbcworldwide\gfycat\GfyCatAnon;

class GfycatTest extends \PHPUnit_Framework_TestCase {

    public function setup()
    {
        $this->gfyAuth = new GfyCatAuth();
        $this->gfyAnon = new GfyCatAnon();
    }

    public function testPrepFile()
    {
        $file = $this->gfyAuth->newFileName('testDir', 'name_of_gfy');
        $this->assertEquals('testDir/name_of_gfy', $file);

        $file = $this->gfyAnon->newFileName('testDir', 'name_of_gfy');
        $this->assertEquals('testDir/name_of_gfy', $file);
    }
    
    public function testBaseUrl()
    {
        $ReflectObject = new \ReflectionClass('bbcworldwide\gfycat\GfyCatAuth');
        $this->assertEquals('https://api.gfycat.com', $ReflectObject->getConstant('BASE_URL'));

        $ReflectObject = new \ReflectionClass('bbcworldwide\gfycat\GfyCatAnon');
        $this->assertEquals('https://api.gfycat.com', $ReflectObject->getConstant('BASE_URL'));

    }

    public function testTokenUri()
    {
        $ReflectObject = new \ReflectionClass('bbcworldwide\gfycat\GfyCatAuth');
        $this->assertEquals('/v1/oauth/token', $ReflectObject->getConstant('TOKEN_URI'));

    }

    public function testUpdateUri()
    {
        $ReflectObject = new \ReflectionClass('bbcworldwide\gfycat\GfyCatAuth');
        $this->assertEquals('/v1/me/gfycats/', $ReflectObject->getConstant('UPDATE_URI'));
    }

    public function testUri()
    {
        $ReflectObject = new \ReflectionClass('bbcworldwide\gfycat\GfyCatAuth');
        $this->assertEquals('/v1/gfycats/', $ReflectObject->getConstant('URI'));

        $ReflectObject = new \ReflectionClass('bbcworldwide\gfycat\GfyCatAnon');
        $this->assertEquals('/v1/gfycats/', $ReflectObject->getConstant('URI'));
    }

    public function testUpdateUrl()
    {
        $updateUrl = $this->gfyAuth->getUpdateUrl('gfyID', 'title');
        $this->assertEquals('https://api.gfycat.com/v1/me/gfycats/gfyID/title', $updateUrl);
    }

    public function testSetUpdateValues()
    {
        $updatedTitleValue = $this->gfyAuth->setUpdateValues('title', 'title of gfycat');
        $updatedDescValue = $this->gfyAuth->setUpdateValues('description', 'updated description');
        $updatedPubValue = $this->gfyAuth->setUpdateValues('published', '1');
        $updatedTagsValues = $this->gfyAuth->setUpdateValues('tags', ['tag1', 'tag2', 'tag3']);
        
        $this->assertEquals(['value' => 'title of gfycat'], $updatedTitleValue);
        $this->assertEquals(['value' => 'updated description'], $updatedDescValue);
        $this->assertEquals(['value' => '1'], $updatedPubValue );
        $this->assertEquals(['value' => ['tag1', 'tag2', 'tag3']], $updatedTagsValues );
    }

    public function testGettingTheGetUrl()
    {
        $reflectionMethod = new \ReflectionMethod('bbcworldwide\gfycat\GfyCatAuth', 'getUrl');
        $url = $reflectionMethod->invoke(new GfyCatAuth(), 'myGfycat');
        $this->assertEquals('https://api.gfycat.com/v1/gfycats/myGfycat', $url);

        $reflectionMethod = new \ReflectionMethod('bbcworldwide\gfycat\GfyCatAnon', 'getUrl');
        $url = $reflectionMethod->invoke(new GfyCatAnon(), 'myGfycat');
        $this->assertEquals('https://api.gfycat.com/v1/gfycats/myGfycat', $url);
    }

}