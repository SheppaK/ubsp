@extends('layouts.platform')
@section('title', $config['name'])
@section('header', $config['name'])
@section('content')
@php
    $resourceRoute = 'modules.marketplace.products.index';
    $resourceLabel = 'Products';
    $features = array (
  0 => 'Categories',
  1 => 'Products',
  2 => 'Wishlist',
  3 => 'Reviews',
);
@endphp
@include('modules.partials.dashboard-content')
@endsection