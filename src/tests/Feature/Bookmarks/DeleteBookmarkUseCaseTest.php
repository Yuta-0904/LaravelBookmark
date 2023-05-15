<?php

namespace Tests\Feature\Bookmarks;

use App\UseCase\Bookmark\DeleteBookmarkUseCase;
use App\UseCase\Bookmark\CreateBookmarkUseCase;
use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use App\Models\User;
use Tests\TestCase;
use App\Lib\LinkPreview\LinkPreviewInterface;
use App\Lib\LinkPreview\MockLinkPreview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class DeleteBookmarkUseCaseTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCaseDelete = $this->app->make(DeleteBookmarkUseCase::class);

        $this->app->bind(LinkPreviewInterface::class, MockLinkPreview::class);
        $this->useCaseCreate = $this->app->make(CreateBookmarkUseCase::class);
    }

    public function testDeleteCorrectData()
    {
        // 念のため絶対に存在しないURL（example.comは使えないドメインなので）を使う
        $url = 'https://notfound.example.com/';
        $category = BookmarkCategory::query()->first()->id;
        $comment = 'テスト用のコメント';

        // ログインしないと失敗するので強制ログイン
        $testUser = User::query()->first();
        Auth::loginUsingId($testUser->id);

        // テストデータの作成
        $createData = $this->useCaseCreate->handle($url, $category, $comment);
        
        // テストデータの削除
        $this->useCaseDelete->handle($createData->id);
        Auth::logout();

        // データベースに作成したテストデータが削除されているかチェックする
        $this->assertDatabaseMissing('bookmarks', [
            'id' => $createData->id,
            'category_id' => $category,
            'user_id' => $testUser->id,
            'comment' => $comment,
            'page_title' => 'モックのタイトル',
            'page_description' => 'モックのdescription',
            'page_thumbnail_url' => 'https://i.gyazo.com/634f77ea66b5e522e7afb9f1d1dd75cb.png',
        ]);
    }

}
