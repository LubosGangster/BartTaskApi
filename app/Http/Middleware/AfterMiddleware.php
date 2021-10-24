<?php


namespace App\Http\Middleware;


use Closure;

class AfterMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $data = $response->getData();
        foreach ($data->galleries as $key => $gallery){
            if (property_exists($gallery, 'image')){
                $extension = explode('.', $gallery->image->path);
                $path = explode('-', $gallery->image->path);
                $gallery->image->path = $path[0].'.'.$extension[1];

                $extension = explode('.', $gallery->image->fullpath);
                $path = explode('-', $gallery->image->fullpath);
                $gallery->image->fullpath = $path[0].'.'.$extension[1];
            }
        }

        $response->setData($data);
        return $response;
    }
}
