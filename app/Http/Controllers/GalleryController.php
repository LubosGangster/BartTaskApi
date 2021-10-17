<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryStoreRequest;
use App\Http\Requests\UploadImageRequest;
use App\Http\Services\GalleryService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GalleryController extends Controller
{
    private static $galleryStoreRules;
    private static $uploadImageRules;
    private static $galleryService;
    public function __construct()
    {
        self::$galleryStoreRules = new GalleryStoreRequest();
        self::$uploadImageRules = new UploadImageRequest();
        self::$galleryService = new GalleryService();
    }

    public function index(){
        try {
            $galleries = self::$galleryService->allGalleries();
        } catch (\Exception $e){
            return response()->json("Gallery list is empty.", 200);
        }

        foreach ($galleries as $index=>$gallery){
            $arr = self::$galleryService->findImagesIndex($gallery["path"]);
            if ($arr != null){
                $galleries[$index]["image"] = $arr;
            }
        }

        return response()->json([
            'count' => count($galleries),
            'galleries' => $galleries
        ], 200);
    }

    public function update(Request $request, $path){
        try {
            self::$galleryService->controlPath($path);
        } catch (\ErrorException $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 404);
        }

        try {
            $this->validate($request, self::$uploadImageRules->rules());
        } catch (ValidationException $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }

        try {
            $uploaded = self::$galleryService->uploadImage($request->file('image'), $path);
        } catch (\ErrorException $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }

        return response()->json([
            'uploaded' => $uploaded
        ], 200);
    }

    public function store(Request $request){
        try {
            if (count($request->all()) > 1){
                return response()->json([
                    'error' => [
                        'message' => 'Additional properties are not allowed.'
                    ]
                ], 400);
            }
            $this->validate($request, self::$galleryStoreRules->rules());
        } catch (ValidationException $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }

        try {
            $newGallery = self::$galleryService->addGallery($request->input('name'));
        } catch (\ErrorException $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 409);
        }

        return response()->json($newGallery, 200);
    }

    public function show($path){
        try {
            self::$galleryService->controlPath($path);
        } catch (\ErrorException $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 404);
        }

        $gallery = self::$galleryService->findGallery($path);
        try {
            $images = self::$galleryService->findImages($path);
        } catch (\Exception $e){
            return response()->json([
                'gallery' => $gallery,
                'count_images' => 0
            ], 200);
        }

        return response()->json([
            'gallery' => $gallery,
            'count_images' => count($images),
            'images' => $images
        ], 200);
    }

    public function delete($path){
        try {
            self::$galleryService->controlPath($path);
        } catch (\ErrorException $e){
            return response()->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 404);
        }

        self::$galleryService->deleteGallery($path);
        return response()->json("Gallery was deleted", 200);
    }

    public function deleteImage($gallery, $image){
        try {
            self::$galleryService->deleteImage($gallery, $image);
            return response()->json("Image was deleted", 200);
        } catch (\ErrorException $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 404);
        }
    }
}
