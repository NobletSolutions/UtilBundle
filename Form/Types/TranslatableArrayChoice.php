<?php

namespace NS\UtilBundle\Form\Types;

use \JMS\TranslationBundle\Model\Message;
use \JMS\TranslationBundle\Model\SourceInterface;

/**
 * Description of TranslatableArrayChoice
 *
 * @author gnat
 */
class ArraySource implements SourceInterface
{
    private $name;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function equals(SourceInterface $source)
    {
        return ($source->__toString() == $this->name);
    }
}

abstract class TranslatableArrayChoice extends ArrayChoice
{
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
//            $message->addSource(new ArraySource("$class Choice"));
            $res[]   = $message;
        }

        return $res;
    }
}

