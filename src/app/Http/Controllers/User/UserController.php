<?php


namespace App\Http\Controllers\User;


use App\Models\Bookmark;
use App\Models\User;
use Artesaos\SEOTools\Facades\SEOTools;
use Illuminate\Support\Facades\Auth;
use App\UseCase\User\ShowUserPageUseCase;

class UserController extends \App\Http\Controllers\Controller
{
    /**
     * プロフィールの表示
     * プロフィールといいつつ、ここでは簡略のため自身のブックマーク一覧のみ表示する
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function showProfile(ShowUserPageUseCase $useCase)
    {  
        // マイページ表示
        return view('page.profile.index',$useCase->handle(Auth::user()));
    }
}