<?php

namespace App\Http\Controllers\Storage;

use App\Enums\FileDirEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
	public function downloadFile()
	{
		$filePath = request('filePath');
		if (Storage::disk(FileDirEnum::PrivateAttachmentsDir)->exists($filePath)) {
			return response()->download(Storage::disk(FileDirEnum::PrivateAttachmentsDir)->path($filePath));
		} else {
			return $this->responseResult(null, false);
		}
	}

	public function downloadImage()
	{
		$filePath = request('filePath');
		if (Storage::disk(FileDirEnum::PrivateImagesDir)->exists($filePath)) {
			return response()->download(Storage::disk(FileDirEnum::PrivateImagesDir)->path($filePath));
		} else {
			return $this->responseResult(null, false);
		}
	}

	public function downloadBase64Image()
	{
		$filePath = request('filePath');
		if (Storage::disk(FileDirEnum::PrivateImagesDir)->exists($filePath)) {
			$path = Storage::disk(FileDirEnum::PrivateImagesDir)->path($filePath);
			$type = pathinfo($path, PATHINFO_EXTENSION);
			$data = file_get_contents($path);
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
			return $this->responseResult($base64);
		} else {
			return $this->responseResult(null, false);
		}
	}
}
