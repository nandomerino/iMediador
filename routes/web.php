<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

/* vvvvvv PUBLIC vvvvvv */
Route::view('/', 'public.home');
Route::view('/informacion', 'public.info');
Route::view('/ayuda', 'public.help');
Route::view('/contacto', 'public.contact');
Route::view('/politica-privacidad', 'public.privacy');
Route::view('/politica-cookies', 'public.cookies');
Route::view('/aviso-legal', 'public.legal');
Route::view('/blog', 'public.blog');
Route::get('/blog/{slug}', function($slug) {
    return View::make('public.blog-posts')->with('slug', $slug);
});
/* ^^^^^^ PUBLIC ^^^^^^ */


/* vvvvvv APP vvvvvv */
Route::group(['middleware' => ['userLogged']], function () {
    Route::view('/app', 'app.home');
    Route::view('/app/tarificacion', 'app.quote');
    Route::view('/app/enviar-solicitud', 'app.send-policy-request');
    Route::view('/app/descargar-documentos', 'app.downloads');
    Route::view('/app/tarificacion', 'app.quote');
    Route::view('/app/documentacion', 'app.documentation');
    Route::view('/app/soporte', 'app.support');
    Route::view('/app/politica-privacidad', 'app.privacy');
    Route::view('/app/politica-cookies', 'app.cookies');
    Route::view('/app/aviso-legal', 'app.legal');
    Route::view('/app/novedades', 'app.blog');
    Route::get('/app/novedades/{slug}', function($slug) {
        return View::make('app.blog-posts')->with('slug', $slug);
    });
});
/* ^^^^^^ APP ^^^^^^ */

/* vvvvvv WIDGET vvvvvv */
Route::view('/widget', 'widget.index');
Route::view('/embed', 'widget.embed');
Route::view('/widget/style.css', 'widget.css');
Route::view('/widget/custom.js', 'widget.js');
/* ^^^^^^ WIDGET ^^^^^^ */

/* vvvvvv PANEL vvvvvv */
Route::view('/panel/login', 'panel.login');
Route::group(['middleware' => ['userLoggedPanel'] ], function () {
    Route::view('/panel', 'panel.home');
    Route::view('/panel/suplantador', 'panel.suplantador');
    Route::view('/panel/sliders', 'panel.sliders');

});
/* ^^^^^^ PANEL ^^^^^^ */


/* vvvvvv OTHERS vvvvvv */
Route::post('/send-contact-form', 'FormController@contactForm');
Route::post('/send-login-form', 'FormController@loginForm');
Route::post('/slider-ajax', 'SliderController@do');
Route::get('url-login', 'FormController@loginForm');
Route::get('logout', 'UserSession@logout');
Route::post('/get-data', 'PMWSjs@getData');
Route::post('/send-mail-request-data', 'MailController@sendFromRequest');
Route::post('/send-mail-this-data', 'MailController@sendThis');
Route::view('lang.js', 'localization.lang');
Route::view('/download','download.file');
Route::post('/send-mail-html', 'MailController@sendHTML');
Route::post('/send-mail-budget', 'MailController@sendBudget');	
/* ^^^^^^ OTHERS ^^^^^^ */


/* vvvvvv TOOLS AND TESTS vvvvvv */
Route::view('/pmapi-test', 'tests.PMWSTEST');
Route::get('/artisan-clear', function() {
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('cache:clear');
    return "Routes, Views, Config and Cache are cleared";
});
Route::get('/session-info', function() {
    echo "<pre>";
    var_dump( Session::all() );
});
/* ^^^^^^ TOOLS AND TESTS ^^^^^^ */


/*
 *
 * Multi-language routes and locales
 * https://laraveldaily.com/multi-language-routes-and-locales-with-auth/
 *

Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => '[a-zA-Z]{2}']
], function() {

    Route::get('/', function () {
        return view('welcome');
    });

    Auth::routes();

    Route::get('/home', 'HomeController@index')->name('home');
});


Route::get('/home', function () {
    return view('home');
});
*/
