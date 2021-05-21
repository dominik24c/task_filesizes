<?php

namespace Tests;

use App\Main;
use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
    private Main $main;
    private static int $NUM_OF_RANDOMIZED_FILESIZES=10;

    protected function setUp():void
    {
        parent::setUp();
        $fakeLogger = new FakeLogger();
        $this->main = new Main($fakeLogger);
    }

    private function getFakeFileSizes():array{
        $arr = [];
        for($i=0;$i<self::$NUM_OF_RANDOMIZED_FILESIZES;$i++){
            $arr[]=rand(1,10000);
        }
        return $arr;
    }

    public function testGetPathToFile():void
    {
        $reflector = new \ReflectionObject($this->main);
        $method = $reflector->getMethod('getPathToFile');
        $method->setAccessible(true);
        $this->assertEquals("./".Main::$DATA_DIR."/".Main::$FILESIZES,$method->invoke($this->main));
    }

    public function testRandomizeFileSizesInYourHardDisk():void
    {
        Main::$NUM_OF_RANDOMIZED_FILESIZES = 10;
        $fakeData= $this->getFakeFileSizes();

        $resultArr = $this->main->randomizeFileSizesInYourHardDisk($fakeData);

        $this->assertCount(self::$NUM_OF_RANDOMIZED_FILESIZES, $resultArr);
    }
}