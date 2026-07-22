@extends('layouts.platform')
@section('title', $config['name'])
@section('header', $config['name'])
@section('content')
@php
    $resourceRoute = 'modules.university-social.posts.index';
    $resourceLabel = 'Posts';
    $features = array (
  0 => 'Posts',
  1 => 'Likes',
  2 => 'Comments',
  3 => 'Events',
  4 => 'Groups',
  5 => 'Messaging',
);
@endphp
@include('modules.partials.dashboard-content')
@endsection