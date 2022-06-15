<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Artisan;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $query = Menu::orderby('id', 'desc')->where('id', '>', 0);
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
        $page_title = 'All Menus';
        $models = Menu::orderby('id', 'desc')->where('status', 1)->paginate(10);
        $roles = Role::where('status', 1)->get();
        return view('admin.menus.index', compact('models', 'roles', 'page_title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page_title = 'Add New Menu';
        $roles = Role::where('status', 1)->get();
        $parent_menus = Menu::where('parent_id', null)->get();
        return view('admin.menus.create', compact('page_title', 'roles', 'parent_menus'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        require base_path()."/crud-template/config.php";

        // return $this->createModel($request);
        // $this->addEntryInRoutes($request);
        // $this->createMigration($request);
        return $this->createViews($request);

        $this->validate($request, [
            'label' => 'required',
            'menu' => 'required',
        ]);

        try{
            $model = Menu::create([
                'menu_of' => $request->menu_of,
                'parent_id' => $request->parent_id,
                'icon' => $request->icon,
                'label' => $request->label,
                'menu' => $request->menu,
                'url' => $request->menu_of.'/'.str_replace(' ', '_', strtolower($request->menu)),
            ]);

            if($model){
                $this->addEntryInRoutes($request);
                $this->createMigration($request);
                $this->createController($request);
                $this->createModel($request);
                $this->createViews($request);
            }
            return redirect()->route('menu.index')->with('message', 'Menu Added Successfully !');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error. '.$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit(Menu $menu)
    {
        $page_title = 'Edit Menu';
        $roles = Role::where('status', 1)->get();
        $parent_menus = Menu::where('parent_id', null)->get();
        return view('admin.menus.edit', compact('menu', 'parent_menus', 'roles', 'page_title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        $this->validate($request, [
            'label' => 'required',
            'menu' => 'required',
        ]);

        try{
            $url_menu = str_replace(' ', '-', $request->menu);

            $menu->menu_of = $request->menu_of;
            $menu->parent_id = $request->parent_id;
            $menu->icon = $request->icon;
            $menu->label = $request->label;
            $menu->menu = $request->menu;
            $menu->url = $request->menu_of.'/'.Str::lower($url_menu);
            $menu->save();

            if($menu){
                return redirect()->route('menu.index')->with('message', 'Menu Updated Successfully !');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error. '.$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        $model = $menu->delete();
        if($model){
            return 1;
        }else{
            return 0;
        }
    }

    private function addEntryInRoutes($request){
        $model_name = str_replace(' ', '_', strtolower($request->menu));
        $controller_name = str_replace(' ', '', ucwords($request->menu)) ;
        $content = "Route::resource('". $model_name."', '". $controller_name ."Controller');";
        $myfile = fopen(ROUTES_FILE, "a") or die("Unable to open file!");

        $txt = PHP_EOL . $content;
        fwrite($myfile, $txt);
        fclose($myfile);
    }

    private function createMigration($request){
        $column_strings = [];
        foreach($request->column_names as $key=>$name){
            $default_type = '';
            if($request->default_types[$key]=='nullable'){
                $default_type='->nullable();';
            }elseif($request->default_types[$key]=='default'){
                $default_type='->default("'.$request->defaults[$key].'");';
            }else{
                $default_type=';';
            }
            $column_strings[] = '$table->'.$request->types[$key].'("'.str_replace(' ', '_', strtolower($name)).'")'.$default_type;
        }

        $migration_string = implode(',', $column_strings);
        $migration_columns = str_replace(',', ' ', $migration_string);

        $migration_file = Str::plural(str_replace(' ', '_', strtolower($request->menu)));
        $migration_class_name = Str::plural(str_replace(' ', '', ucwords($request->menu))) ;
    	$migration_file_name = date('Y_m_d_his').'_create_'.$migration_file ."_table";
    	$root = base_path();
    	$templateFolder = $root ."/crud-template";
    	$newDir = MIGRATION_PATH ;
    	$modelFile = file_get_contents($templateFolder."/migration.php");

    	$str1 = str_replace('{MigrationClassName}', $migration_class_name, $modelFile);
    	$str1 = str_replace('{tableName}', $migration_file, $str1);
    	$str1 = str_replace('{tableColumns}', $migration_columns, $str1);

        $ext = ".php";
		$str1  = "<?php \n". $str1;

		$this->createFile($newDir , $migration_file_name , $ext , $str1);

        Artisan::call('migrate');

		// echo "Controller Successfully Created at ".$newDir ."/". $migration_file_name ."<BR>";

    }

    private function createController($data){
    	$modelName = str_replace(' ', '', ucwords($data->menu)) ;
    	$ControllerName = $modelName  ."Controller";
		$viewFolderName = Str::plural(str::lower($modelName));
    	$root = base_path();
    	$templateFolder = $root ."/crud-template";
    	$newDir = CONTROLLER_PATH ;

    	$modelFile = file_get_contents($templateFolder."/controller.php");

    	$str1 = str_replace('{modelName}', $modelName, $modelFile);
    	$str1 = str_replace('{viewFolderName}', $viewFolderName, $str1);
    	$str1 = str_replace('{ControllerName}', $ControllerName, $str1);

		$ext = ".php";
		$str1  = "<?php \n". $str1;

		$this->createFile($newDir , $ControllerName , $ext , $str1);

		// echo "Controller Successfully Created at ".$newDir ."/". $ControllerName ."<BR>";

    }

    private function createModel($data){
    	$modelName = str_replace(' ', '', ucwords($data->menu)) ;
        $table_name = Str::plural(str_replace(' ', '_', strtolower($data->menu)));
    	$root = base_path();
    	$templateFolder = $root ."/crud-template";
    	$newDir = MODEL_PATH;

    	$modelFile = file_get_contents($templateFolder."/model.php");
    	$str1 = str_replace('{modelName}', $modelName, $modelFile);
    	$str1 = str_replace('{tableName}', $table_name, $str1);


        /* $columns = DB::select('show columns from ' . $table_name);

        $temp  = array();
		$temp2 = array();
		$conditions  = "";
		foreach ($columns as $value) {
            $temp[] = $value->Field;
            if($value->Null == "NO"){
            $temp2[] .=  "'".$value->Field . "' => 'required'" ;
            }
            $conditions .='if(!empty(Input::get("'.$value->Field.'"))){
            $query->where("'.$value->Field.'","=",Input::get("'.$value->Field.'"));
            } ' ."\n";

		//    echo "'" . $value->Field . "' => '" . $value->Type . "|" . ( $value->Null == "NO" ? 'required' : '' ) ."', <br/>" ; die;
		}

		$fieldsName = "'".implode("','", $temp) ."'";
		$rules = implode(",", $temp2);
		$str1 = str_replace('{fieldsNameOnly}', $fieldsName, $str1);
		$str1 = str_replace('{rules}', $rules, $str1);
		$str1 = str_replace('{conditions}', $conditions, $str1);
		if(!is_dir($newDir)){
			mkdir($newDir);
		} */

		$ext = ".php";
		$str1  = "<?php \n". $str1;
		$this->createFile($newDir , $modelName , $ext , $str1);

		echo "Model Successfully Created at ".$newDir ."/". $modelName ."<BR>";
    }

    private function createViews($data){
        $table_name = Str::plural(str_replace(' ', '_', strtolower($data->menu)));
        $route_menu = str_replace(' ', '_', strtolower($data->menu));
        $modelName = str_replace(' ', '', ucwords($data->menu)) ;

        $viewFolderName =$table_name;
        $controller_name = $modelName  ."Controller";

    	$root = base_path();
    	$templateFolder = $root ."/crud-template";
    	$newDir = VIEW_PATH ;
    	$newViewDir = $newDir ."/". $viewFolderName;

    	
    	$indexFile = file_get_contents($templateFolder."/templateViews/index.blade.php");
        $createFile = file_get_contents($templateFolder."/templateViews/create.blade.php");
    	$editFile = file_get_contents($templateFolder."/templateViews/edit.blade.php");
    	$showFile = file_get_contents($templateFolder."/templateViews/show.blade.php");
        $searchFile = file_get_contents($templateFolder."/templateViews/_search.blade.php");

    	// return $indexFile = str_replace('{modelName}', $controller_name, $indexFile);
    	// $createFile = str_replace('{modelName}', $controller_name, $createFile);
    	// $editFile = str_replace('{modelName}', $controller_name, $editFile);
    	// $showFile = str_replace('{modelName}', $controller_name, $showFile);
        // $searchFile = str_replace('{modelName}', $controller_name, $searchFile);

    	$form = '';
    	$index_page = "";
    	$show  = "";
        $t_columns = "";
    	$columns = DB::select('show columns from ' . $table_name);
        $index_title = ucwords($data->menu);
        $index_page_title = Str::plural(Str::upper($index_title));
        $create_page_title = 'Add New '. Str::upper($index_title);
        dd($columns);
    	foreach ($columns as $value) {
            if ($value->Field != 'id' && $value->Field != 'deleted_at' && $value->Field != 'created_at' && $value->Field != 'updated_at' && $value->Field != 'status') {
                $type = explode('(', $value->Type);
                
                $t_columns.='<th>'.Str::upper($value->Field).'</th>';

                $form .= '<div class="form-group">' ."\n";
                if($value->Field=='status'){
                    return $type;
                }
                $form .= '<label for="" class="col-sm-2 control-label">'.ucfirst($value->Field).' <span style="color:red">*</span></label>' ."\n".
                        '<div class="col-sm-8">';
                            if($type[0]=='text'){
                                $form .= '<textarea class="form-control" name="'.$value->Field.'"></textarea>'."\n";
                            }elseif($type[0]=='tinyint'){
                                $form .= '<select class="form-control" name="status">'.
                                            '<option value="1" selected>Active</option>'.
                                            '<option value="0">In Active</option>'.
                                        '</select>'."\n";
                            }elseif($type[0]=='varchar'){
                                $form .= '<input type="text" class="form-control" name="'.$value->Field.'" value="'.old("'.$value->Field.'").'" placeholder="Enter '.$value->Field.'">'."\n";
                            }elseif($type[0]=='int' || $type[0]=='bigint'){
                                $form .= '<input type="int" class="form-control" name="'.$value->Field.'" value="'.old("'.$value->Field.'").'" placeholder="Enter '.$value->Field.'">'."\n";
                            }else{
                                $form .= '<input type="'.$type[0].'" class="form-control" name="'.$value->Field.'" value="'.old("'.$value->Field.'").'" placeholder="Enter '.$value->Field.'">'."\n";
                            }
                            
                            $form .= '<span style="color: red">{{ $errors->first("'.$value->Field.'") }}</span>'.
                        '</div>';
                $form .= '</div>' ."\n";

                $index_page .= '<td>{{ $model->'.$value->Field.' }}</td>';

                $show .= '<p> {{$model->'.$value->Field.'}} </p>';
            }
		}

		$index_page .="<hr>";

		$createForm = $form;
        $createForm .= '<label for="" class="col-sm-2 control-label"></label>'."\n".
                        '<div class="col-sm-6">'.
                            '<button type="submit" class="btn btn-success pull-left">Save</button>'.
                        '</div>';

		$createForm = str_replace('{createForm}', $createForm, $createFile);
		$createForm = str_replace('{store_route}', '{{ route("'.$route_menu.'.store") }}', $createForm);
		$createForm = str_replace('{view_all_route}', '{{ route("'.$route_menu.'.index") }}', $createForm);
		$createForm = str_replace('{page_title}', 'ADD NEW '.Str::upper($modelName), $createForm);

		$updateForm = $form;
        $updateForm .= '<label for="" class="col-sm-2 control-label"></label>'."\n".
                        '<div class="col-sm-6">'.
                            '<select class="form-control" id="status" name="status">'+
                                '<option value="1" {{ $model->status==1?"selected":"" }}>Active</option>'+
                                '<option value="0" {{ $model->status==0?"selected":"" }}>InActive</option>'+
                            '</select>'.
                        '</div>';
        $updateForm .= '<label for="" class="col-sm-2 control-label"></label>'."\n".
                        '<div class="col-sm-6">'.
                            '<button type="submit" class="btn btn-success pull-left">Save</button>'.
                        '</div>';
		$updateForm = str_replace('{createForm}', $updateForm, $editFile);
        $updateForm = str_replace('{store_route}', '{{ route("'.$route_menu.'.update", $model->id) }}', $updateForm);
		$updateForm = str_replace('{view_all_route}', '{{ route("'.$route_menu.'.index") }}', $updateForm);
        $updateForm = str_replace('{page_title}', 'EDIT '.Str::upper($data->menu), $updateForm);

		$searchForm = str_replace('{index}', $index_page, $searchFile);

		$index = str_replace('{create_index_title}', $index_page_title, $indexFile);     
		$index = str_replace('{create_create_title}', $create_page_title, $index);     
		$index = str_replace('{create_route}', $route_menu, $index);     
		$index = str_replace('{index_route}', $route_menu, $index);     
		$index = str_replace('{tcolumns}', $t_columns, $index);     
		$index = str_replace('{index}', $index_page, $index);     

		$show = str_replace('{show}', $show, $showFile);

        if(!is_dir($newDir)){
			mkdir($newDir);
		}

		if(!is_dir($newViewDir)){
			mkdir($newViewDir);
		}

		$files = array();
		$files["_search"] = $searchForm;
		$files["create"] = $createForm;
		$files["edit"] = $updateForm;
		$files["index"] = $index;
		$files["show"] = $show;

		foreach($files as $filename => $content){
			$ext = ".blade.php";
			$this->createFile($newViewDir , $filename , $ext , $content);
			echo "Controller Successfully Created at ".$templateFolder ."/views/". $filename.$ext ."<BR>";
		}
    }

    private function createFile($dir , $fileName ,  $ext , $content){
    	$myfile = fopen($dir."/".$fileName. $ext, "w") or die("Unable to open file!");
		$txt = $content;
		fwrite($myfile, $txt);
		fclose($myfile);
    }
}
