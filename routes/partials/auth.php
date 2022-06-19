<?php

$this->get('login', 'Auth\LoginController@showLoginForm')->middleware('title:Login')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->get('logout', 'Auth\LoginController@logout')->name('logout');



Route::get('auth/{provider}', 'Auth\LoginController@redirectToProvider')->name("login_social");
Route::get('auth/{provider}/callback', 'Auth\LoginController@handleProviderCallback');
