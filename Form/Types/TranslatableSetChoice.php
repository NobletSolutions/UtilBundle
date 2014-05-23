<?php

namespace NS\UtilBundle\Form\Types;

use \JMS\TranslationBundle\Model\Message;

/**
 * Description of TranslatableSetChoice
 *
 * @author gnat
 */
abstract class TranslatableSetChoice extends SetChoice
{
    // return either set or groupedSet
    abstract public function getValues();

    static function getTranslationMessages()
    {
        $class = get_called_class();
        $obj   = new $class();
        $res   = array();

        foreach($obj->getValues() as $val)
        {
            if(is_numeric($val))
                continue;

            $message = new Message($val);
            $res[]   = $message;
        }

        return $res;
    }
}
