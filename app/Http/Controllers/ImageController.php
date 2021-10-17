<?php


namespace App\Http\Controllers;


use App\Http\Services\GalleryService;
use Illuminate\Support\Facades\Response;
use Intervention\Image\Facades\Image;
use Symfony\Component\ErrorHandler\Error\FatalError;


class ImageController extends Controller
{
    private static $galleryService;
    public function __construct()
    {
        self::$galleryService = new GalleryService();
    }

    public function getImage($w, $h, $gallery, $image){
        try {
            self::$galleryService->controlPath($gallery);
        } catch (\ErrorException $e) {
            return response()->json([
                "error" => [
                    "message" => $e->getMessage()
                ]
            ], 404);
        }
        try {
            $images = self::$galleryService->findImagesNotJson($gallery);
        } catch (\Exception $e) {
            return response()->json([
                "error" => [
                    "message" => "Gallery has not images."
                ]
            ], 404);
        }

        if (ctype_digit($w)){
            $w=intval($w);
            if ($w<0 ||$w>9000){
                return response()->json([
                    "error" => [
                        "message" => "The photo preview can't be generated."
                    ]
                ], 500);
            }
        } else {
            return response()->json([
                "error" => [
                    "message" => "The photo preview can't be generated."
                ]
            ], 500);
        }

        if (ctype_digit($h)){
            $h=intval($h);
            if ($h<0 ||$h>9000){
                return response()->json([
                    "error" => [
                        "message" => "The photo preview can't be generated."
                    ]
                ], 500);
            }
        } else {
            return response()->json([
                "error" => [
                    "message" => "The photo preview can't be generated."
                ]
            ], 500);
        }

        foreach ($images as $img){
            if (strcmp($img->getFileName(), $image) == 0){
                $imgIntervention = Image::make(storage_path("app/images/${gallery}/${image}"));

                try {
                    if ($w == 0 && $h != 0) {
                        $imgIntervention->resize(null, $h, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    } elseif ($w != 0 && $h == 0){
                        $imgIntervention->resize($w, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    } elseif ($w != 0 && $h != 0){
                        $imgIntervention->resize($w, $h);
                    }
                } catch (FatalError $e){
                    return response()->json([
                        "error" => [
                            "message" => "The photo preview can't be generated."
                        ]
                    ], 500);
                }
                return Response::make($imgIntervention->encode('jpg'), 200, ['Content-Type' => 'image/jpeg']);
            }
        }

        return response()->json([
            "error" => [
                "message" => "Image not found."
            ]
        ], 404);
    }
}
