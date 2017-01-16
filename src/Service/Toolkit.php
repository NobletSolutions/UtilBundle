<?php

namespace NS\UtilBundle\Service;

/**
 * Description of Toolkit
 *
 * @author gnat
 */
class Toolkit
{
    /**
     * @param $text
     * @return mixed|string
     */
    public function stripTextAndHtmlEntities($text)
    {
        return $this->stripText(preg_replace('/&([#A-Za-z0-9]+);/', '', $text));
    }

    /**
     * @param $text
     * @return mixed|string
     */
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
