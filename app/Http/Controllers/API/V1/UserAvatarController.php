<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserAvatarResource;
use App\Models\UserAvatar;
use Illuminate\Http\Request;

class UserAvatarController extends Controller
{
    public function index()
    {
        $avatars = UserAvatar::all();
        return UserAvatarResource::collection($avatars);
    }

    public function show(UserAvatar $userAvatar)
    {
        return new UserAvatarResource($userAvatar);
    }
}
