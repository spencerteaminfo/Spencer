<?php

namespace App\Services;

use App\Models\Group;
use App\Models\User;
use App\Models\Event;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class StorageService
{
    public function image(?UploadedFile $image, string $folder = 'thumbnails'): string
    {
        if (!$image) return '';
        return $image->store($folder, 'public');
    }
}
