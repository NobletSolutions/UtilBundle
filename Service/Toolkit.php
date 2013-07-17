<?php

namespace NS\UtilBundle\Service;

/**
 * Description of Toolkit
 *
 * @author gnat
 */
class Toolkit
{
    public function stripTextAndHtmlEntities($text)
    {
        return $this->stripText(ereg_replace('&([a-z]+);', '', $text));
    }

    public function stripText($text)
    {
        $text = mb_strtolower(trim($text), 'UTF-8');

        $text = strip_tags($text);

        // strip all whitespace chars
        $text = preg_replace('/\s/', '-', $text);

        // trim dashes
        $text = preg_replace('/\-$/', '', $text);
        $text = preg_replace('/^\-/', '', $text);

        $text = str_replace("&","",$text);

        return $text;
    }    //put your code here
}