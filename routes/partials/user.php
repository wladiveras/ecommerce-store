<?php

$this->post('/api/user/addresses/new','UserAddressesController@store');
$this->post('/api/user/addresses/new/{id}','UserAddressesController@update');
$this->get('/api/user/addresses','UserAddressesController@index');
$this->post('/api/user/addresses/delete/{id}','UserAddressesController@delete');
$this->group(['prefix' => 'meus-dados'],function(){
  $this->post('','UserAddressesController@edit')->middleware('title:Meus Dados')->name('user.edit');
  $this->post('card/edit','UserAddressesController@cardEdit')->middleware('title:Card Edit')->name('user.card.edit');
  $this->post('card/delete','UserAddressesController@cardDelete')->middleware('title:Card Delete')->name('user.card.delete');
  $this->get('','UserAddressesController@index_view')->middleware('title:Meus Dados')->name('user.data');
});