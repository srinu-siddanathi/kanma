<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $section = $request->get('section', Banner::SECTION_WEB_HOME);
        $banners = Banner::where('section', $section)
                        ->orderBy('display_order')
                        ->paginate(10);
        
        return view('admin.banners.index', compact('banners', 'section'));
    }

    public function create(Request $request)
    {
        $section = $request->get('section', Banner::SECTION_WEB_HOME);
        return view('admin.banners.create', compact('section'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'button_text' => 'nullable|string|max:50',
            'button_url' => 'nullable|url|max:255',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'section' => 'required|string|in:' . implode(',', array_keys(Banner::getSections())),
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/banners'), $imageName);
            $imageUrl = 'uploads/banners/' . $imageName;
        }

        Banner::create([
            'section' => $request->section,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'image_url' => $imageUrl ?? null,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'is_active' => $request->is_active ?? true,
            'display_order' => $request->display_order ?? 0
        ]);

        return redirect()->route('admin.banners.index', ['section' => $request->section])
            ->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'button_text' => 'nullable|string|max:50',
            'button_url' => 'nullable|url|max:255',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'section' => 'required|string|in:' . implode(',', array_keys(Banner::getSections())),
        ]);

        $data = [
            'section' => $request->section,
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'description' => $request->description,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'is_active' => $request->is_active ?? true,
            'display_order' => $request->display_order ?? 0
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image_url && file_exists(public_path($banner->image_url))) {
                unlink(public_path($banner->image_url));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/banners'), $imageName);
            $data['image_url'] = 'uploads/banners/' . $imageName;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index', ['section' => $request->section])
            ->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_url && file_exists(public_path($banner->image_url))) {
            unlink(public_path($banner->image_url));
        }

        $section = $banner->section;
        $banner->delete();

        return redirect()->route('admin.banners.index', ['section' => $section])
            ->with('success', 'Banner deleted successfully.');
    }
} 