<?php


namespace App\Http\Controllers\Bookmarks;


use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\BookmarkCategory;
use App\Models\User;
use Artesaos\SEOTools\Facades\SEOTools;
use Dusterio\LinkPreview\Client;
use Dusterio\LinkPreview\Exceptions\UnknownParserException;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

use App\Bookmark\UseCase\ShowBookmarkListPageUseCase;
use App\Bookmark\UseCase\CreateBookmarkUseCase;
use App\Bookmark\UseCase\UpdateBookmarkUseCase;
use App\Bookmark\UseCase\DeleteBookmarkUseCase;
use App\Http\Requests\CreateBookmarkRequest; 
use App\Http\Requests\UpdateBookmarkRequest;
use App\Lib\LinkPreview\MockLinkPreview;

class BookmarkController extends Controller
{
    /**
     * ブックマーク一覧画面
     *
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
     *
     * @return Application|Factory|View
     */
    public function list(Request $request, ShowBookmarkListPageUseCase $useCase)
    {
        //ブックマーク一覧ページ表示
        return view('page.bookmark_list.index', [
            'h1' => 'ブックマーク一覧',
        ] + $useCase->handle());
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
     * @param CreateBookmarkRequest $request
     * @param CreateBookmarkUseCase $useCase <- 一応、ここのコメントをちゃんと追記しておきましょう
     * @return Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws ValidationException
     */
    public function create(CreateBookmarkRequest $request, CreateBookmarkUseCase $useCase)
    {
        $useCase->handle($request->url, $request->category, $request->comment);

        // 暫定的に成功時は一覧ページへ
        return redirect('/bookmarks', 302);
    }

    /**
     * ブックマーク更新
     * コメントとカテゴリのバリデーションは作成時のそれと合わせる
     * 本人以外は編集できない
     *
     * @param Request $request
     * @param int $id
     * @return Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws ValidationException
     */
    public function update(UpdateBookmarkRequest $request, int $id,UpdateBookmarkUseCase $useCase)
    {
        $useCase->handle(
            $id,
            $request->comment,
            $request->category
        );

        // 成功時は一覧ページへ
        return redirect('/bookmarks', 302);
    }

    /**
     * ブックマーク削除
     * 本人以外のブックマークは削除できない
     *
     * @param int $id
     * @return Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws ValidationException
     */
    public function delete(int $id,DeleteBookmarkUseCase $useCase)
    {
        $useCase->handle(
            $id
        );

        // 暫定的に成功時はプロフィールページへ
        return redirect('/user/profile', 302);
    }

    /**
     * 編集画面の表示
     * 未ログインであればログインページへ
     * 存在しないブックマークの編集画面なら表示しない
     * 本人のブックマークでなければ403で返す
     *
     * @param Request $request
     * @param int $id
     * @return Application|Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function showEditForm(Request $request, int $id,ShowBookmarkListPageUseCase $useCase)
    { 
        //ブックマーク編集ページ表示
        return view('page.bookmark_edit.index', [
            'user' => Auth::user()
        ] + $useCase->show($id));
    }

      /**
     * カテゴリ別ブックマーク一覧
     *
     * カテゴリが数字で無かった場合404
     * カテゴリが存在しないIDが指定された場合404
     *
     * title, descriptionにはカテゴリ名とカテゴリのブックマーク投稿数を含める
     *
     * 表示する内容は普通の一覧と同様
     * しかし、カテゴリに関しては現在のページのカテゴリを除いて表示する
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function listCategory(Request $request,ShowBookmarkListPageUseCase $useCase)
    {
        $category_id = $request->category_id;
        if (!is_numeric($category_id)) {
            abort(404);
        }

        //カテゴリ一覧ページ表示
        return view('page.bookmark_list.index', $useCase->get($category_id));
    }

    /**
     * ブックマーク作成フォームの表示
     * @return Application|Factory|View
     */
    public function showCreateForm(CreateBookmarkUseCase $useCase)
    {
        //ブックマーク作成ページ表示
        return view('page.bookmark_create.index', [
            'master_categories' => $useCase->show(),
        ]);
    }
}
