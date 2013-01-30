<?php

namespace NS\UtilBundle\Service;

interface AjaxAutocompleteRepositoryInterface
{
    public function getForAutoComplete($alias,$fields,array $value,$limit);
}

