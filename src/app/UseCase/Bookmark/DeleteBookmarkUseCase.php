<?php

namespace App\UseCase\Bookmark;

use App\Models\Bookmark;
use App\Interfaces\BookMarkInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


final class DeleteBookmarkUseCase
{

    private BookMarkInterface $bookMarkRepository;
 
    public function __construct(BookMarkInterface $bookMarkRepository)
    {
        $this->bookMarkRepository = $bookMarkRepository;
    }

    /**
     * ブックマーク削除処理
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
    public function handle(int $id):void
    {
        $this->bookMarkRepository->deleteBookMark($id);
    }
}