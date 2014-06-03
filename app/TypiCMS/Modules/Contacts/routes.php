<?php
Route::bind('contacts', function ($value, $route) {
    return TypiCMS\Modules\Contacts\Models\Contact::where('id', $value)
        ->firstOrFail();
});

if (! App::runningInConsole()) {
    Route::group(array('before' => 'auth.public|cache', 'after' => 'cache'), function () {
        $routes = app('TypiCMS.routes');
        foreach (Config::get('app.locales') as $lang) {
            $uri = (array_key_exists('contacts', $routes)) ? $routes['contacts'][$lang] : $lang.'/contacts' ;
            Route::get(
                $uri,
                array(
                    'as' => $lang.'.contacts',
                    'uses' => 'TypiCMS\Modules\Contacts\Controllers\PublicController@index'
                )
            );
        }
    });
}

Route::post(
    'contact-store_',
    array(
        'before' => 'csrf',
        'as'     => 'contacts.index',
        'uses'   => 'TypiCMS\Modules\Contacts\Controllers\PublicController@store'
    )
);

Route::group(array('prefix' => 'admin', 'before' => 'auth.admin'), function () {
    Route::resource('contacts', 'TypiCMS\Modules\Contacts\Controllers\AdminController');
});
