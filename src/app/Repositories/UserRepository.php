<?php

namespace App\Repositories;

use App\Interfaces\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Log;

class UserRepository implements UserInterface
{
    // 投稿数の多いユーザートップ10件を取得
    public function getUserLists():object
    {
        return User::query()->withCount('bookmarks')->orderBy('bookmarks_count', 'desc')->orderBy('id')->take(10)->get();
    }
}
