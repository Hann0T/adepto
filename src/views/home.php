<h1>Hello {{ $name }}</h1>
<p><strong>str_replace:</strong> {{ str_replace('*', '8', 'asdf *asdf   s') }}</p>
<p><strong>2 raised to the power of 4:</strong> {{ pow(2,   4) }}</p>
<p><strong>array implode:</strong> {{ implode(['apples', ' pears and oranges', 'bananas']) }}</p>
<p><strong>with normal php syntaxis:</strong> <?= implode(['number 1,', ' number 2', ' and number 3']); ?></p>
