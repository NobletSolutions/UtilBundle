<?php

namespace NS\UtilBundle\Service;

interface AjaxAutocompleteRepositoryInterface
{
    public function getForAutoComplete($fields,array $value,$limit);
}

