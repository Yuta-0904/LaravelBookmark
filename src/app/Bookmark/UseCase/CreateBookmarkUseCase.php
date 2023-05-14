<?php

namespace App\Bookmark\UseCase;

use App\Lib\LinkPreview\LinkPreviewInterface;
use App\Models\Bookmark;
use App\Interfaces\BookMarkInterface;
use App\Interfaces\BookmarkCategoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Artesaos\SEOTools\Facades\SEOTools;


final class CreateBookmarkUseCase
{
    private LinkPreviewInterface $linkPreview;
    private BookMarkInterface $bookMarkRepository;
    private BookmarkCategoryInterface $bookmarkCategoryRepository;
 
    public function __construct(LinkPreviewInterface $linkPreview,BookMarkInterface $bookMarkRepository,BookmarkCategoryInterface $bookmarkCategoryRepository)
    {
        $this->linkPreview = $linkPreview;
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
    public function handle(string $url, int $category, string $comment): Bookmark
    {
        try {
            $preview = $this->linkPreview->get($url);
            $bookMark = $this->bookMarkRepository->createBookMark($url,$category,$comment,$preview);
            return $bookMark;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw ValidationException::withMessages([
                'url' => 'URLが存在しない等の理由で読み込めませんでした。変更して再度投稿してください'
            ]);
        }
    }


    /**
     * SEO
     * title, description
     * title、descriptionは固定
     *
     * ページ内に表示される内容
     * ・カテゴリ一覧
     *
     * @param string $url
     * @param int $category
     * @param string $comment
     * @throws ValidationException
     */
    public function show():object
    {
        SEOTools::setTitle('ブックマーク作成');
        SEOTools::setDescription("ブックマークの新規登録画面です。タイトルとカテゴリを選択してブックマークの登録をすることができます。");

        return $this->bookmarkCategoryRepository->getBookmarkCategoryAll();
    }
}