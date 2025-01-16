<?php

use Mantas6\FzfPhp\FuzzyFinder;
use Tests\FakeProcess;

use function Mantas6\FzfPhp\fzf;

beforeEach(fn () => FuzzyFinder::usingProcessClass(FakeProcess::class));

it('renders basic options correctly', function () {
    fzf(['Apple', 'Orange', 'Grapefruit']);

    expect(FakeProcess::$lastInput)->toMatchSnapshot();
});
