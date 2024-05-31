<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

class CategoryController extends Controller
{
    public function categories(Request $request){
        $categories = Category::orderBy('id', 'DESC');

        if($request->has('key') && $request->input('key') != ''){
            $categories = $categories->where('key', 'RLIKE', $request->input('key'));
        }

        if($request->has('de') && $request->input('de') != ''){
            $categories = $categories->where('de', 'RLIKE', $request->input('de'));
        }

        if($request->has('en') && $request->input('en') != ''){
            $categories = $categories->where('en', 'RLIKE', $request->input('en'));
        }

        $categories = $categories->paginate(20);

        $categoriesForFilterInDe = Category::all();

        return view('modules.category.get', compact(['categories', 'categoriesForFilterInDe']));
    }

    public function saveCategories(Request $request){
        if(!$request->de){
            return redirect('category?add=true');
        }

        $category = Category::create([
            'key' => $request->category,
            'de' => $request->de,
            'en' => $request->en,
            'nl' => $request->nl,
            'fr' => $request->fr,
            'icon' => 'custom',
        ]);

        $file = $request->file('image');
        $fileName = str_replace(' ', '', $file->getClientOriginalName());

        $file->storeAs('public/category/'.$category->id, $fileName);

        $url = url('storage/category/'.$category->id.'/'.$fileName);

        Category::where('id', $category->id)->update([
           'image' => $url
        ]);

        shell_exec('chmod -R 777 public/storage/');

        return redirect('category')->with('message', 'Category has been created.');
    }

    public function updateImage(Request $request, $categoryId){
        $file = $request->file('image');
        $fileName = str_replace(' ', '', $file->getClientOriginalName());

        $file->storeAs('public/category/'.$categoryId, $fileName);

        $url = url('storage/category/'.$categoryId.'/'.$fileName);

        Category::where('id', $categoryId)->update([
            'image' => $url
        ]);

        shell_exec('chmod -R 777 public/storage/');

        return redirect('category')->with('message', 'Category image has been updated.');
    }

    public function editCategory($id){
        $category = Category::where('id', $id)->first();

        return view('modules.category.edit', compact(['category']));
    }

    public function deleteCategory($id){
        Category::where('id', $id)->delete();

        return redirect('category')->with('message', 'Category has been deleted.');
    }

    public function importCategories(){
        Category::where('icon', '<>', 'custom')->delete();

        $handle = fopen("https://promi-dl.de/Profiles/Live/9ccfda5a-8127-495a-a089-5df05b2c7a11/CAT.csv","r");

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row = explode(';', $data[0]);

            if($row[0] == 'KEY'){
                continue;
            }

            Category::create([
                'key' => $row[0],
                'de' => $row[1],
                'en' => isset($row[2])? $row[2] : '',
                'nl' => isset($row[3])? $row[3] : '',
                'fr' => isset($row[7])? $row[7] : '',
                'icon' => isset($row[5])? $row[5] : '',
                'image' => isset($row[6])? $row[6] : '',
            ]);
        }
        fclose($handle);
    }

    public function getCollectionSize($key, $de, $en){
        $categories = Category::where('categories.id', '>', 0);

        if($key){
            $categories = $categories->where('key', 'RLIKE', $key);
        }

        if($de){
            $categories = $categories->where('de', 'RLIKE', $de);
        }

        if($en){
            $categories = $categories->where('en', 'RLIKE', $en);
        }

        return $categories->count();
    }

    public function deleteCategories(Request $request){
        if($request->has('category_ids')){
            $categoryIds = explode(',', $request->input('category_ids'));

            foreach($categoryIds as $categoryId){
                Category::where('id', $categoryId)->delete();
            }
        }

        return redirect()->back()->with('message', 'Category has been deleted.');
    }
}
