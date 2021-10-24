<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryStoreRequest;
use App\Http\Requests\UploadImageRequest;
use App\Http\Services\GalleryService;
use App\Models\GalleryTitleImage;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\File;

class GalleryController extends Controller
{
    private static $galleryStoreRules;
    private static $uploadImageRules;
    private static $galleryService;
    private static $client;
    public function __construct()
    {
        self::$galleryStoreRules = new GalleryStoreRequest();
        self::$uploadImageRules = new UploadImageRequest();
        self::$galleryService = new GalleryService();
        self::$client = new Client([ 'base_uri' => 'https://graph.facebook.com' ]);
    }


    public function index(Request $request){
        try {
            $galleries = self::$galleryService->allGalleries();
        } catch (\Exception $e){
            return response()->json([
                'count' => 0,
                'galleries' => []
            ], 200);
        }

        foreach ($galleries as $index=>$gallery){
            $arr = self::$galleryService->findImagesIndex($gallery["path"]);
            if ($arr != null){
                $temp = new GalleryTitleImage();
                $temp->path = $galleries[$index]["path"];
                $temp->image = $arr;
                $temp->name = $galleries[$index]["name"];
                $galleries[$index] = $temp;
            }
        }

        //filter paginate
        if (ctype_digit($request->get('limit')) && $request->get('limit') != null && $request->get('limit') < count($galleries) && $request->get('limit')>0){
            $allPage = (int)ceil(count($galleries)/$request->get('limit'));
            if (ctype_digit($request->get('page')) &&$request->get('page') != null && $request->get('page') <= $allPage && $request->get('page') > 0){
                $filterGalleries = array();
                $downBorder = $request->get('limit')*($request->get('page') - 1);
                $upBorder = $downBorder + $request->get('limit') - 1;
                foreach ($galleries as $index=>$gallery){
                    if ($index >= $downBorder && $index <= $upBorder){
                        array_push($filterGalleries, $gallery);
                    }
                }
                return response()->json([
                    'total_count' => count($galleries),
                    'count' => count($filterGalleries),
                    'limit' => (int)$request->get('limit'),
                    'page' => (int)$request->get('page'),
                    'galleries' => $filterGalleries
                ], 200);
            } else {
                //default 1
                $filterGalleries = array();
                foreach ($galleries as $index=>$gallery){
                    if ($index<$request->get('limit')){
                        array_push($filterGalleries, $gallery);
                    }
                }
                return response()->json([
                    'total_count' => count($galleries),
                    'count' => count($filterGalleries),
                    'limit' => (int)$request->get('limit'),
                    'page' => 1,
                    'galleries' => $filterGalleries
                ], 200);
            }
        }

        return response()->json([
            'total_count' => count($galleries),
            'galleries' => $galleries
        ], 200);
    }

    public function update(Request $request, $path){
        $id = $request->get('idOfUser');
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
            $uploaded = self::$galleryService->uploadImage($request->file('image'), $path, $id);
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

        return response()->json($newGallery, 201);
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
        return response()->json([
            "message" => "Gallery was deleted"
        ], 200);
    }

    public function deleteImage($gallery, $image){
        try {
            self::$galleryService->deleteImage($gallery, $image);
            return response()->json([
                "message" => "Image was deleted"
            ], 200);
        } catch (\ErrorException $e) {
            return response()->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 404);
        }
    }
}
