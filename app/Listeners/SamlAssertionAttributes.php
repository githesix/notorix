<?php

namespace App\Listeners;

use LightSaml\ClaimTypes;
use LightSaml\Model\Assertion\Attribute;
use CodeGreenCreative\SamlIdp\Events\Assertion;

class SamlAssertionAttributes
{
    public function handle(Assertion $event)
    {
        $event->attribute_statement
        ->addAttribute(new Attribute(ClaimTypes::PPID, auth()->user()->uid))
        ->addAttribute(new Attribute(ClaimTypes::NAME, auth()->user()->name))
        ->addAttribute(new Attribute(ClaimTypes::GIVEN_NAME, auth()->user()->prenom))
        ->addAttribute(new Attribute(ClaimTypes::COMMON_NAME, auth()->user()->nom))
        ->addAttribute(new Attribute(ClaimTypes::ROLE, auth()->user()->samlrole));
    }
}