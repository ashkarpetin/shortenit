<?php
/**
 * Loader class file.
 *
 * @author Alexander Shkarpetin ashkarpetin@gmail.com
 */
class Loader 
{
   public function view($file_name, $data = null) 
   {
      if(is_array($data)) 
      {
         extract($data);
      }
      include dirname(__FILE__) . '/views/' . $file_name;
   }
}



