<?php


namespace App\Traits;

use App\Pixel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait PixelTrait
{
    /**
     * Store a new pixel
     *
     * @param Request $request
     * @return Pixel
     */
    protected function pixelCreate(Request $request)
    {
        $user = Auth::user();

        $pixel = new Pixel;

        $pixel->name = $request->input('name');
        $pixel->user_id = $user->id;
        $pixel->type = $request->input('type');
        $pixel->pixel_id = $request->input('pixel_id');
        $pixel->save();

        return $pixel;
    }

    /**
     * Update the pixel
     *
     * @param Request $request
     * @param Model $pixel
     * @return Pixel|Model
     */
    protected function pixelUpdate(Request $request, Model $pixel)
    {
        if ($request->has('name')) {
            $pixel->name = $request->input('name');
        }

        if ($request->has('type')) {
            $pixel->type = $request->input('type');
        }

        if ($request->has('pixel_id')) {
            $pixel->pixel_id = $request->input('pixel_id');
        }

        $pixel->save();

        return $pixel;
    }
}