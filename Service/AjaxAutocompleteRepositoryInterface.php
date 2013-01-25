<?php

namespace NS\UtilBundle\Service;

interface AjaxAutocompleteRepositoryInterface
{
    public function getForAutoComplete($alias,$field,$value,$limit);
}

