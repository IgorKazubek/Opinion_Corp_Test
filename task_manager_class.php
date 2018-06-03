<?
class FileManager{

    protected static $instance;

    private $path = '';
    private $currentDirFiles = [];
    private $isSorted = false;

    private function __construct(){ }

    private function __clone(){ }

    /*
    * Singleton realization
    * */
    public static function getInstance(){
         if (is_null(self::$instance)) {
             self::$instance = new self;
         }
         return self::$instance;
     }

    public function getPath(){

        return $this->path;
    }

    public function setPath($pathToDir){

        $this->path = $pathToDir;
    }
    /*
    * Collect all files and directories to array currentDirFiles[]
    *
   * @return array
    * */
    public function showDir($pathToDir){

        $this->setPath($pathToDir);

        if (!empty($this->currentDirFiles)){
            $this->currentDirFiles = [];
        }
        if($handle = opendir($this->path)){
            while(false !== ($file = readdir($handle))) {
                if($file != "." && $file != "..") $this->currentDirFiles[] = $file;
            }
        }
        return $this->currentDirFiles;
    }
    /*
     * Sort all files by NAME
     *
     * @return array
     * */
    public function sortByName(){

        if(!$this->isSorted)rsort($this->currentDirFiles);

        else sort($this->currentDirFiles);

        $this->isSorted = !$this->isSorted;

        return $this->currentDirFiles;
    }
    /*
     * Sort all files by TYPE
     *
     * @return array
     * */
    public function sortByType($arr){

        $i = 0;
        foreach ($arr as $a)
        {
            $index = 'idx'.$i;
            $parts = explode('.', $a);
            $path[$index] = $parts[0];
            $ext[$index] = $parts[1];
            $i++;
        }

        if (!$this->isSorted) asort($ext);
        else arsort($ext);

        $this->isSorted = !$this->isSorted;

        $this->currentDirFiles = [];

        foreach ($ext as $key => $value)
        {
            if(!$value) $this->currentDirFiles[] = $path[$key];
            else $this->currentDirFiles[] = $path[$key] . "." . $value;
        }
        return $this->currentDirFiles;
    }
    /*
     * Sort all files by SIZE
     *
     * @return array
     * */
    public function sortBySize(){

        usort($this->currentDirFiles, array($this,'cmp_obj'));

        return $this->currentDirFiles;
    }
    /*
     * Counting size of file or directory with files(using recursion)
     *
     * @param string $filePath
     * @return int $size
     * */

    public function countSize($filePath){

    if(is_file($filePath))
        return filesize($filePath);

        $size = 0;
        $dh = opendir($filePath);

        while(($file=readdir ($dh))!==false){

            if($file == '.' || $file == '..') continue;

            if(is_file($filePath.'/'.$file))
                $size = $size + filesize ($filePath . '/' . $file);
            else $size += $this->countSize($filePath . '/' . $file);
        }
        closedir($dh);

        return $size + filesize($filePath);

    }
    /*
     * Compare size of two nearest files in array
     * Accessory function to sortBySize()
     *
     * @param string $first Name of file or directory. Calculated automatically by usort() function
     * @param string $second Name of file or directory. Calculated automatically by usort() function
     * @return int
     * */
    private function cmp_obj($first, $second){

        $al = $this->countSize($this->path.'/'.$first);
        $bl = $this->countSize($this->path.'/'.$second);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }
    /*
     * Open file in browser. In case with directory, open it and rewrite array $currentDirFiles
     *
     * @param string $fileName Path to file or directory
     * @return array $currentDirFiles or content of file
     * */
    public function openFile($fileName){

        if(is_file($fileName)){

            $fileToOpen = fopen($fileName, "r") or die("Unable to open file!");

            echo fread($fileToOpen,filesize($fileName));

            fclose($fileToOpen);
        }
        if(is_dir($fileName)){

            $this->path = $fileName;
            $this->currentDirFiles = [];

            if($handle = opendir($fileName)){
                while(false !== ($file = readdir($handle))) {
                    if($file != "." && $file != "..") $this->currentDirFiles[] = $file;
                    echo $file;
                }
            }
            return $this->currentDirFiles;
        }
    }
}

$obj = FileManager::getInstance();

?>







