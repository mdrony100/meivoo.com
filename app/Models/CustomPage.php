<?php
namespace App\Models;
use Illuminate\Support\Str;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class CustomPage extends Model
{
    use RepoResponse;
    protected $table        = 'custom_pages';
    public $timestamps      = false;


    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->orderBy('order_id','DESC')->paginate($per_page);
        return $this->formatResponse(true, '', 'admin.cpage.index', $data);
    }
    public function getShow(int $id)
    {
        $data =  CustomPage::find($id);
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.custom-page.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.cpage.index', null);
    }

    public function postStore($request)
    {
        DB::beginTransaction();
        try {
            $page                   = new CustomPage();
            $page->title            = $request->title;
            if(!empty($request->url_slug)){
                $str                = strtolower($request->url_slug);
                $page->url_slug     = Str::slug($str);
            }
            else{
                $str                = strtolower($request->title);
                $page->url_slug     = Str::slug($str);
            }
            // $page->display_in       = $request->display_in;
            $page->body             = $request->body;
            // $page->position         = $request->position;
            $page->is_active        = $request->status;
            $page->order_id         = CustomPage::max('order_id')+1;
            $page->meta_title       = $request->meta_title;
            $page->meta_keywords    = $request->meta_keywords;
            $page->meta_description = $request->meta_description;
            $page->created_by       = Auth::user()->id;
            $page->created_at       = date('Y-m-d H:i:s');
            $page->save();
        } catch (\Exception $e) {
              DB::rollback();
            return $this->formatResponse(false, 'Unable to create page !', 'admin.cpage.create');
        }
        DB::commit();
        return $this->formatResponse(true, 'page has been created successfully !', 'admin.cpage.index',$page->PK_NO);
    }

     public function putUpdate($request)
    {
        DB::beginTransaction();
        try {
            $page                   = CustomPage::findOrFail($request->id);
            $page->title            = $request->title;
            $page->body             = $request->body;
            $page->is_active        = 1;
            $page->order_id         = $request->order_id ?? 1;
            $page->meta_title       = $request->meta_title;
            $page->meta_keywords    = $request->meta_keywords;
            $page->meta_description = $request->meta_description;  
            $page->save();
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollback();
            return $this->formatResponse(false, __('messages.toastr.unable_create_page'), 'admin.custom-page.edit',$request->id);
        }
        DB::commit();
        return $this->formatResponse(true, __('messages.toastr.success_create_page'), 'admin.cpage.index',$page->PK_NO);
    }

    public function getDelete(int $id)
    {
        DB::begintransaction();
        try { 
            $page = CustomPage::where('id',$id)->delete();
        } catch (\Exception $e) { 
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete page !', 'admin.cpage.index');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully delete page !', 'admin.cpage.index');
    }


}
