<?php

namespace App\Bookmark\UseCase;

use App\Models\Bookmark;
use App\Interfaces\BookMarkInterface;
use App\Interfaces\BookmarkCategoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

final class UpdateBookmarkUseCase
{

    private BookMarkInterface $bookMarkRepository;
    private BookmarkCategoryInterface $bookmarkCategoryRepository;
 
    public function __construct(BookMarkInterface $bookMarkRepository,BookmarkCategoryInterface $bookmarkCategoryRepository)
    {
        $this->bookMarkRepository = $bookMarkRepository;
        $this->bookmarkCategoryRepository = $bookmarkCategoryRepository;
    }

    /**
     * ブックマーク作成処理
     *
     * 未ログインの場合、処理を続行するわけにはいかないのでログインページへリダイレクト
     *
     * 投稿内容のURL、コメント、カテゴリーは不正な値が来ないようにバリデーション
     *
     * ブックマークするページのtitle, description, サムネイル画像を専用のライブラリを使って取得し、
     * 一緒にデータベースに保存する※ユーザーに入力してもらうのは手間なので
     * URLが存在しないなどの理由で失敗したらバリデーションエラー扱いにする
     *
     * @param string $url
     * @param int $category
     * @param string $comment
     * @throws ValidationException
     */
    public function handle(int $id, string $comment,string $category_id):void
    {
        // ブックマークカテゴリーの存在チェック
        $this->bookmarkCategoryRepository->findOrFail($category_id);

        $this->bookMarkRepository->updateBookMark($id,$comment,$category_id);
    }
}