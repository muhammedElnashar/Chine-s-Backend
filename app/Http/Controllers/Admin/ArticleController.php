<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::paginate(5);
        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $data=$request->validated();
        if ($request->hasFile('image')) {
            $path = $request->file('image')->storePublicly('images/articles', 's3');
            $data['image']=$path;
        }
        Article::create($data);
        return redirect()->route('articles.index')->with('success', 'Article created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        return view('articles.edit', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($article->image) {
                Storage::disk('s3')->delete($article->image);
            }

            $path = $request->file('image')->storePublicly('images/articles', 's3');
            $data['image'] = $path;
        }

        $article->update($data);

        return redirect()->route('articles.index')->with('success', 'Article updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        if ($article->image){
            Storage::disk('s3')->delete($article->image);
        }
        $article->delete();
        return redirect()->route('articles.index')->with('success', 'Article deleted successfully.');
    }
}
