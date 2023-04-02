<?php

namespace App\Bookmark\UseCase;

use App\Models\Bookmark;
use Dusterio\LinkPreview\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

final class DeleteBookmarkUseCase
{
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
    public function handle(int $id)
    {
        $model = Bookmark::query()->findOrFail($id);

        if ($model->can_not_delete_or_edit) {
            throw ValidationException::withMessages([
                'can_delete' => 'ブックマーク後24時間経過したものは削除できません'
            ]);
        }

        if ($model->user_id !== Auth::id()) {
            abort(403);
        }

        $model->delete();
    }
}