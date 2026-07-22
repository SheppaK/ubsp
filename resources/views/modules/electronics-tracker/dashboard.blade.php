@extends('layouts.platform')
@section('title', $config['name'])
@section('header', $config['name'])
@section('content')
@php
    $resourceRoute = 'modules.electronics-tracker.assets.index';
    $resourceLabel = 'Assets';
    $features = array (
  0 => 'Purchase date',
  1 => 'Warranty',
  2 => 'Serial numbers',
  3 => 'QR Code',
  4 => 'Maintenance',
  5 => 'Disposal',
);
@endphp
@include('modules.partials.dashboard-content')
@endsection