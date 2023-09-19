<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Str;
use App\Handlers\ImageUploadHandler;
use App\Http\Resources\ImageResource;
use App\Http\Requests\Api\ImageRequest;

class ImagesController extends Controller
{
    public function store(ImageRequest $request, ImageUploadHandler $uploader, Image $image)
    {
        $result = $uploader->save($request->image, Str::plural($request->type), $request->serial_number, 1024);

        $image->path = $result['path'];
        $image->type = $request->type;
        $image->serial_number = $request->serial_number;
        $image->save();

        return new ImageResource($image);
    }
}
