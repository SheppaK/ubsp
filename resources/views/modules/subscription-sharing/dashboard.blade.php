@extends('layouts.platform')
@section('title', $config['name'])
@section('header', $config['name'])
@section('content')
@php
    $resourceRoute = 'modules.subscription-sharing.plans.index';
    $resourceLabel = 'Plans';
    $features = array (
  0 => 'Plans',
  1 => 'Members',
  2 => 'Renewals',
  3 => 'Usage logs',
);
@endphp
@include('modules.partials.dashboard-content')
@endsection