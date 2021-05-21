<?php

namespace App;

use Psr\Log\LoggerInterface;

class Main
{
    public static $DATA_DIR = "data";
    public static $FILESIZES = "filesizes_list.txt";
    public static $NUM_OF_RANDOMIZED_FILESIZES = 100;
    public static $RESULTFILE = "data.txt";

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function run()
    {
        $this->getFileSizesInYourHardDisk();
        $arrOfBytes = $this->getArrayOfFileSizesInBytes();
        $arrOfBytes =$this->randomizeFileSizesInYourHardDisk($arrOfBytes);
        $this->saveFile($arrOfBytes);
    }

    public function getFileSizesInYourHardDisk()
    {
        if(!is_dir("./".self::$DATA_DIR)){
            $this->logger->info("Create data directory");
            mkdir(__DIR__."/../data");
        }

        if(!file_exists($this->getPathToFile())){
            $this->logger->info("Creating bytes of data in hard disk list");
            $cmd = "sudo find / -exec stat -c %s {} \; > ".$this->getPathToFile()." 2> /dev/null";
            system($cmd);
        }else{
            $this->logger->alert("File already has exists");
        }
    }

    private function getPathToFile(): string{
        return "./".self::$DATA_DIR."/".self::$FILESIZES;
    }

    public function getArrayOfFileSizesInBytes():array
    {
        $this->logger->info("Read file and creating array of bytes");

        if (!file_exists($this->getPathToFile())) {
            throw new \Exception("File doesn't exists! Not found file: "
                .self::$FILESIZES." in ".getcwd()."/".self::$DATA_DIR);
        }

        $data = array();

        $file = fopen($this->getPathToFile(),"r");
        while (!feof($file)){
            $line = str_replace("\n","",fgets($file));
            array_push($data, (int)$line);
        }
        fclose($file);

//        var_dump($data);
        return $data;
    }

    public function randomizeFileSizesInYourHardDisk(array $arrOfBytes): array
    {
        $this->logger->info("Random filesizes");
        if(count($arrOfBytes)<self::$NUM_OF_RANDOMIZED_FILESIZES){
            throw new \Exception("Array of file sizes is too short!");
        }

        $indexes = array_rand($arrOfBytes,self::$NUM_OF_RANDOMIZED_FILESIZES);
//        var_dump($indexes);

        $result = array();
        foreach ($indexes as $index){
            array_push($result, $arrOfBytes[$index]);
        }

        return $result;
    }

    private function convertArrToString(array $arrOfBytes):string
    {
        return implode("\r\n",$arrOfBytes);
    }

    public function saveFile(array $arrOfBytes)
    {
        $this->logger->info("Save bytes to file");
        $txt = $this->convertArrToString($arrOfBytes);
        file_put_contents(__DIR__."/../".self::$DATA_DIR."/".self::$RESULTFILE,$txt);
    }
}