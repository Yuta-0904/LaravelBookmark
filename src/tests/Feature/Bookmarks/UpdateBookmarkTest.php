<?php

namespace Tests\Feature\Bookmarks;


use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\UseCase\Bookmark\UpdateBookmarkUseCase;
use App\Models\BookmarkCategory;

class UpdateBookmarkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * ユーザー認証済み
     * ユーザーIDが作成者と一致
     * 投稿内容がバリデーション通る
     *
     */
    public function testUpdateCorrect()
    {
        $category = BookmarkCategory::query()->first()->id;
        $comment = '更新テスト用のコメント';
        $user = User::query()->find(1);
        Auth::loginUsingId($user->id);

        $response = $this->put('/bookmarks/1', [
            'comment' => $comment,
            'category' => $category,
        ]);
        Auth::logout();

        $response->assertRedirect('/bookmarks');
        $this->assertDatabaseHas('bookmarks', [
            'id' => 1,
            'comment' => $comment,
            'category_id' => $category,
        ]);
    }


    /**
     * ユーザーが未認証
     *
     * →ログインページへのリダイレクト
     */
    public function testFailedWhenLogoutUser()
    {
        $this->put('/bookmarks/1', [
            'comment' => 'ブックマークのテスト用のコメントです',
            'category' => 1,
        ])->assertRedirect('/login');
    }
}
