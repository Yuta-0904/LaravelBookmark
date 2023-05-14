<?php

namespace App\UseCase\User;

use App\Models\Bookmark;
use App\Models\User;
use App\Interfaces\BookMarkInterface;
use App\Interfaces\UserInterface;
use Illuminate\Support\Facades\Auth;
use Artesaos\SEOTools\Facades\SEOTools;

final class ShowUserPageUseCase
{
    private BookMarkInterface $bookMarkRepository;
    private UserInterface $userRepository;
 
    public function __construct(BookMarkInterface $bookMarkRepository,UserInterface $userRepository)
    {
        $this->bookMarkRepository = $bookMarkRepository;
        $this->userRepository = $userRepository;
    }


     /**
     * SEO
     * title, description
     * title、descriptionには対象のカテゴリー名を含める
     *
     * ページ内に表示される内容
     * ・対象のユーザ名
     * ・ユーザが登録したブックマーク一覧
     * @return array
     */
    public function handle(User $user): array
    {
        SEOTools::setTitle("{$user->name}さんのプロフィール");
        SEOTools::setDescription("{$user->name}さんのプロフィールページです。{$user->name}さんが登録したブックマーク一覧が表示されます。");
        
        // 操作ユーザ自身が登録したブックマークを取得
        $bookmarks = $this->bookMarkRepository->getBookMarkListsWithUser($user->id);
        
        //ユーザ情報とユーザが登録したブックマーク情報
        return [
            'user' => $user,
            'bookmarks' => $bookmarks
        ];
    }
}
