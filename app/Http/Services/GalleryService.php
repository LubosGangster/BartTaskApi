<?php


namespace App\Http\Services;


use App\Models\Gallery;
use Carbon\Carbon;
use DateTime;
use ErrorException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GalleryService
{
    public function addGallery($name){
        try {
            $galleries = $this->allGalleries();
        } catch (\Exception $e){
            $newGallery = new Gallery();
            $newGallery->name = $name;
            $newGallery->path = rawurlencode($name);

            Storage::disk('local')->put("galleries/{$newGallery->path}.json", json_encode($newGallery));
            return $newGallery;
        }

        foreach ($galleries as $gallery){
            if (strcmp($gallery["name"], $name) == 0){
                throw new ErrorException("The gallery already exists");
            }
        }
        $newGallery = new Gallery();
        $newGallery->name = $name;
        $newGallery->path = rawurlencode($name);

        Storage::disk('local')->put("galleries/{$newGallery->path}.json", json_encode($newGallery));
        return $newGallery;
    }

    public function allGalleries(){
        $galleries = array();
        try {
            $path = storage_path('app/galleries');
        } catch (\Exception $e){
            return $e;
        }

        $files = File::allFiles($path);
        foreach ($files as $file){
            $json = json_decode(file_get_contents($file->getRealPath()), true);
            array_push($galleries, $json);
        }

        return $galleries;
    }

    public function findGallery($path){
        $galleries = $this->allGalleries();
        foreach ($galleries as $gallery){
            if (strcmp($gallery["path"], $path) == 0){
                return $gallery;
            }
        }
    }

    public function findImages($path){
        $images = array();
        try {
            $path = storage_path('app/images/'.$path);
        } catch (\Exception $e){
            return $e;
        }

        $files = File::allFiles($path);
        foreach ($files as $file){
            if(strcmp($file->getExtension(), "json") == 0){
                $json = json_decode(file_get_contents($file->getRealPath()), true);
                array_push($images, $json);
            }
        }

        usort($images, function($a, $b) {
            $ad = new DateTime($a['modified']);
            $bd = new DateTime($b['modified']);

            if ($ad == $bd) {
                return 0;
            }

            return $ad < $bd ? 1 : -1;
        });

        return $images;
    }

    public function findImagesNotJson($path){
        $images = array();
        try {
            $path = storage_path('app/images/'.$path);
        } catch (\Exception $e){
            return $e;
        }

        $files = File::allFiles($path);
        foreach ($files as $file){
            if(strcmp($file->getExtension(), "json") != 0){
                array_push($images, $file);
            }
        }

        return $images;
    }

    public function findImagesIndex($path){
        if (Storage::disk('local')->exists('images/'.$path)){
            $images = array();
            $path = storage_path('app/images/'.$path);
            $files = File::allFiles($path);
            foreach ($files as $file){
                if(strcmp($file->getExtension(), "json") == 0){
                    $json = json_decode(file_get_contents($file->getRealPath()), true);
                    array_push($images, $json);
                }
            }

            usort($images, function($a, $b) {
                $ad = new DateTime($a['modified']);
                $bd = new DateTime($b['modified']);

                if ($ad == $bd) {
                    return 0;
                }

                return $ad < $bd ? 1 : -1;
            });
            return $images[0];
        } else {
            return null;
        }

    }

    public function controlPath($path){
        $galleries = $this->allGalleries();
        $temp = false;
        foreach ($galleries as $gallery){
            if(strcmp($path, $gallery["path"]) == 0){
                $temp = true;
                break;
            }
        }

        if (!$temp){
            throw new ErrorException("The gallery not found");
        }
    }

    public function uploadImage($file, $path, $id){
        if (!$file || !$file->isValid()){
            throw new ErrorException("File is not valid");
        }

        $filepath = storage_path('app/images/' . $path);
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'-'.$id.'.'.$file->getClientOriginalExtension();

        $file->move($filepath, $filename);
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        Storage::disk('local')->put("images/${path}/${name}-${id}.json", json_encode([
            'path' => $filename,
            'fullpath' => "${path}/${filename}",
            'name' => $name,
            'modified' => Carbon::now()->addHours(2)
        ]));

        $uploaded = array();
        array_push($uploaded, [
            'path' => $filename,
            'fullpath' => "${path}/${filename}",
            'name' => $name,
            'modified' => Carbon::now()->addHours(2)
        ]);
        return $uploaded;
    }

    public function deleteGallery($path){
        $file = storage_path('app/galleries/'.$path.'.json');
        File::delete($file);
        if (Storage::disk('local')->exists("images/${path}")){
            Storage::disk('local')->deleteDirectory("images/${path}");
        }
    }

    public function deleteImage($gallery, $image){
        $path = storage_path('app/images/'.$gallery);
        if (File::exists($path)) {
            $files = File::allFiles($path);
            $keywords = preg_split("/[.]+/", $image);
            $temp = false;
            //najprv vymaz obrazok
            foreach ($files as $file){
                if(strcmp($file->getFilename(), $image) == 0){
                    $temp = true;
                    File::delete($file);
                    break;
                }
            }
            if ($temp == false){
                throw new ErrorException("Image not found.");
            } else {
                //vymaz json obrazku
                foreach ($files as $file){
                    $name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    if(strcmp($name, $keywords[0]) == 0 && strcmp($file->getExtension(),"json") == 0){
                        File::delete($file);
                        break;
                    }
                }
            }
        } else {
            throw new ErrorException("Image not found.");
        }
    }
}
