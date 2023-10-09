<?php

namespace App\Http\Controllers;

use App\Pixel;
use App\Http\Requests\CreatePixelRequest;
use App\Http\Requests\UpdatePixelRequest;
use App\Traits\PixelTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PixelsController extends Controller
{
    use PixelTrait;

    public function index(Request $request)
    {
        $user = Auth::user();

        $search = $request->input('search');
        $type = $request->input('type');
        $sort = ($request->input('sort') == 'asc' ? 'asc' : 'desc');

        $pixels = Pixel::where('user_id', $user->id)
            ->when($search, function($query) use ($search) {
                return $query->searchName($search);
            })->when($type, function($query) use ($type) {
                return $query->searchType($type);
            })
            ->orderBy('id', $sort)
            ->paginate(config('settings.paginate'))
            ->appends(['search' => $search, 'sort' => $sort]);

        return view('pixels.content', ['view' => 'list', 'pixels' => $pixels]);
    }

    public function pixelsNew()
    {
        return view('pixels.content', ['view' => 'new']);
    }

    public function pixelsEdit($id)
    {
        $user = Auth::user();

        $pixel = Pixel::where([['id', '=', $id], ['user_id', '=', $user->id]])->firstOrFail();

        return view('pixels.content', ['view' => 'edit', 'pixel' => $pixel]);
    }

    public function createPixel(CreatePixelRequest $request)
    {
        $this->pixelCreate($request);

        return redirect()->route('pixels')->with('success', __(':name has been created.', ['name' => str_replace(['http://', 'https://'], '', $request->input('name'))]));
    }

    public function updatePixel(UpdatePixelRequest $request, $id)
    {
        $user = Auth::user();

        $pixel = Pixel::where([['id', '=', $id], ['user_id', '=', $user->id]])->firstOrFail();

        $this->pixelUpdate($request, $pixel);

        return back()->with('success', __('Settings saved.'));
    }

    public function deletePixel($id)
    {
        $user = Auth::user();

        $pixel = Pixel::where([['id', '=', $id], ['user_id', '=', $user->id]])->firstOrFail();

        $pixel->delete();

        return redirect()->route('pixels')->with('success', __(':name has been deleted.', ['name' => str_replace(['http://', 'https://'], '', $pixel->name)]));
    }
}
