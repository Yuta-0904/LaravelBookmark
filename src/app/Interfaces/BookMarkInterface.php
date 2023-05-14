<?php

namespace App\Interfaces;

interface BookMarkInterface
{
    // CRUD機能
    public function getBookMarkLists();

    public function findOrFail(int $bookmark_id);
    
    public function updateBookMark(int $bookmark_id, string $comment,string $category);
    
    public function deleteBookMark(int $bookmark_id);
    
    public function createBookMark(string $url,int $category,string $comment,object $preview);

}