<?php

namespace App\Providers;

use Lyra\Providers\ServiceProvider;
use Lyra\Validation\Rule;

class RuleServiceProvider implements ServiceProvider {
    public function registerServices() {
        Rule::loadDefaultRules();
    }
}