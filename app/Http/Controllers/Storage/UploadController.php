<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;

class UploadController extends Controller
{
	public function uploadFiles()
	{
		$this->validateRequest([
			'files' => 'array|min:1',
			'files.*' => 'max:' . config('app.max_file_size')
		]);

		$filePaths = Helpers::uploadFiles(false);
		return $this->responseResult($filePaths, count($filePaths) ? true : false);
	}

	public function uploadImages()
	{
		$this->validateRequest([
			'files' => 'array|min:1',
			'files.*' => 'mimes:jpeg,jpg,png|max:' . config('app.max_file_size')
		]);

		$imagePaths = Helpers::uploadImages(false);
		return $this->responseResult($imagePaths, count($imagePaths) ? true : false);
	}

	public function uploadBase64Files()
	{
		$validated = $this->validateRequest([
			'files' => 'array|min:1',
			'files.*' => 'max:' . (config('app.max_file_size') * 1048576)
		]);

		$filePaths = Helpers::uploadBase64Files($validated['files'], false);
		return $this->responseResult($filePaths, count($filePaths) ? true : false);
	}

	public function uploadBase64Images()
	{
		$validated = $this->validateRequest([
			'files' => 'array|min:1',
			'files.*' => 'max:' . (config('app.max_file_size') * 1048576)
		]);

		$imagePaths = Helpers::uploadBase64Images($validated['files'], false);
		return $this->responseResult($imagePaths, count($imagePaths) ? true : false);
	}
}
