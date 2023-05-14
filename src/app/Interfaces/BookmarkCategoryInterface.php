<?php

namespace App\Interfaces;

interface BookmarkCategoryInterface
{
    // CRUD機能
    public function getBookmarkCategory();

    public function getBookmarkCategoryAll();
    
    public function getBookmarkCategoryWithout(int $category_id);

    public function findOrFail(int $category_id);
    
}