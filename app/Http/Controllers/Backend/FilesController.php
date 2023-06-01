<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Backend\File;
use App\Helpers\HCache;
use Exception;
use Throwable;
use Cache;

class FilesController extends Controller
{
    // File types and respective Font Awesome icons
    protected const FONTAWESOME_ICON_EXTENSIONS_MAP = [
        'file-lines'        => ['txt','rtf'],
        'file-zipper'       => ['zip','rar','7z','s7z','tar','gz'],
        'file-csv'          => ['csv'],
        'file-pdf'          => ['pdf'],
        'file-excel'        => ['xlsx','xlsm','xlsb','xls','ods'],
        'file-word'         => ['doc','docm','docx','odt'],
        'file-powerpoint'   => ['pptx','pptm','ppt','ppsx','ppsm','odp'],
        'file-image'        => ['jpg','jpeg','png','tif','tiff','gif','bmp','webp','svg'],
        'file-video'        => ['mp4','mkv','avi','wmv','vob','3gp','3g2'],
        'file-audio'        => ['mp3','midi','midi','mid','ogg','aac'],
        'file-waveform'     => ['wav','wave'],
        'file-code'         => ['html','css','css','js','php'],
    ];

    public static function getImageUrlById(string $id, $w = null, $h = null, $format = null, $quality = null): string | null {
        try {
            $file = self::getFileById($id);
            if(empty($file)) {
                throw new Exception('File not found');
            }

            $param = ['name' => getField($file, 'name')];

            if(!empty($w))
                $param['w'] = $w;

            if(!empty($h))
                $param['h'] = $h;

            if(!empty($format))
                $param['format'] = $format;

            if(!empty($quality))
                $param['quality'] = $quality;


            return route('download.image', $param);
        } catch (Exception|Throwable $e) {
            logError("Error getting image URL (by id '".$id."'): ".$e->getMessage());
            return null;
        }
    }

    public static function getImageUrlByName(string $name, $w = null, $h = null, $format = null, $quality = null): string | null {
        try {
            $file = self::getFileByName($name);

            if(empty($file)) {
                throw new Exception('File not found');
            }

            return self::getImageUrlById(getField($file, 'id'), $w, $h, $format, $quality);
        } catch (Exception|Throwable $e) {
            logError("Error getting image URL (by name '".$name."'): ".$e->getMessage());
            return null;
        }
    }

    public static function getFileUrlById(string $id): string | null {
        try {
            $file = self::getFileById($id);

            if(empty($file)) {
                throw new Exception('File not found');
            }

            return route('download.file', ['name' => getField($file, 'name')]);
        } catch (Exception|Throwable $e) {
            logError("Error getting file URL (by id '".$id."'): ".$e->getMessage());
            return null;
        }
    }

    public static function getFileUrlByName(string $name): string | null {
        try {
            $file = self::getFileByName($name);
            if(empty($file)) {
                throw new Exception('File not found');
            }

            return route('download.file', ['name' => getField($file, 'name')]);
        } catch (Exception|Throwable $e) {
            logError("Error getting file URL (by name '".$name."'): ".$e->getMessage());
            return null;
        }
    }

    public static function getFileByName($name): File | null {
        return HCache::remember('file', "_NAME_".$name, function() use ($name) {
            return File::whereName($name)->first();
        });
    }

    public static function getFileSizeByName(string $name): string | null {
        try {
            $file = self::getFileByName($name);
            if(empty($file)) {
                throw new Exception('File not found');
            }
            return $file->size;
        } catch (Exception|Throwable $e) {
            logError("Error getting file URL (by name '".$name."'): ".$e->getMessage());
            return null;
        }
    }

    public static function getFileById($id): File | null {
        return HCache::remember('file', "_ID_".$id, function() use ($id) {
            return File::whereId($id)->first();
        });
    }

    public function downloadFile(Request $request, string $name)
    {
        try {
            $file = self::getFileByName($name);

            if (!isset($file)) {
                throw new Exception("File not found in database: ".$name);
            }

            if (! Storage::disk(env('STORAGE_SYSTEM', 'local'))->exists($name)) {
                throw new Exception("File not found in storage: ".$name);
            }

            return Storage::disk(env('STORAGE_SYSTEM', 'local'))->download($name, $file->original);
        } catch (Exception|Throwable $e) {
            logError("Error downloading file: ".$e->getMessage());
            return response()->json('File not found', 404);
        }
    }

        /**
     * @param string $filename
     * @param int|null $w
     * @param int|null $h
     * @param null $format
     * @param null $quality
     * @return array|Exception
     */
    public function downloadImage(Request $request, string $name)
    {
        $imageTypes = ['png','jpeg','jpg','gif', 'webp'];

        try {
            // logDebug("File: ".$name);
            $file = self::getFileByName($name);

            if (!isset($file)) {
                throw new Exception("File not found in database: ".$name);
            }

            $w = $request->input('w');
            $h = $request->input('h');
            $format = $request->input('format', 'webp');
            $quality = $request->input('quality');

            if($format != null && !in_array($format, $imageTypes)) {
                throw new Exception('Invalid format to convert: '.$name." | ".implode(', ', $imageTypes));
            }

            if(empty($w) && empty($h) && empty($format)) {
                if(!Storage::disk(env('STORAGE_SYSTEM', 'local'))->exists($name)) {
                    throw new Exception("File not found in storage (empy w, h and format): ".$name);
                }

                return Storage::disk(env('STORAGE_SYSTEM', 'local'))->download($name);
            }

            // Get from cache
            $cache = "cache/".$name."_".md5($w."_".$h."_".($format ?? ''));
            // logDebug("Convert: ".$w."x".$h." - ".($format ?? ''));
            // logDebug("Getting: ".$cache);

            if(!Storage::disk(env('STORAGE_SYSTEM', 'local'))->exists($cache)) {
                logDebug("Not in cache: ".$cache);

                /*******************/
                /** Convert image **/

                $img = \Image::make(Storage::disk(env('STORAGE_SYSTEM', 'local'))->get($name));

                // Resize & format
                if($w > 0 || $h > 0) {
                    $img->resize($w, $h, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // Format
                if(!empty($format)) {
                    $img->encode($format, $quality);
                }

                // Cache upload
                if(!self::uploadFile($cache, (string)$img->encode())) {
                    logError("Error uploading converted image: ".$cache);
                    return Storage::disk(env('STORAGE_SYSTEM', 'local'))->download($name);
                }
            }

            return Storage::disk(env('STORAGE_SYSTEM', 'local'))->download($cache);
        } catch (Exception|Throwable $e) {
            logError("Error downloading image: ".$e->getMessage());
            return response()->json('File not found', 404);
        }
    }

    private static function uploadFile($filename, $file): bool {
        try {
            if(!Storage::disk(env('STORAGE_SYSTEM', 'local'))->put($filename, $file)) {
                throw new Exception("Unable to upload file: ".$filename);
            }

            return true;
        } catch (Exception|Throwable $e) {
            logError("Error uploading file: ".$e->getMessage());
        }

        return false;
    }

    public static function store(UploadedFile $file) {
        try {
            $timestamp = Carbon::now()->timestamp;
            $name      = Str::random(20);
            $filename  = "{$timestamp}_{$name}";

            if(!self::uploadFile($filename, file_get_contents($file->path()))) {
                return null;
            }

            return File::create([
                'name'       => $filename,
                'original'   => $file->getClientOriginalName() ?? $filename,
                'type'       => $file->getClientMimeType(),
                'size'       => $file->getSize(),
                'created_by' => Auth::id() ?? 0,
                'updated_by' => Auth::id() ?? 0,
            ]);

        } catch (Exception|Throwable $e) {
            logError("Error storing file: ".$e->getMessage());
            return null;
        }
    }

    // Get Font Awesome icon depending on the file type
    public static function iconClassFromFileName(string $name) : string
    {
        $nameParts = explode('.', $name);
        $extension = strtolower(trim(end($nameParts) ?? '', ' '));

        foreach (self::FONTAWESOME_ICON_EXTENSIONS_MAP as $iconClass => $iconExtensions){
            if( in_array($extension, $iconExtensions) ){
                $class = $iconClass;
                break;
            }
        }

        return !empty($class) ? $class : 'file';
    }

    // public static function upload(Request $request)
    // {
    //     try {
    //         $result = self::put($request->file('file'), $request->input('public', true));

    //         if ($result instanceof Exception) {
    //             throw $result;
    //         }

    //         return response()->json(['file' => $result], 200);
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::upload]: {$e->getMessage()}");
    //         return response()->json('Error uploading file', 500);
    //     }
    // }

    // /**
    //  * @param UploadedFile $file
    //  * @param bool $public
    //  *
    //  * @return string|Exception
    //  */
    // public static function put(UploadedFile $file, bool $public = true)
    // {
    //     try {
    //         $timestamp = Carbon::now()->timestamp;
    //         $name      = Str::random(20);
    //         $filename  = "{$timestamp}_{$name}";

    //         $result = self::storeInCloud(file_get_contents($file->path()), $filename);

    //         if ($result instanceof Exception) {
    //             $result = self::storeInDisk(file_get_contents($file->path()), $filename);
    //         }

    //         if ($result instanceof Exception) {
    //             throw $result;
    //         }

    //         $result = File::create([
    //             'name'       => $filename,
    //             'original'   => $file->getClientOriginalName() ?? $name,
    //             'type'       => $file->getClientMimeType(),
    //             'size'       => $file->getSize(),
    //             'public'     => $public,
    //             'created_by' => Auth::id() ?? 0,
    //             'updated_by' => Auth::id() ?? 0,
    //         ]);

    //         return $result->name;
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::put]: {$e->getMessage()}");
    //         return $e;
    //     }
    // }

    // /**
    //  * @param string $filepath
    //  * @param string $filename
    //  *
    //  * @return bool|Exception
    //  */
    // private static function storeInCloud(string $file, string $filename)
    // {
    //     try {
    //         return Storage::cloud()->put($filename, $file);
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::storeInCloud]: {$e->getMessage()}");
    //         return $e;
    //     }
    // }

    // /**
    //  * @param string $filename
    //  * @param File $file
    //  *
    //  * @return StreamedResponse|Exception
    //  */
    // private static function downloadFromCloud(string $filename, File $file)
    // {
    //     try {
    //         if (! Storage::cloud()->exists($filename)) {
    //             throw new Exception('File not found');
    //         }

    //         return Storage::cloud()->download($filename, $file->original);
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::downloadFromCloud]: {$e->getMessage()}");
    //         return $e;
    //     }
    // }

    // /**
    //  * @param string $filename
    //  * @param File $file
    //  *
    //  * @return StreamedResponse|Exception
    //  */
    // private static function downloadFromDisk(string $filename, File $file)
    // {
    //     try {
    //         if (! Storage::exists($filename)) {
    //             throw new Exception('File not found');
    //         }

    //         return Storage::download($filename, $file->original);
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::downloadFromDisk]: {$e->getMessage()}");
    //         return $e;
    //     }
    // }

    // /**
    //  * @param string $filename
    //  *
    //  * @return array|Exception
    //  */
    // public static function get(string $filename)
    // {
    //     try {
    //         $file = File::whereName($filename)->first();

    //         if (! isset($file)) {
    //             throw new Exception('File not found');
    //         }

    //         $result = self::getUrlFromCloud($filename);

    //         if ($result instanceof Exception) {
    //             $result = self::getUrlFromDisk($filename);
    //         }

    //         if ($result instanceof Exception) {
    //             throw new Exception('File not found');
    //         }

    //         return [
    //             'file' => $file,
    //             'url'  => $result,
    //         ];
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::get]: {$e->getMessage()}");
    //         return $e;
    //     }
    // }


    // /**
    //  * @param string $filename
    //  *
    //  * @return string|Exception
    //  */
    // private static function getUrlFromCloud(string $filename)
    // {
    //     try {
    //         if (! Storage::cloud()->exists($filename)) {
    //             throw new Exception('File not found');
    //         }

    //         return Storage::cloud()->temporaryUrl($filename, Carbon::now()->addMinutes(10080));
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::getUrlFromCloud]: {$e->getMessage()}");
    //         return $e;
    //     }
    // }

    // /**
    //  * @param string $filename
    //  *
    //  * @return string|Exception
    //  */
    // private static function getUrlFromDisk(string $filename)
    // {
    //     try {
    //         if (! Storage::exists($filename)) {
    //             throw new Exception('File not found');
    //         }

    //         return Storage::url($filename);
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::getUrlFromDisk]: {$e->getMessage()}");
    //         return $e;
    //     }
    // }

    // /**
    //  * @param string $filename
    //  *
    //  * @return JsonResponse
    //  */
    // public static function destroy(string $filename)
    // {
    //     try {
    //         $result = self::delete($filename);

    //         if ($result instanceof Exception) {
    //             throw $result;
    //         }

    //         return response()->json([], 204);
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::destroy]: {$e->getMessage()}");
    //         return response()->json('Error deleting file', 500);
    //     }
    // }

    // /**
    //  * @param string $filename
    //  *
    //  * @return bool|Exception
    //  */
    // public static function delete(string $filename)
    // {
    //     try {
    //         $file = File::whereName($filename)->first();

    //         if (! isset($file)) {
    //             throw new Exception('File not found');
    //         }

    //         $file->deleted_by = Auth::id() ?? 0;
    //         $file->delete();
    //         $file->save();

    //         return true;
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::delete]: {$e->getMessage()}");
    //         return $e;
    //     }
    // }

    // /**
    //  * @param string $filename
    //  *
    //  * @return JsonResponse
    //  */
    // public static function recover(string $filename)
    // {
    //     try {
    //         $result = self::restore($filename);

    //         if ($result instanceof Exception) {
    //             throw $result;
    //         }

    //         return response()->json([], 204);
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::recover]: {$e->getMessage()}");
    //         return response()->json('Error recovering file', 500);
    //     }
    // }

    // /**
    //  * @param string $filename
    //  *
    //  * @return bool|Exception
    //  */
    // public static function restore(string $filename)
    // {
    //     try {
    //         $file = File::whereName($filename)->withTrashed()->first();

    //         if (! isset($file)) {
    //             throw new Exception('File not found');
    //         }

    //         $file->deleted_by = null;
    //         $file->restore();
    //         $file->save();

    //         return true;
    //     } catch (Exception|Throwable $e) {
    //         logError("[FilesController::restore]: {$e->getMessage()}");
    //         return $e;
    //     }
    // }
}
