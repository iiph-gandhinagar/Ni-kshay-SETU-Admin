<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\ResourceMaterial;
use App\Models\Subscriber;

class MaterialController extends BaseController
{
    public function getAllPdfs()
    {
        $pdfs = ResourceMaterial::with(['media'])->where('type_of_materials', 'pdfs')->get();
        $success = true;
        return ['status' => $success, 'data' => $pdfs, 'code' => 200];
    }

    public function getAllVideos()
    {
        $videos = ResourceMaterial::with(['media'])->where('type_of_materials', 'videos')->get();
        $success = true;
        return ['status' => $success, 'data' => $videos, 'code' => 200];
    }

    public function getAllDocument()
    {
        $document = ResourceMaterial::with(['media'])->where('type_of_materials', 'document')->get();
        $success = true;
        return ['status' => $success, 'data' => $document, 'code' => 200];
    }

    public function getAllImages()
    {
        $images = ResourceMaterial::with(['media'])->where('type_of_materials', 'images')->get();
        $success = true;
        return ['status' => $success, 'data' => $images, 'code' => 200];
    }

    public function getAllPpts()
    {
        // $ppts = ResourceMaterial::with(['media'])->where('type_of_materials','ppt')->get();
        $ppts = ResourceMaterial::with(['media'])->where('type_of_materials', 'ppt')->get()[0];
        $publicUrl = $ppts['media'][0]->getUrl();
        // $ppts->getUrl();
        $success = true;
        return ['status' => $success, 'data' => $publicUrl, 'code' => 200];
    }

    public function getAllMaterial($type, Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);

        $data = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id', 'state_id']);
        if ($data[0]['country_id'] != 0) {
            $material = ResourceMaterial::whereRaw("find_in_set('" . $data[0]['cadre_id'] . "',cadre)")
                ->whereRaw("find_in_set('" . $data[0]['country_id'] . "',country_id)")
                ->with(['media'])
                ->where('type_of_materials', $type);
        } else {
            $material = ResourceMaterial::whereRaw("find_in_set('" . $data[0]['cadre_id'] . "',cadre)")
                ->where('state', 'LIKE', '%' . $data[0]['state_id'] . '%')
                ->with(['media'])
                ->where('type_of_materials', $type);
        }


        if ($request['filter'] == "name" || $request['filter'] == "") {
            $material = $material->orderBy('title')->get();
        } elseif ($request['filter'] == "date") {
            $material = $material->orderBy('created_at', 'DESC')->get();
        } else {
            $material = $material->get();
        }
        $success = true;
        return ['status' => $success, 'data' => $material, 'code' => 200];
    }

    public function getAllRootFolders(Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);
        $materials = ResourceMaterial::with(['media'])->where('parent_id', 0)->where('type_of_materials', 'folder')->orderBy('index')->get();
        $success = true;
        return ['status' => $success, 'data' => $materials, 'code' => 200];
    }

    public function getFilesByParentId($parentId, Request $request)
    {
        $lang = $request->header('lang');

        if ($lang == NULL) {
            $lang = 'en';
        }
        app()->setLocale($lang);

        $data = Subscriber::where('api_token', $request->bearerToken())->get(['id', 'cadre_id', 'state_id', 'country_id']);
        if ($data[0]['country_id'] != 0) {
            $material = ResourceMaterial::with(['media'])
                ->whereRaw("find_in_set('" . $data[0]['cadre_id'] . "',cadre)")
                ->whereRaw("find_in_set('" . $data[0]['country_id'] . "',country_id)")
                ->where('parent_id', $parentId)
                ->orderByRaw("FIELD(type_of_materials , 'folder','pdfs','images','ppt','document','videos','pdf_office_orders') ASC");
        } else {
            $material = ResourceMaterial::with(['media'])
                ->whereRaw("find_in_set('" . $data[0]['cadre_id'] . "',cadre)")
                // ->where('state','LIKE','%'.$data[0]['state_id'].'%')
                ->whereRaw("find_in_set('" . $data[0]['state_id'] . "',state)")
                ->where('parent_id', $parentId)
                ->orderByRaw("FIELD(type_of_materials , 'folder','pdfs','images','ppt','document','videos','pdf_office_orders') ASC");
        }


        if ($request['filter'] == "name" || $request['filter'] == "") {
            $material = $material->orderBy('index')->orderBy('title')->get();
        } elseif ($request['filter'] == "date") {
            $material = $material->orderBy('created_at', 'DESC')->get();
        } else {
            $material = $material->get();
        }
        $success = true;
        return ['status' => $success, 'data' => $material, 'code' => 200];
    }
}
