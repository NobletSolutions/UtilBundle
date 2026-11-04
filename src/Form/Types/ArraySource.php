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
    private ?string $name = null;

    /**
     * @param string|null $name
     */
    public function __construct(?string $name = null)
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function equals(SourceInterface $source): bool
    {
        return ($source->__toString() === $this->name);
    }
}
