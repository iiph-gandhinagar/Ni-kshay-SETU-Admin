<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\StaticBlog;
use Illuminate\Http\Request;

class StaticBlogController extends BaseController
{
    public function getAllBlogs(Request $request)
    {
        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $allBlogs = StaticBlog::with(['media'])->where('active', 1)->orderby('order_index')->get(['id', 'title', 'short_description', 'author', 'source', 'order_index', 'slug', 'keywords', 'created_at']);
        $success = true;
        return ['status' => $success, 'data' => $allBlogs, 'code' => 200];
    }

    public function getBlogsDetails(Request $request, $slug)
    {
        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $blog = StaticBlog::with(['media'])->where('slug', $slug)->get();
        $success = true;
        return ['status' => $success, 'data' => $blog, 'code' => 200];
    }

    public function getSimilarBlogs(Request $request, $slug)
    {
        $lang = $request->header('lang');
        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $blog_keywords = StaticBlog::where('slug', $slug)->get(['keywords']);

        if (count($blog_keywords) > 0) {
            $keywords_array = explode('|', $blog_keywords[0]['keywords']);
            foreach ($keywords_array as $item) {
                // $string = DB::select("SELECT id, title, short_description, author, source, order_index, keywords, created_at FROM static_blogs WHERE id != $id and (instr(keywords,'|$item|') or  instr(keywords,'$item')) and deleted_at is NULL"); //$item space needed for perfect match
                $string = StaticBlog::with(['media'])
                    ->orWhere('keywords', 'LIKE', "%|$item|%")
                    ->orWhere('keywords', 'LIKE', "%$item%")
                    ->where('slug', '!=', $slug)
                    ->get(['id', 'title', 'short_description', 'author', 'source', 'order_index', 'keywords', 'created_at', 'slug']);
                if (count($string) > 0) {
                    $success = true;
                    return ['status' => $success, 'data' => $string, 'code' => 200];
                } else {
                    $success = true;
                    return ['status' => $success, 'data' => [], 'code' => 200];
                }
            }
        } else {
            $success = true;
            return ['status' => $success, 'data' => [], 'code' => 200];
        }
    }
}
