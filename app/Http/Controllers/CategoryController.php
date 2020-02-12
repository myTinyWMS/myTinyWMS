<?php

namespace Mss\Http\Controllers;

use Barryvdh\Snappy\Facades\SnappyPdf;
use http\Env\Request;
use Illuminate\Support\Facades\App;
use Mss\Models\Category;
use Illuminate\Http\Response;
use Mss\DataTables\CategoryDataTable;
use Mss\Http\Requests\CategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(CategoryDataTable $categoryDataTable)
    {
        return $categoryDataTable->render('category.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $category = new Category();

        return view('category.create', compact('category'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request) {
        Category::create($request->all());

        flash('Kategorie angelegt')->success();

        return redirect()->route('category.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $category = Category::findOrFail($id);
        $context = [
            "category" => $category,
            "audits" => $category->getAudits()
        ];

        return view('category.show', $context);
    }

    /**
     * @param CategoryRequest $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function update(CategoryRequest $request, $id) {
        $category = Category::findOrFail($id);

        // save data
        $category->update($request->all());

        flash(__('Kategorie gespeichert'))->success();

        return redirect()->route('category.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        Category::findOrFail($id)->delete();

        flash(__('Kategorie gelöscht'))->success();

        return redirect()->route('category.index');
    }

    public function printList() {
        $categories = request('category');

        if (empty($categories) || !is_array($categories) || count($categories) == 0) {
            flash(__('Bitte mind. 1 Kategorie auswählen'))->error();
            return redirect()->route('category.index');
        }

        $categories = Category::withActiveArticles()->orderedByName()->find($categories);

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('margin-top', 30);

        return $pdf->loadView('documents.category_article_list', compact('categories'))->download('list.pdf');
    }
}
