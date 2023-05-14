<?php

namespace App\UseCase\Bookmark;

use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use App\Models\User;
use App\Interfaces\BookmarkCategoryInterface;
use App\Interfaces\BookMarkInterface;
use App\Interfaces\UserInterface;
use Illuminate\Support\Facades\Auth;
use Artesaos\SEOTools\Facades\SEOTools;

final class ShowBookmarkListPageUseCase
{

    private BookmarkCategoryInterface $bookmarkCategoryRepository;
    private BookMarkInterface $bookMarkRepository;
    private UserInterface $userRepository;
 
    public function __construct(BookmarkCategoryInterface $bookmarkCategoryRepository,BookMarkInterface $bookMarkRepository,UserInterface $userRepository)
    {
        $this->bookmarkCategoryRepository = $bookmarkCategoryRepository;
        $this->bookMarkRepository = $bookMarkRepository;
        $this->userRepository = $userRepository;
    }


    /**
     * SEO
     * title, description
     * titleは固定、descriptionは人気のカテゴリTOP5を含める
     *
     * ソート
     * ・投稿順で最新順に表示
     *
     * ページ内に表示される内容
     * ・ブックマーク※ページごとに10件
     * ・最も投稿件数の多いカテゴリ※トップ10件
     * ・最も投稿数の多いユーザー※トップ10件
     * @return array
     */
    public function handle(): array
    {
        /**
         * SEOに必要なtitleタグなどをファサードから設定できるライブラリ
         * @see https://github.com/artesaos/seotools
         */
        SEOTools::setTitle('ブックマーク一覧');

        $bookmarks = $this->bookMarkRepository->getBookMarkLists();

        $top_categories = $this->bookmarkCategoryRepository->getBookmarkCategory();

        // Descriptionの中に人気のカテゴリTOP5を含めるという要件
        SEOTools::setDescription("技術分野に特化したブックマーク一覧です。みんなが投稿した技術分野のブックマークが投稿順に並んでいます。{$top_categories->pluck('display_name')->slice(0, 5)->join('、')}など、気になる分野のブックマークに絞って調べることもできます");

        $top_users = $this->userRepository->getUserLists();

        return [
            'bookmarks' => $bookmarks,
            'top_categories' => $top_categories,
            'top_users' => $top_users
        ];
    }


    /**
     * SEO
     * title, description
     * titleは固定、descriptionは対象のブックマークのタイトルを含める
     *
     * ページ内に表示される内容
     * ・対象ブックマークのタイトル
     * ・対象ブックマークに紐づくカテゴリ
     * ・カテゴリ一覧
     * @return array
     */
    public function show($bookMark_id): array
    {
        $bookmark = $this->bookMarkRepository->findOrFail($bookMark_id);
        SEOTools::setTitle('ブックマーク編集');
        SEOTools::setDescription("{$bookmark->page_title}の編集画面です。");
        if ($bookmark->user_id !== Auth::id()) {
            abort(403);
        }

        $master_categories = $this->bookmarkCategoryRepository->getBookmarkCategoryAll();

        return [
            'bookmark' => $bookmark,
            'master_categories' => $master_categories,
        ];
    }


     /**
     * SEO
     * title, description
     * title、descriptionには対象のカテゴリー名を含める
     *
     * ページ内に表示される内容
     * ・対象のカテゴリ名
     * ・最も投稿件数の多いカテゴリ※トップ10件
     * ・ブックマーク※ページごとに10件
     * @return array
     */
    public function get($category_id): array
    {
        $category = $this->bookmarkCategoryRepository->findOrFail($category_id);
        SEOTools::setTitle("{$category->display_name}のブックマーク一覧");
        SEOTools::setDescription("{$category->display_name}に特化したブックマーク一覧です。みんなが投稿した{$category->display_name}のブックマークが投稿順に並んでいます。全部で{$category->bookmarks->count()}件のブックマークが投稿されています");


        $bookmarks = $this->bookMarkRepository->getCategoryWithBookMarkLists($category_id);

        // 自身のページのカテゴリを表示しても意味がないのでそれ以外のカテゴリで多い順に表示する
        $top_categories = $this->bookmarkCategoryRepository->getBookmarkCategoryWithout($category_id);

        $top_users = $this->userRepository->getUserLists();

        //カテゴリ一覧ページ表示
        return [
            'h1' => "{$category->display_name}のブックマーク一覧",
            'bookmarks' => $bookmarks,
            'top_categories' => $top_categories,
            'top_users' => $top_users
        ];
    }
}
