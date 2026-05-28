<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\JsonCmsStore;
use Illuminate\Http\Request;

class CvController extends Controller
{
    public function edit(JsonCmsStore $cms)
    {
        return view('admin.cv.edit', [
            'settings' => $cms->settings(),
        ]);
    }

    public function update(Request $request, JsonCmsStore $cms)
    {
        $request->validate([
            'cv_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ]);

        $cms->storeCv($request->file('cv_file'));

        return back()->with('status', 'Da doi file CV.');
    }

    public function upload(Request $request, JsonCmsStore $cms)
    {
        $request->validate([
            'cv_file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:51200'],
        ]);

        $settings = $cms->storeCv($request->file('cv_file'));

        return response()->json([
            'url' => $settings['cv_url'],
            'name' => $settings['cv_original_name'],
        ]);
    }
}
