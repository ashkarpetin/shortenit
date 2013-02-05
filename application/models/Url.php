<?php
/**
 * Url class file.
 *
 * @author Alexander Shkarpetin ashkarpetin@gmail.com
 */

class Url extends Model
{	
    public $url;

    /**
     * @var boolean check if url exists by trying to connect throug it
     */
    protected static $checkUrlExists = false;

    /**
     * Create a hash from a long URL.
     *
     * @return string the url hash
     */    
    public function toHash()
    {    						       
        require_once (dirname(__FILE__) . '/../PseudoCrypt.php');

        $counter = $this->redis->incr('counter:id');
        $hash = PseudoCrypt::udihash($counter);
        $this->redis->setnx("urls:" . $hash, $this->url);

        return $hash;
    }

    /**
     * Validates URL format, connect to the URL to make sure it exists (optionally)
     *
     * @return boolean whether Url is valid
     */  
    public function validate()
    {
        if (empty($this->url))
        {
           $this->error = "No URL!";
        }

        if (empty($this->error) && $this->validateUrl($this->url) == false) 
        {
            $this->error = "Invalid URL!";
        }

        if (self::$checkUrlExists) 
        {
            if (empty($this->error) && !$this->verifyUrlExists($this->url)) 
            {
               $this->error = "URL does not exist.";
            }
        }
        return empty($this->error);
    }
    
    /**
     * Check if the URL in a valid format
     * 
     * @param string $url the long URL
     * @return boolean whether URL is a valid format
     */
   	private function validateUrl($url)
	{
        return filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED);
    }

     /* Check if the URL exists
     * 
     * Using cURL to access the URL and check if response code 404 returned
     * 
     * @param string $url the long URL
     * @return boolean whether the URL does not return response code 404
     */
    private function verifyUrlExists($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return (!empty($response) && $response != 404);
    }



}