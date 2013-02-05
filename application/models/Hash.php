<?php
/**
 * Hash class file.
 *
 * @author Alexander Shkarpetin ashkarpetin@gmail.com
 */
class Hash extends Model
{
    public $hash;

    /**
     * Get url from db by its hash
     *
     * @param boolean $increment whether to increment url user redirect count
     * @return string the long url
     */   
    public function toUrl($increment = true) 
    {               
        $url = $this->redis->get("urls:" . $this->hash);

        if ($increment == true) 
        {
            $this->redis->hincrby("stat:" . $this->hash, "count", 1);
        }

        return $url;
    }

    /**
     * Validates hash format, whether hash exists in url database
     *
     * @return boolean whether Hash is valid and exists in database
     */  
    public function validate()
    {
        if (empty($this->hash))
        {
            $this->error = "No hash!";
        }

        if (empty($this->error) && $this->validateHash($this->hash) == false)
        {
            $this->error = "Invalid hash!";
        }

        if (empty($this->error))
        {
            $url = $this->redis->get("urls:" . $this->hash);
            if ($url == null)
            {
               $this->error = "Url does not exist.";
            }
        } 

        return empty($this->error);
    }

    /**
     * Check if hash in valid format
     * 
     * @param string $hash the hash
     * @return boolean if hash in valid format
     */
    private function validateHash($hash) 
    {
        return ctype_alnum($hash);
    }
}