<?php

namespace NS\UtilBundle\Entity\Types;

use \Doctrine\DBAL\Types\IntegerType;
use \Doctrine\DBAL\Platforms\AbstractPlatform;

class ArrayChoice extends IntegerType
{
    protected $convert_class = 'stdClass';
    
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new $this->convert_class($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if($value === null) {
            return null;
        }
 
        if(is_object($value)) {
            return $value->getValue();
        } elseif(is_numeric($value)) {
            return $value;
        } else {
            return null;
        }
    }
}
