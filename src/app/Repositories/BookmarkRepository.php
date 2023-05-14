<?php

namespace App\Repositories;

use App\Interfaces\BookMarkInterface;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Log;

class BookmarkRepository implements BookMarkInterface
{
    // ブックマーク一覧を10件取得＋紐づくカテゴリー、ユーザも取得
    public function getBookMarkLists():object
    {
        return Bookmark::with(['category', 'user'])->latest('id')->paginate(10);
    }

    // 指定のカテゴリに紐づくブックマーク一覧を10件取得＋紐づくカテゴリー、ユーザも取得
    public function getCategoryWithBookMarkLists($category_id):object
    {
        return Bookmark::with(['category', 'user'])->where('category_id', '=', $category_id)->latest('id')->paginate(10);
    }

    // 指定のブックマーク取得
    public function findOrFail(int $bookmark_id):Bookmark
    {
        return Bookmark::findOrFail($bookmark_id);
    }

    // 指定のブックマーク更新
    public function updateBookMark(int $bookmark_id, string $comment,string $category_id):void
    {
        try {    
            $bookmark = Bookmark::findOrFail($bookmark_id);
            if ($bookmark->user_id !== Auth::id()) {
                abort(403);
            }
            $bookmark->category_id = $category_id;
            $bookmark->comment = $comment;
            $bookmark->save();
            log::info('ブックマークの更新 操作ユーザID:'.Auth::id());
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            throw ValidationException::withMessages([
                'bookmark' => 'ブックマークの更新処理に失敗しました。再度更新し直してください。'
            ]);
        }
    }
    
    // 指定のブックマーク削除
    public function deleteBookMark(int $bookmark_id):void
    {
        try {
            $bookmark = Bookmark::findOrFail($bookmark_id);
            if ($bookmark->user_id !== Auth::id()) {
                abort(403);
            }
            $bookmark->delete();
            log::info('ブックマークの削除 操作ユーザID:'.Auth::id());
        }catch (\Exception $e) {
            Log::error($e->getMessage());
            throw ValidationException::withMessages([
                'bookmark' => 'ブックマークの削除処理に失敗しました。再度削除し直してください。'
            ]);
        }
    }
    
    // ブックマークの新規作成
    public function createBookMark(string $url,int $category,string $comment,object $preview):Bookmark
    {
            try {
                $bookmark = Bookmark::create([
                    'url' => $url,
                    'category_id' => $category,
                    'user_id' => Auth::id(),
                    'comment' => $comment,
                    'page_title' => $preview->title,
                    'page_description' => $preview->description,
                    'page_thumbnail_url' => $preview->cover,
                ]);
                log::info('ブックマークの作成 操作ユーザID:'.Auth::id());
                return $bookmark;
            }catch (\Exception $e) {
                Log::error($e->getMessage());
                throw ValidationException::withMessages([
                    'bookmark' => 'ブックマークの作成処理に失敗しました。再度作成し直してください。'
                ]);
            }
    }

}
