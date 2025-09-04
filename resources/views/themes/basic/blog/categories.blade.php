@extends('themes.basic.blog.layout')
@section('section', translate('Blog'))
@section('header_title', translate('Categories'))
@section('title', translate('Categories'))
@section('breadcrumbs', Breadcrumbs::render('blog_categories'))
@section('header', true)
@section('content')
    @if ($blogCategories->count() > 0)
        <div class="list-group">
            @foreach ($blogCategories as $blogCategory)
                <a href="{{ route('blog.category', $blogCategory->slug) }}"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-4">
                    <span>{{ $blogCategory->name }} ({{ $blogCategory->articles_count }})</span>
                    <i class="fa-solid fa-tag"></i>
                </a>
            @endforeach
        </div>
        {{ $blogCategories->links() }}
    @else
        <div class="box bg-color p-5 text-center">
            <div class="py-3">
                <i class="fa-regular fa-file fa-lg"></i>
                <div class="text-muted">{{ translate('No categories found') }}</div>
            </div>
        </div>
    @endif
@endsection
