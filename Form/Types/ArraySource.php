<?php
/**
 * Created by PhpStorm.
 * User: gnat
 * Date: 31/08/16
 * Time: 4:53 PM
 */

namespace NS\UtilBundle\Form\Types;

use JMS\TranslationBundle\Model\SourceInterface;

/**
 * Description of TranslatableArrayChoice
 *
 * @author gnat
 */
class ArraySource implements SourceInterface
{
    /** @var string */
    private $name;

    /**
     * @param string|null $name
     */
    public function __construct($name = null)
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @param SourceInterface $source
     * @return bool
     */
    public function equals(SourceInterface $source)
    {
        return ($source->__toString() == $this->name);
    }
}
