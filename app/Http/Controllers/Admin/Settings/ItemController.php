<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\StorageProvider;
use Illuminate\Http\Request;
use Validator;

class ItemController extends Controller
{
    public function index()
    {
        $storageProviders = StorageProvider::all();
        return view('admin.settings.item', ['storageProviders' => $storageProviders]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item.trending_number' => ['required', 'integer', 'min:1'],
            'item.best_selling_number' => ['required', 'integer', 'min:1'],
            'item.file_duration' => ['required', 'integer', 'min:1'],
            'item.convert_images_webp' => ['required', 'boolean'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                toastr()->error($error);
            }
            return back();
        }

        $requestData = $request->except('_token');
        $itemSettings = $requestData['item'];

        $itemSettings['item_total_sales'] = ($request->has('item.item_total_sales')) ? 1 : 0;
        $itemSettings['free_item_total_downloads'] = ($request->has('item.free_item_total_downloads')) ? 1 : 0;
        $itemSettings['free_items_require_login'] = ($request->has('item.free_items_require_login')) ? 1 : 0;
        $itemSettings['buy_now_button'] = $request->has('item.buy_now_button') ? 1 : 0;
        $itemSettings['reviews_status'] = ($request->has('item.reviews_status')) ? 1 : 0;
        $itemSettings['comments_status'] = ($request->has('item.comments_status')) ? 1 : 0;
        $itemSettings['support_status'] = ($request->has('item.support_status')) ? 1 : 0;
        $itemSettings['changelogs_status'] = ($request->has('item.changelogs_status')) ? 1 : 0;

        $update = Settings::updateSettings('item', $itemSettings);
        if (!$update) {
            toastr()->error(translate('Updated Error'));
            return back();
        }

        toastr()->success(translate('Updated Successfully'));
        return back();
    }
}
