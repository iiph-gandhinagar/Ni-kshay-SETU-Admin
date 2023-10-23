
@if($mode && $mode == "create")
    {{-- @if (app('request')->input('master') && app('request')->input('master') != '' && (app('request')->input('master') >= 0 || app('request')->input('master') == '0')) --}}
        <input type="hidden" v-model="form.parent_id" v-validate="'required'" @input="validate($event)" class="form-control" id="parent_id" name="parent_id" placeholder="{{ trans('admin.resource-material.columns.parent_id') }}">
        <input type="text" disabled value="{{$parent_title}}" class="form-control" :class="{'form-control-danger': errors.has('parent_id'), 'form-control-success': fields.parent_id && fields.parent_id.valid}" placeholder="{{ trans('admin.resource-material.columns.parent_id') }}">
    {{-- @endif --}}
@else
    <select class="form-control" v-model="form.parent_id" v-validate="'required'" @input="validate($event)" :class="{'form-control-danger': errors.has('parent_id'), 'form-control-success': fields.parent_id && fields.parent_id.valid}" id="parent_id" name="parent_id" placeholder="{{ trans('admin.assessment-question.columns.parent_id') }}">
        <option value="">Select Options</option>
        <option value="0">Root</option>
        @foreach ($folderList as $folder)
            <?php 
                $finalTitle = $folder->title;
                $parentFolderList = $folder;
                while($parentFolderList->parent_folder != null) {
                    $finalTitle = $parentFolderList->parent_folder->title .' > '.$finalTitle;
                    $parentFolderList =  $parentFolderList->parent_folder;
                }
            ?>
            <option value="{{$folder->id}}" >{{ $folder->title}} ({{$finalTitle}})</option>
        @endforeach
    </select>
@endif