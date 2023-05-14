<?php

namespace App\Repositories;

use App\Interfaces\BookmarkCategoryInterface;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use App\Models\BookmarkCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Log;

class BookmarkCategoryRepository implements BookmarkCategoryInterface
{
    // カテゴリ一覧10件＋紐付くブックマークの件数取得
    public function getBookmarkCategory():object
    {
        return BookmarkCategory::withCount('bookmarks')
            ->orderBy('bookmarks_count', 'desc')->orderBy('id')->take(10)->get();
    }

    // カテゴリー全件取得
    public function getBookmarkCategoryAll():object
    {
        return BookmarkCategory::orderBy('id')->get();
    }
      
    // 指定のカテゴリ以外のカテゴリ一覧10件＋紐付くブックマークの件数取得
    public function getBookmarkCategoryWithout(int $category_id):object
    {
        return BookmarkCategory::query()->withCount('bookmarks')
            ->orderBy('bookmarks_count', 'desc')->orderBy('id')->where('id', '<>', $category_id)->take(10)->get();
    }
    
    // 指定のカテゴリ取得
    public function findOrFail(int $category_id):BookmarkCategory
    {
        return BookmarkCategory::findOrFail($category_id);
    }

}
