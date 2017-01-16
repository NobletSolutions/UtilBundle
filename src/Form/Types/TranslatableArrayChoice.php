<?php

namespace NS\UtilBundle\Form\Types;

use JMS\TranslationBundle\Model\Message;

abstract class TranslatableArrayChoice extends ArrayChoice
{
    static function getTranslationMessages()
    {
        $class = get_called_class();
        $obj = new $class();
        $res = array();

        foreach ($obj->getValues() as $val) {
            if (is_numeric($val)) {
                continue;
            }

            $message = new Message($val);
            $res[] = $message;
        }

        return $res;
    }
}
