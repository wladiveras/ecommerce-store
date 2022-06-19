<?php

Route::group(['prefix' => 'termos-de-uso'], function () {
  $this->get('normas-de-envio',"HomeController@normas_art_view")->middleware('title:Termos de Uso')->name('use-normas-art');
  // $this->get('termos-de-uso',function(){
  //   return view('pages.general.use-terms');
  // })->middleware('title:Termos de Uso')->name('use-terms');
});

$this->post('/new-reseller-request','ApiController@resellerRequest');
$this->get('/register/thank-you','ApiController@registerFinished');
/*
$this->get('politicas-de-privacidade',function(){
  return view('pages.general.privacy-policy');
})->middleware('title:Políticas de Privacidade')->name('privacy-policy');
$this->get('sobre',function(){
  return view('pages.general.about');
})->middleware('title:Sobre')->name('about');
$this->get('perguntas-frequentes',function(){
  return view('pages.general.faq');
})->middleware('title:Perguntas Frequentes')->name('faq');
$this->get('contato',function(){
  return view('pages.general.contact');
})->middleware('title:Contato')->name('contact');
$this->post('contato','MailController@send');*/
