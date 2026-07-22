@extends('layouts.platform')
@section('title', $config['name'])
@section('header', $config['name'])
@section('content')
@php
    $resourceRoute = 'modules.sports-league.leagues.index';
    $resourceLabel = 'Leagues';
    $features = array (
  0 => 'Leagues',
  1 => 'Teams',
  2 => 'Fixtures',
  3 => 'Standings',
);
@endphp
@include('modules.partials.dashboard-content')
@endsection