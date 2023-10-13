<?php

use function PHPStan\Testing\assertType;

$collection = User::all();
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection);

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>|User|null', $collection->find(1));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>|string|User', $collection->find(1, 'string'));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->load('string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->load(['string']));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->load(['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}]));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadAggregate('string', 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadAggregate(['string'], 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadAggregate(['string'], 'string', 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadAggregate(['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}], 'string', 'string'));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadCount('string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadCount(['string']));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadCount(['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}]));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMax('string', 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMax(['string'], 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMax(['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}], 'string'));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMin('string', 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMin(['string'], 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMin(['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}], 'string'));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadSum('string', 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadSum(['string'], 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadSum(['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}], 'string'));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadAvg('string', 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadAvg(['string'], 'string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadAvg(['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}], 'string'));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadExists('string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadExists(['string']));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadExists(['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}]));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMissing('string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMissing(['string']));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMissing(['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}]));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMorph('string', ['string']));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMorph('string', ['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}]));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMorphCount('string', ['string']));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->loadMorphCount('string', ['string' => function ($query) {
    // assertType('\QuantaQuirk\Database\Query\Builder', $query);
}]));

assertType('bool', $collection->contains(function ($user) {
    assertType('User', $user);

    return true;
}));
assertType('bool', $collection->contains('string', '=', 'string'));

assertType('array<int, (int|string)>', $collection->modelKeys());

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->merge($collection));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->merge([new User]));

assertType(
    'QuantaQuirk\Support\Collection<int, User>',
    $collection->map(function ($user, $int) {
        assertType('User', $user);
        assertType('int', $int);

        return new User;
    })
);

assertType(
    'QuantaQuirk\Support\Collection<int, User>',
    $collection->mapWithKeys(function ($user, $int) {
        assertType('User', $user);
        assertType('int', $int);

        return [new User];
    })
);
assertType(
    'QuantaQuirk\Support\Collection<string, User>',
    $collection->mapWithKeys(function ($user, $int) {
        return ['string' => new User];
    })
);

assertType(
    'QuantaQuirk\Database\Eloquent\Collection<int, User>',
    $collection->fresh()
);
assertType(
    'QuantaQuirk\Database\Eloquent\Collection<int, User>',
    $collection->fresh('string')
);
assertType(
    'QuantaQuirk\Database\Eloquent\Collection<int, User>',
    $collection->fresh(['string'])
);

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->diff($collection));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->diff([new User]));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->intersect($collection));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->intersect([new User]));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->unique());
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->unique(function ($user, $int) {
    assertType('User', $user);
    assertType('int', $int);

    return $user->getTable();
}));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->unique('string'));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->only(null));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->only(['string']));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->except(null));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->except(['string']));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->makeHidden('string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->makeHidden(['string']));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->makeVisible('string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->makeVisible(['string']));

assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->append('string'));
assertType('QuantaQuirk\Database\Eloquent\Collection<int, User>', $collection->append(['string']));

assertType('array<User>', $collection->getDictionary());
assertType('array<User>', $collection->getDictionary($collection));
assertType('array<User>', $collection->getDictionary([new User]));

assertType('QuantaQuirk\Support\Collection<(int|string), mixed>', $collection->pluck('string'));
assertType('QuantaQuirk\Support\Collection<(int|string), mixed>', $collection->pluck(['string']));

assertType('QuantaQuirk\Support\Collection<int, int>', $collection->keys());

assertType('QuantaQuirk\Support\Collection<int, QuantaQuirk\Support\Collection<int, int|User>>', $collection->zip([1]));
assertType('QuantaQuirk\Support\Collection<int, QuantaQuirk\Support\Collection<int, string|User>>', $collection->zip(['string']));

assertType('QuantaQuirk\Support\Collection<int, mixed>', $collection->collapse());

assertType('QuantaQuirk\Support\Collection<int, mixed>', $collection->flatten());
assertType('QuantaQuirk\Support\Collection<int, mixed>', $collection->flatten(4));

assertType('QuantaQuirk\Support\Collection<User, int>', $collection->flip());

assertType('QuantaQuirk\Support\Collection<int, int|User>', $collection->pad(2, 0));
assertType('QuantaQuirk\Support\Collection<int, string|User>', $collection->pad(2, 'string'));

assertType('array<int, mixed>', $collection->getQueueableIds());

assertType('array<int, string>', $collection->getQueueableRelations());
