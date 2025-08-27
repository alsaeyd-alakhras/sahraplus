<?php

namespace App\Observers;

use App\Models\Person;


class PersonObserver
{
    public function boot(): void
{
    Person::observe(PersonObserver::class);
}
}
