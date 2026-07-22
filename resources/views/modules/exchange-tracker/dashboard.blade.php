@extends('layouts.platform')
@section('title', $config['name'])
@section('header', $config['name'])
@section('content')
@php
    $resourceRoute = 'modules.exchange-tracker.rates.index';
    $resourceLabel = 'Rates';
    $features = array (
  0 => 'Exchange rates',
  1 => 'Fuel',
  2 => 'Food',
  3 => 'Alerts',
);
@endphp
@include('modules.partials.dashboard-content')
@endsection