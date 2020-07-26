<?php

namespace App\Http\Controllers;

use App\Enum\Status;
use App\Services\DB;
use App\Traits\CustomAjaxResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use CustomAjaxResponse;

    private $_DB;
    private $_TABLE;

    public function __construct()
    {
        $this->_DB = new DB($this->_TABLE = 'images.json');
        $this->_TABLE = $this->_DB . '/' . $this->_TABLE;
    }

    public function index()
    {
        $images = $this->_DB->all('desc');
        return view('welcome')->with(compact('images'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:5',
            'image' => 'required|image|mimes:png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json($this->customErrorResponse($validator->errors()));
        }

        $image = $this->uploadImage($request);

        $image = $this->_DB->insert([
            'title' => $request->input('title'),
            'image' => $image
        ]);

        return $this->customResponse(Status::$_SUCCESS, 'Image successfully uploaded.', reset($image));
    }

    public function destroy(Request $request, $id)
    {
        $image = $this->_DB->get($id);

        if ($this->_DB->delete($id)) {
            if (!empty($image)) {
                $this->deleteImage($image);
            }
            return $this->customResponse(Status::$_SUCCESS, 'Image successfully deleted.');
        }
        return $this->customErrorResponse('Image could not be deleted.');
    }

    private function uploadImage(Request $request)
    {
        $url = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image_name = time() . '.' . $image->getClientOriginalExtension();
            Storage::disk('public_uploads')->putFileAs('images', $image, $image_name);
            $exists = Storage::disk('public_uploads')->exists('images/' . $image_name);

            if ($exists) {
                $url = Storage::disk('public_uploads')->url('images/' . $image_name);
            }
        }

        return $url;
    }

    private function deleteImage(array $image)
    {
        $image_name = str_replace(config('app.url') . '/uploads/', '', $image['image']);
        $exists = Storage::disk('public_uploads')->exists($image_name);
        if ($exists) {
            Storage::disk('public_uploads')->delete($image_name);
            return true;
        }
        return false;
    }
}
