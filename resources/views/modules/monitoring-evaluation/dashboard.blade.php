@extends('layouts.platform')
@section('title', $config['name'])
@section('header', $config['name'])
@section('content')
@php
    $resourceRoute = 'modules.monitoring-evaluation.projects.index';
    $resourceLabel = 'Projects';
    $features = array (
  0 => 'Projects',
  1 => 'Indicators',
  2 => 'Evidence',
  3 => 'Dashboards',
);
@endphp
@include('modules.partials.dashboard-content')
@endsection