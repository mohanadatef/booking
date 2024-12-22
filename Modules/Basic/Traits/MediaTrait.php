<?php

namespace Modules\Basic\Traits;


trait MediaTrait
{
    /**
     * This function moves a file to a specified directory and renames it.
     *
     * param file This parameter is a file object that represents the file that needs to be moved to a
     * new location.
     * param file_name The name of the directory where the file will be moved to.
     * param imageName The name of the image file that needs to be moved to the specified path.
     * param path The path where the file will be moved to.
     */
    public function media_move($file, $file_name, $imageName, $path)
    {
        if (!\File::isDirectory(public_path($path . '/' . $file_name))) {
            \File::makeDirectory(public_path($path . '/' . $file_name), 0777, true, true);
        }
        $file->move(public_path($path . '/' . $file_name), $imageName);
    }

    /**
     * This PHP function uploads media files to a server based on the provided data, request, file
     * name, path, and type.
     *
     * param data This parameter is likely an array or object containing additional data related to
     * the media being uploaded, such as metadata or information about the user uploading the media.
     * param request This is an object that contains the request data sent to the server. It could
     * contain various data such as form data, query parameters, and uploaded files.
     * param fileNameServer The name of the file on the server where the uploaded media will be
     * stored.
     * param path The path where the uploaded media file will be stored.
     * param type The type of media being uploaded (e.g. "image", "video", "audio").
     */
    public function media_upload($data, $request, $fileNameServer, $path, $type)
    {
        if (isset($request->$type) && !empty($request->$type)) {
            if (is_array($request->$type)) {
                foreach ($request->$type as $media) {
                    $this->upload($media, $data, $fileNameServer, $path, $type);
                }
            } else {
                $this->upload($request->$type, $data, $fileNameServer, $path, $type);
            }
        }
    }

    /**
     * This PHP function uploads a media file to a specified path with a given file name and type.
     *
     * param media It is likely an instance of the uploaded file that is being passed to the function.
     * param data It is an object that represents the model instance to which the media file will be
     * attached. The method `create()` is called on this object to create a new media file record in
     * the database.
     * param fileNameServer The parameter  is not used in the given code snippet. It is
     * not clear what it represents or what its purpose is.
     * param path The path parameter is a string that represents the directory path where the uploaded
     * file will be stored. It could be an absolute path or a relative path from the root directory of
     * the application.
     * param type The "type" parameter is a string that represents the type of the file being
     * uploaded. It could be an image, video, audio, document, or any other type of file. This
     * parameter is used to categorize the uploaded files and handle them accordingly in the
     * application.
     */
    public function upload($media, $data, $fileNameServer, $path, $type)
    {
        $fileName = time() . $media->getClientOriginalname();
        $file = $data->media()->create(['file' => $fileName, 'type' => $type]);
        !$file->file ?: $this->media_move($media, $fileNameServer, $fileName, $path);
    }

    /**
     * This function checks if a specific type of media exists in the request and deletes it if it
     * exists in the data.
     *
     * param data The data parameter is likely an object or an array containing information about a
     * media file, such as its ID, file path, or other metadata.
     * param request The  parameter is likely an instance of the Illuminate\Http\Request
     * class, which represents an HTTP request made to the application. It contains information about
     * the request, such as the HTTP method, URL, headers, and any data sent in the request body.
     * param type The type of media being checked for deletion. It could be "image", "video", or any
     * other type of media.
     */
    public function checkMediaDelete($data, $request, $type)
    {
        if (isset($request->$type) && !empty($request->$type)) {
            if ($data->$type) {
                $data->$type->delete();
            }
        }
    }

    /**
     * The function renames and moves an uploaded image to a specified folder in the public directory
     * and returns the file path.
     *
     * param folder The folder parameter is a string that specifies the directory where the uploaded
     * image will be stored. By default, it is set to the root directory '/'.
     * param file The file parameter is the image file that needs to be uploaded.
     *
     * return the path of the uploaded image file. The path includes the folder name, file name, and
     * extension.
     */
    function settingImage($folder = '/', $file)
    {
        $extension = $file->getClientOriginalExtension(); // getting image extension
        $fileName = time() . '' . rand(11111, 99999) . '.' . $extension; // renameing image
        $dest = public_path('/uploads' . '/' . $folder);
        if (!\File::isDirectory(public_path($dest . '/' . $fileName))) {
            \File::makeDirectory(public_path($dest . '/' . $fileName), 0777, true, true);
        }

        $file->move($dest, $fileName);
        return '/public/uploads' . '/' . $folder . '/' . $fileName;
    }


}
