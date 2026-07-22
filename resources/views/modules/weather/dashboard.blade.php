@extends('layouts.platform')
@section('title', $config['name'])
@section('header', $config['name'])
@section('content')
@php
    $resourceRoute = 'modules.weather.locations.index';
    $resourceLabel = 'Locations';
    $features = array (
  0 => 'Forecast',
  1 => 'Temperature',
  2 => 'Humidity',
  3 => 'Wind',
);
@endphp
@include('modules.partials.dashboard-content')
@endsection