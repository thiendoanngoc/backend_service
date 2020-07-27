<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class FileDirEnum extends Enum
{
	const PublicImagesDir = 'publicImagesDir';
	const PublicAttachmentsDir = 'publicAttachmentsDir';
	const PrivateImagesDir = 'privateImagesDir';
	const PrivateAttachmentsDir = 'privateAttachmentsDir';
}
