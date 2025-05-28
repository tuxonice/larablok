@extends('layouts.app')

@section('title', $article['content']['title'] ?? 'Article Detail')

@section('content')
    <livewire:article-detail :slug="$slug" />
@endsection
