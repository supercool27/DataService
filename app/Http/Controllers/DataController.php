<?php
namespace App\Http\Controllers;

use Illuminate\Database\QueryException As Exception;
use Illuminate\Http\Request As Request;

use StackUtil\Utils\DbUtils;
use StackUtil\Utils\ApiUtils;
use StackUtil\Utils\Utility;
use App\Utils\MetadataUtils;


class DataController extends Controller{

    public function RecordList(Request $request, $tableName, $select = null, $where = null, $orderBy = null){
        $select = $request->input('select');
        $where = $request->input('where');
        $orderBy = $request->input('orderBy');

        try {
            if(empty($tableName)){
                return "Object not found!";
            }

            if($tableName != 'tobject'){
                $metadata = MetadataUtils::CallMetaData($request, $tableName);
                $data = MetadataUtils::ValidateRequest($request, $metadata, $tableName, null);
            }

            $result = DbUtils::generateQuery($tableName,null,$select,$where,$orderBy);

            return response()->json(['object'=>$result])->setStatusCode(200);
        } catch(Exception $ex){
            throw $ex;
        }
    }

    public function GetRecord(Request $request, $tableName, $idOrKey, $select = null, $where = null, $orderBy = null){
        $select = $request->input('select');
        $where = $request->input('where');
        $orderBy = $request->input('orderBy');

        try {
            if(empty($tableName)){
                return "Object not found!";
            }
            
            $result = DbUtils::generateQuery($tableName,$idOrKey,$select,$where,$orderBy);
            return response()->json($result)->setStatusCode(200);

        } catch(Exception $ex){
            throw $ex;
        }
    }

	public function DeleteRecord(Request $request, $tableName, $idOrKey){
        $where = $request->input('where');
        try {
            if(empty($tableName)){
                return "Object not found!";
            }
            $result = DbUtils::generateDelete($tableName, $idOrKey, $where);
            return response()->json(['message'=>'Record Deleted Successfully.'])->setStatusCode(200); //delete 204 => no content, 200=>with content;

        } catch(Exception $ex){
            throw $ex;
        }
        return 'true';
    }

    public function InsertRecord(Request $request, $tableName){
        $data = $request->all();
        try {
            if(empty($tableName)){
                return "Object not found!";
            }
            $metadata = MetadataUtils::CallMetaData($request, $tableName);
            $data = MetadataUtils::ValidateRequest($request, $metadata, $tableName, $data);
            if(isset($data)){
                $result = DbUtils::generateInsert($tableName, $data);
                if($result){
                    $insertedRecord = DbUtils::generateQuery($tableName,$data['id']);
                    return response()->json($insertedRecord)->setStatusCode(201);
                }else{
                    return response()->json(['error'=>$result])->setStatusCode(500);
                }
            }
        } catch(Exception $ex){
            throw $ex;
        }
    }

    public function UpdateRecord(Request $request, $tableName){
        $where = $request->input('where');
        return $tableName;
        // return "UpdateReocrd";
    }
}