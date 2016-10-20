<?php

namespace FilePatchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * webDAV details, as well as the davDownload and davUpload functions.
 *
 * @ORM\Table(name="dav")
 * @ORM\Entity(repositoryClass="FilePatchBundle\Repository\DavRepository")
 */
class Dav
{

    /**
     * The directory that all files will be downloaded to.
     * @var string
     *
     */
    public $downloadDirectory =  "/home/coyle.intuitsolutions-apps.net/web/sandbox/downloads/";
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="davUrl", type="string", length=255, unique=true)
     */
    private $davUrl;

  /**
     * @var string
     *
     * @ORM\Column(name="store", type="string", length=255, unique=true)
     */
    private $store;


    /**
     * @var string
     *
     * @ORM\Column(name="userName", type="string", length=255, unique=false)
     */
    private $userName;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="text")
     */
    private $password;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set davUrl
     *
     * @param string $davUrl
     * @return Dav
     */
    public function setDavUrl($davUrl)
    {
        $this->davUrl = $davUrl;

        return $this;
    }

    /**
     * Get davUrl
     *
     * @return string 
     */
    public function getDavUrl()
    {
        return $this->davUrl;
    }

  /**
     * Set Store
     *
     * @param string $store
     * @return Dav
     */
    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * Get Store
     *
     * @return string 
     */
    public function getStore()
    {
        return $this->store;
    }


    /**
     * Set userName
     *
     * @param string $userName
     * @return Dav
     */
    public function setUserName($userName)
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * Get userName
     *
     * @return string 
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Dav
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

///////CUSTOM SCRIPTS/////////////////////////////////////////////////////////////
/**
 * Download a file from a webDAV client
 * @param  object $davCred  the :Dav object that contains the login credentials
 * @param  string $fileName the file name to download from webDAV client (include directory prefixes eg: 'template/Panels/checkout_express.html')
 * @param  string $saveAs   save $fileName as this in the download directory
 * @param  string $backUp  not currently in use.
 * @return array           returns the status and a message if there is an error
 */
    public function davDownload($davCred, $fileName, $saveAs,   $backup = false){
        $davCred = (object) $davCred;

        if(strpos($saveAs, "/") !== false){
           $folders = explode("/", $saveAs);
           foreach ($folders as $folder) {
               if(strpos($folder, ".") === false){
                    if(file_exists($this->downloadDirectory.$folder) === false){
                        mkdir($this->downloadDirectory.$folder);
                    }
               }
           }
        }
        $saveAs = "{$this->downloadDirectory}{$saveAs}";
        if(file_exists($saveAs)){
            unlink($saveAs);
        }
        $download = "curl --digest --user {$davCred->userName}:{$davCred->password} {$davCred->davUrl}/{$fileName} --output {$saveAs}";
        exec($download);
        $result = [];
        if(file_exists($saveAs)){
            copy($saveAs,$saveAs.".backUp.".$davCred->id);
            $content = file_get_contents($saveAs);
            if(strpos($content, "Sabre\DAV\Exception") <= 0){
                $result['success'] =  1;
            }
            else{
                $result['success'] =  0;
                $content = explode("<s:message>",$content);
                $content = end($content);
                $content = strip_tags($content);
                if(trim($content) == "Incorrect username"){
                    $content .= " or password.";
                }
                $result['message'] = $content;
            }
        unlink($saveAs);
        }
        else{
                $result['success'] =  0;
                $result['message'] = "file not created";
        }
        return $result;
    }


    /**
     * Upload a file to a webDAV client
     * @param  object $davCred  [description]
     * @param  string $fileName the name of the file to upload, as well as the name of the file that will be saved in the webDAV
     * @return string           if blank, all went well.
     */
    public function davUpload($davCred, $fileName){
        $davCred = (object) $davCred;
        if(strpos($fileName, "/") !== false){
            $folders = explode("/",$fileName);
            $fileName = end($folders);
            array_pop($folders);
            $davFolder = implode("/", $folders)."/";
        }
        else{
            $davFolder = "";
        }
        $upload = "curl --digest --user {$davCred->userName}:{$davCred->password} -T {$this->downloadDirectory}{$davFolder}{$fileName} {$davCred->davUrl}/{$davFolder}";
        return shell_exec($upload);

    }



}
