<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Session;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {

        if($request->ajax()){
            $query = Product::orderby('id', 'desc')->where('id', '>', 0);
            if($request['search'] != ""){
                $query->where('menu', 'like', '%'. $request['search'] .'%');
                $query->orWhere('label', 'like', '%'. $request['search'] .'%');
                $query->orWhere('status', 'like', '%'. $request['search'] .'%');
                $query->orWhere('parent_id', 'like', '%'. $request['search'] .'%');
                $query->orWhere('menu_of', 'like', '%'. $request['search'] .'%');
            }
            if($request['status']!="All"){
                $query->where('menu_of', $request['status']);
            }
            $models = $query->paginate(10);
            return (string) view('admin.menus.search', compact('models'));
        }
        $page_title = 'All Products';
        $models = Product::orderby('id', 'desc')->where('status', 1)->paginate(10);
        return view('products.index', compact('models', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, Product::getValidationRules());

        $input = $request->all();
	    Product::create($input);

	    Session::flash('flash_message', 'Task successfully added!');

	    return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {

        $model = Product::findOrFail($id);

      	return view('products.show', array('model' => $model));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
         $model = Product::findOrFail($id);

    return view('products.edit')->withModel($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request)
    {
        //
       $model = Product::findOrFail($id);

	    $this->validate($request, Product::getValidationRules());

	    $model->fill( $request->all() )->save();

	    Session::flash('flash_message', 'Product successfully updated!');

	    return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
        $model = Product::findOrFail($id);

	    $model->delete();

	    Session::flash('flash_message', 'Product successfully deleted!');

	    return redirect()->route('products.index');
    }
}
