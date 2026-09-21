<?php

test('redirects visitors to the English website', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect('/en');
});
