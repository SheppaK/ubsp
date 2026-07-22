@extends('layouts.platform')
@section('title', $config['name'])
@section('header', $config['name'])
@section('content')
@php
    $resourceRoute = 'modules.balanced-scorecard.kpis.index';
    $resourceLabel = 'KPIs';
    $features = array (
  0 => 'Objectives',
  1 => 'KPIs',
  2 => 'Targets',
  3 => 'Reports',
  4 => 'Traffic lights',
);
@endphp
@include('modules.partials.dashboard-content')
@endsection