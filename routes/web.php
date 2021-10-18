<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


//Route::post('/test','App\Http\Controllers\ProductTypeController@test');

//auth
Route::get('/logout','App\Http\Controllers\UserController@logout');
Route::post('/login','App\Http\Controllers\UserController@login');

//productType
Route::get('/product-types','App\Http\Controllers\ProductTypeController@index')->middleware('auth');
Route::get('/product-type','App\Http\Controllers\ProductTypeController@find')->middleware('auth');
Route::post('/product-type','App\Http\Controllers\ProductTypeController@save')->middleware('auth');
Route::put('/product-type','App\Http\Controllers\ProductTypeController@update');
Route::delete('/product-type','App\Http\Controllers\ProductTypeController@delete')->middleware('auth');


//product
Route::get('/products','App\Http\Controllers\ProductController@index');
Route::get('/product','App\Http\Controllers\ProductController@find')->middleware('auth');
Route::post('/product','App\Http\Controllers\ProductController@save')->middleware('auth');
Route::put('/product','App\Http\Controllers\ProductController@update');
Route::delete('/product','App\Http\Controllers\ProductController@delete')->middleware('auth');

//order
Route::post('/order','App\Http\Controllers\OrderController@sell')->middleware('auth');
Route::post('/order-buy','App\Http\Controllers\OrderController@buy');

Route::get('/orders','App\Http\Controllers\OrderController@index');
Route::get('/export-orders','App\Http\Controllers\OrderController@exportCsv')->middleware('auth');
Route::get('/order-detail','App\Http\Controllers\OrderController@orderDetail');
Route::post('/report','App\Http\Controllers\OrderController@ReportProduct');


//backup data
Route::get('backup-db', function () {
    /*
    Needed in SQL File:

    SET GLOBAL sql_mode = '';
    SET SESSION sql_mode = '';
    */
    $get_all_table_query = "SHOW TABLES";
    $result = DB::select(DB::raw($get_all_table_query));

    $tables = [
        'sh_order',
        'sh_order_detail',
        'sh_product',
        'sh_product_type',
        'sh_user',
    ];

    $structure = '';
    $data = '';
    foreach ($tables as $table) {
        $show_table_query = "SHOW CREATE TABLE " . $table . "";

        $show_table_result = DB::select(DB::raw($show_table_query));

        foreach ($show_table_result as $show_table_row) {
            $show_table_row = (array)$show_table_row;
            $structure .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
        }
        $select_query = "SELECT * FROM " . $table;
        $records = DB::select(DB::raw($select_query));

        foreach ($records as $record) {
            $record = (array)$record;
            $table_column_array = array_keys($record);
            foreach ($table_column_array as $key => $name) {
                $table_column_array[$key] = '`' . $table_column_array[$key] . '`';
            }

            $table_value_array = array_values($record);
            $data .= "\nINSERT INTO $table (";

            $data .= "" . implode(", ", $table_column_array) . ") VALUES \n";

            foreach($table_value_array as $key => $record_column)
                $table_value_array[$key] = addslashes($record_column);

            $data .= "('" . implode("','", $table_value_array) . "');\n";
        }
    }
    $file_name = __DIR__ . '/../database/database_backup_on_' . date('y_m_d') . '.sql';
    $file_handle = fopen($file_name, 'w + ');

    $output = $structure . $data;
    fwrite($file_handle, $output);
    fclose($file_handle);
    
    if (file_exists($file_name)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_name));
    readfile($file_name);
    exit;
}
});