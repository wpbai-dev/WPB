@extends('admin.layouts.form')
@section('section', translate('Settings'))
@section('title', translate('Item Settings'))
@section('container', 'container-max-lg')
@section('content')
    <form id="vironeer-submited-form" action="{{ route('admin.settings.item.update') }}" method="POST">
        @csrf
        <div class="card mb-4">
            <div class="card-header">{{ translate('Actions') }}</div>
            <div class="card-body p-4">
                <div class="row row-cols-1 row-cols-lg-3 row-cols-xxl-3 g-3">
                    <div class="col">
                        <label class="form-label">{{ translate('Show item total sales') }}</label>
                        <input type="checkbox" name="item[item_total_sales]" data-toggle="toggle" data-height="38px"
                            data-on="{{ translate('Yes') }}" data-off="{{ translate('No') }}"
                            {{ @$settings->item->item_total_sales ? 'checked' : '' }}>
                    </div>
                    <div class="col">
                        <label class="form-label">{{ translate('Show free item total downloads') }}</label>
                        <input type="checkbox" name="item[free_item_total_downloads]" data-toggle="toggle"
                            data-height="38px" data-on="{{ translate('Yes') }}" data-off="{{ translate('No') }}"
                            {{ @$settings->item->free_item_total_downloads ? 'checked' : '' }}>
                    </div>
                    <div class="col">
                        <label class="form-label">{{ translate('Download free items require login') }}</label>
                        <input type="checkbox" name="item[free_items_require_login]" data-toggle="toggle" data-height="38px"
                            data-on="{{ translate('Yes') }}" data-off="{{ translate('No') }}"
                            {{ @$settings->item->free_items_require_login ? 'checked' : '' }}>
                    </div>
                    <div class="col">
                        <label class="form-label">{{ translate('Buy Now Button') }}</label>
                        <input type="checkbox" name="item[buy_now_button]" data-toggle="toggle" data-height="38px"
                            data-on="{{ translate('Enable') }}" data-off="{{ translate('Disable') }}"
                            {{ @$settings->item->buy_now_button ? 'checked' : '' }}>
                    </div>
                    <div class="col">
                        <label class="form-label">{{ translate('Item Changelogs') }}</label>
                        <input type="checkbox" name="item[changelogs_status]" data-toggle="toggle" data-height="38px"
                            data-on="{{ translate('Enable') }}" data-off="{{ translate('Disable') }}"
                            {{ @$settings->item->changelogs_status ? 'checked' : '' }}>
                    </div>
                    <div class="col">
                        <label class="form-label">{{ translate('Item Reviews') }}</label>
                        <input type="checkbox" name="item[reviews_status]" data-toggle="toggle" data-height="38px"
                            data-on="{{ translate('Enable') }}" data-off="{{ translate('Disable') }}"
                            {{ @$settings->item->reviews_status ? 'checked' : '' }}>
                    </div>
                    <div class="col">
                        <label class="form-label">{{ translate('Item Comments') }}</label>
                        <input type="checkbox" name="item[comments_status]" data-toggle="toggle" data-height="38px"
                            data-on="{{ translate('Enable') }}" data-off="{{ translate('Disable') }}"
                            {{ @$settings->item->comments_status ? 'checked' : '' }}>
                    </div>
                    <div class="col">
                        <label class="form-label">{{ translate('Item Support') }}</label>
                        <input type="checkbox" name="item[support_status]" data-toggle="toggle" data-height="38px"
                            data-on="{{ translate('Enable') }}" data-off="{{ translate('Disable') }}"
                            {{ @$settings->item->support_status ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">{{ translate('Trending And Best selling') }}</div>
            <div class="card-body p-4">
                <div class="row g-3 mb-3">
                    <div class="col-lg-6">
                        <label class="form-label">{{ translate('Trending Items Number') }}</label>
                        <input type="number" class="form-control" name="item[trending_number]" min="1"
                            value="{{ @$settings->item->trending_number }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ translate('Best Selling Items Number') }}</label>
                        <input type="number" class="form-control" name="item[best_selling_number]" min="1"
                            value="{{ @$settings->item->best_selling_number }}" required>
                    </div>
                </div>
                <div class="alert alert-warning mb-0">
                    <i class="fa-regular fa-circle-question me-1"></i>
                    <span>{{ translate('You must setup the cron job to refresh the Items every 24 hours.') }}</span>
                    <a href="{{ route('admin.system.cronjob.index') }}">{{ translate('Setup Cron Job') }}<i
                            class="fa-solid fa-arrow-right fa-rtl ms-2"></i></a>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                {{ translate('Files') }}
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-lg-12">
                        <label class="form-label">{{ translate('File Duration') }}</label>
                        <div class="input-group">
                            <input type="number" name="item[file_duration]" class="form-control" min="1"
                                step="any" value="{{ @$settings->item->file_duration }}" required>
                            <span class="input-group-text"><i
                                    class="fa-regular fa-clock me-1"></i>{{ translate('Hours') }}</span>
                        </div>
                        <div class="form-text">
                            {{ translate('Uploaded files will expire after this time if you did not use them.') }}
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ translate('Convert Images To WEBP') }}</label>
                        <select name="item[convert_images_webp]" class="form-select form-select-md">
                            <option value="0" @selected(@$settings->item->convert_images_webp == 0)>
                                {{ translate('No') }}</option>
                            <option value="1" @selected(@$settings->item->convert_images_webp == 1)>
                                {{ translate('Yes') }}</option>
                        </select>
                        <div class="form-text">
                            {{ translate('Convert uploaded images to WEBP like Preview Image, Screenshots, etc...') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @push('styles_libs')
        <link rel="stylesheet" href="{{ asset('vendor/libs/tags-input/bootstrap-tagsinput.css') }}">
    @endpush
    @push('scripts_libs')
        <script src="{{ asset('vendor/libs/tags-input/bootstrap-tagsinput.min.js') }}"></script>
    @endpush
    @push('scripts')
        <script>
            "use strict";
            let discountStatus = $('#discountStatus');
            discountStatus.on('change', function() {
                let discountDetails = $('#discountDetails');
                if ($(this).is(':checked')) {
                    discountDetails.removeClass('d-none');
                } else {
                    discountDetails.addClass('d-none');
                }
            });
        </script>
    @endpush
@endsection
