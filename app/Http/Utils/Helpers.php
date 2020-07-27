<?php

namespace App\Http\Utils;

use App\Enums\FileDirEnum;
use App\Models\Account;
use App\Models\AccountSession;
use Exception;
use Firebase\JWT\JWT;
use Gumlet\ImageResize;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Helpers
{
	public static function getRandomString($length = 16, $uppercase = false)
	{
		$characters = array(
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',

			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
			'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T',
			'U', 'V', 'W', 'X', 'Y', 'Z',

			'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j',
			'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
			'u', 'v', 'w', 'x', 'y', 'z'
		);

		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString = $randomString . $characters[rand(0, count($characters) - 1)];
		}

		return $uppercase ? strtoupper($randomString) : $randomString;
	}

	public static function getRandomStringNumber($length = 8)
	{
		$characters = array(
			'0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
		);

		$randomStringNumber = '';
		for ($i = 0; $i < $length; $i++) {
			$randomStringNumber = $randomStringNumber . $characters[rand(0, count($characters) - 1)];
		}

		return $randomStringNumber;
	}

	public static function getJWT($accountId, $accountSessionId)
	{
		$key = config('app.guid');
		$sessionKey = hash('sha256', $key . $accountSessionId) . $accountSessionId;

		$account = Account::find($accountId);

		$payload = new \stdClass();
		$payload->accountId = $account->id;
		$payload->username = $account->username;
		$payload->sessionKey = $sessionKey;

		return JWT::encode($payload, $key);
	}

	public static function getAccountSession($jwt)
	{
		$key = config('app.guid');

		$decoded = null;
		try {
			$decoded = JWT::decode($jwt, $key, array('HS256'));
		} catch (Exception $e) {
		}

		if ($decoded) {
			$accountSessionId = substr($decoded->sessionKey, 64);
			$recheckSessionKey = hash('sha256', $key . $accountSessionId) . $accountSessionId;

			if ($recheckSessionKey === $decoded->sessionKey) {
				return AccountSession::find($accountSessionId);
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	public static function getAccountFromJWT()
	{
		$jwt = request()->header('jwt');
		if ($jwt) {
			$accountSession = Helpers::getAccountSession($jwt);

			if ($accountSession) {
				$account = Account::find($accountSession->account_id);

				return $account;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	public static function saveImage($file, $public = true, $type = 'jpg')
	{
		if ($public) {
			$imagesDir = public_path() . '/images';
		} else {
			$imagesDir = config('app.uploads_folder') . '/images';
		}

		$subDir = date('/Y/m/');
		$filePath = $subDir . Helpers::getRandomString() . ".$type";
		$absoluteFilePath = "$imagesDir$filePath";

		if (!file_exists($imagesDir . $subDir)) {
			mkdir($imagesDir . $subDir, 0770, true);
		}

		$error = false;
		try {
			if (file_exists($absoluteFilePath)) {
				unlink($absoluteFilePath);
			}

			$image = new ImageResize($file);
			if ($image->getSourceWidth() > 1920 || $image->getSourceHeight() > 1080) {
				if ($image->getSourceHeight() > $image->getSourceWidth()) {
					$image->resizeToHeight(1080);
				} else {
					$image->resizeToWidth(1920);
				}
			}

			if ($type === 'png') {
				// Compression level: from 0 (no compression) to 9.
				$image->save($absoluteFilePath, IMAGETYPE_PNG, 9);
			} else {
				// From 0 (worst quality, smaller file) to 100 (best quality, biggest file).
				$image->save($absoluteFilePath, IMAGETYPE_JPEG, 75);

				// If image > 500KB delete it
				if (filesize($absoluteFilePath) > 524288) {
					unlink($absoluteFilePath);
					$error = true;
				}
			}
		} catch (Exception $ex) {
			$error = true;
			echo $ex->getTraceAsString();
		}

		if ($error) {
			return '';
		} else {
			return $filePath;
		}
	}

	public static function saveBase64Image($file, $public = true, $type = 'jpg')
	{
		if ($public) {
			$imagesDir = public_path() . '/images';
		} else {
			$imagesDir = config('app.uploads_folder') . '/images';
		}

		$subDir = date('/Y/m/');
		$filePath = $subDir . Helpers::getRandomString() . ".$type";
		$absoluteFilePath = "$imagesDir$filePath";

		if (!file_exists($imagesDir . $subDir)) {
			mkdir($imagesDir . $subDir, 0770, true);
		}

		$tmpAbsoluteFilePath = $absoluteFilePath . '.tmp';
		try {
			if (file_exists($absoluteFilePath)) {
				unlink($absoluteFilePath);
			}

			file_put_contents($tmpAbsoluteFilePath, base64_decode($file['base64']));
			$image = new ImageResize($tmpAbsoluteFilePath);
			if ($image->getSourceWidth() > 1920 || $image->getSourceHeight() > 1080) {
				if ($image->getSourceHeight() > $image->getSourceWidth()) {
					$image->resizeToHeight(1080);
				} else {
					$image->resizeToWidth(1920);
				}
			}

			if ($type === 'png') {
				// Compression level: from 0 (no compression) to 9.
				$image->save($absoluteFilePath, IMAGETYPE_PNG, 9);
			} else {
				// From 0 (worst quality, smaller file) to 100 (best quality, biggest file).
				$image->save($absoluteFilePath, IMAGETYPE_JPEG, 75);

				// If image > 500KB delete it
				if (filesize($absoluteFilePath) > 524288) {
					unlink($absoluteFilePath);
					$error = true;
				}
			}
		} catch (Exception $ex) {
			$error = true;
			echo $ex->getTraceAsString();
		} finally {
			if (file_exists($tmpAbsoluteFilePath)) {
				unlink($tmpAbsoluteFilePath);
			}
		}

		if ($error) {
			return '';
		} else {
			return $filePath;
		}
	}

	public static function uploadFiles($public = true, $fieldName = 'files')
	{
		$attachmentsDir = $public ? FileDirEnum::PublicAttachmentsDir : FileDirEnum::PrivateAttachmentsDir;

		$filePaths = array();

		$files = request()->file($fieldName);
		if ($files && count($files)) {
			foreach ($files as $file) {
				$fileName = '';
				$random = Helpers::getRandomString();
				$clientOriginalName = $file->getClientOriginalName();
				$arrTmp = explode('.', $clientOriginalName);
				if (count($arrTmp) === 2) {
					$fileName = $arrTmp[0] . '-' . $random . '.' . $arrTmp[1];
				} else {
					$fileName = $random . '-' . $clientOriginalName;
				}

				$filePath = date('/Y/m/') . $fileName;
				if (Storage::disk($attachmentsDir)->put($filePath, file_get_contents($file))) {
					array_push($filePaths, $filePath);
				}
			}
		}

		return $filePaths;
	}

	public static function uploadImages($public = true, $fieldName = 'files')
	{
		$imagePaths = array();

		$files = request()->file($fieldName);
		if ($files && count($files)) {
			foreach ($files as $file) {
				array_push($imagePaths, Helpers::saveImage($file, $public));
			}
		}

		return $imagePaths;
	}

	public static function uploadBase64Files($files, $public = true)
	{
		$attachmentsDir = $public ? FileDirEnum::PublicAttachmentsDir : FileDirEnum::PrivateAttachmentsDir;

		$filePaths = array();

		if ($files && count($files)) {
			foreach ($files as $file) {
				$fileName = '';
				$random = Helpers::getRandomString();
				$arrTmp = explode('.', $file['fileName']);
				if (count($arrTmp) === 2) {
					$fileName = $arrTmp[0] . '-' . $random . '.' . $arrTmp[1];
				} else {
					$fileName = $random . '-' . $file['fileName'];
				}

				$filePath = date('/Y/m/') . $fileName;
				if (Storage::disk($attachmentsDir)->put($filePath, base64_decode($file['base64']))) {
					array_push($filePaths, $filePath);
				}
			}
		}

		return $filePaths;
	}

	public static function uploadBase64Images($files, $public = true)
	{
		$imagePaths = array();

		if ($files && count($files)) {
			foreach ($files as $file) {
				array_push($imagePaths, Helpers::saveBase64Image($file, $public));
			}
		}

		return $imagePaths;
	}

	public static function deleteFiles($oldFiles, $public = true)
	{
		$attachmentsDir = $public ? FileDirEnum::PublicAttachmentsDir : FileDirEnum::PrivateAttachmentsDir;

		if ($oldFiles && count($oldFiles)) {
			foreach ($oldFiles as $oldFile) {
				if (Storage::disk($attachmentsDir)->exists($oldFile)) {
					Storage::disk($attachmentsDir)->delete($oldFile);
				}
			}
		}
	}

	public static function deleteImages($oldImages, $public = true)
	{
		$imagesDir = $public ? FileDirEnum::PublicImagesDir : FileDirEnum::PrivateImagesDir;

		if ($oldImages && count($oldImages)) {
			foreach ($oldImages as $oldImage) {
				if (Storage::disk($imagesDir)->exists($oldImage)) {
					Storage::disk($imagesDir)->delete($oldImage);
				}
			}
		}
	}

	public static function getAllDatabases(&$arrConnections, $dir = null)
	{
		$dir = $dir ? $dir : base_path() . '/dbconnections';

		$files = scandir($dir);
		foreach ($files as $file) {
			if (!in_array($file, array('.', '..'))) {
				$tmpPath = $dir . DIRECTORY_SEPARATOR . $file;
				if (is_dir($tmpPath)) {
					Helpers::getAllDatabases($arrConnections, $tmpPath);
				} else {
					if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
						$fileName = pathinfo($file, PATHINFO_FILENAME);
						if ($fileName !== config('app.master_db')) {
							array_push($arrConnections, $fileName);
						}
					}
				}
			}
		}
	}

	public static function pushNotifications($deviceTokens, $message, $title = null, $data = null)
	{
		if (count($deviceTokens)) {
			$body = array(
				'registration_ids' => $deviceTokens,
				'data' => $data,
				'notification' =>
				array(
					'title' => ($title ? $title : config('app.name')),
					'body' => $message
				)
			);

			$headers = array(
				'Content-Type' => 'application/json',
				'Authorization' => 'key=' . config('app.firebase_server_key')
			);

			$client = new Client([
				'base_uri' => 'https://fcm.googleapis.com',
				'headers' => $headers
			]);

			$res = $client->post('/fcm/send', array(
				'json' => $body
			));
		}
	}

	public static function sendOTPCode($phoneNumber, $otpCode)
	{
		$body = new \stdClass();
		$body->Phone = $phoneNumber;
		$body->Content = 'Ma xac thuc cua ban la ' . $otpCode;
		$body->ApiKey = '37520F68711BB1DF9123426E72E505';
		$body->SecretKey = 'B2DD0031FDC369FD8C6DD3E830BFD9';
		$body->SmsType = 2;
		$body->Brandname = 'Verify';

		$client = new Client(['base_uri' => 'http://rest.esms.vn']);
		$client->get('/MainService.svc/json/SendMultipleMessage_V4_get?' . http_build_query($body));
	}

	public static function stringifyIdList($idList)
	{
		return implode(',', $idList);
	}

	public static function toArrayIdsString($idsString)
	{
		return explode(',', $idsString);
	}
}
