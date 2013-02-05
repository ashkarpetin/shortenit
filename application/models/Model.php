<?php
/**
 * Abstract base model class file.
 *
 * @author Alexander Shkarpetin ashkarpetin@gmail.com
 */
abstract class Model
{
    public $error;

    /**
     * @var object reference to a redis object
     */
    protected $redis;

    /**
     * Constructor
     * 
     * Sets the class variable $redis with a reference to a redis object     
     */
    public function __construct()
    {
        $this->redis = Db::instance();
    }

}
