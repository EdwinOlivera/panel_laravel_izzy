@if ($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Name Field -->
    <div class="form-group row ">
        {!! Form::label('name', trans('lang.department_name'), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' =>
            trans('lang.department_name_placeholder')]) !!}
            <div class="form-text text-muted">
                {{ trans('lang.department_name_help') }}
            </div>
        </div>
    </div>

    <!-- Image Field -->
    <div class="form-group row">
        {!! Form::label('image', trans('lang.department_image'), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            <div style="width: 100%" class="dropzone image" id="image" data-field="image">
                <input type="hidden" name="image">
            </div>
            <a href="#loadMediaModal" data-dropzone="image" data-toggle="modal" data-target="#mediaModal"
                class="btn btn-outline-{{ setting('theme_color', 'primary') }} btn-sm float-right mt-1">{{ trans('lang.media_select') }}</a>
            <div class="form-text text-muted w-50">
                {{ trans('lang.department_image_help') }}
            </div>
        </div>
    </div>
    @prepend('scripts')
        <script type="text/javascript">
            $(document).ready(function() {
                
                if ({{ $idMarket }} != null) {
                    $('#idMarket').val({{ $idMarket }});
                } else {                    
                    var idMarketL = '0';
                    if (typeof(Storage) !== "undefined") {
                        idMarketL = localStorage.getItem("supermarket_id");
                    }
                    $('#idMarket').val(idMarketL);
                }
            });
            var var15671147171873255749ble = '';
            @if(isset($department) && $department->hasMedia('image'))
                var15671147171873255749ble = {
                name: "{!! $department->getFirstMedia('image')->name !!}",
                size: "{!! $department->getFirstMedia('image')->size !!}",
                type: "{!! $department->getFirstMedia('image')->mime_type !!}",
                collection_name: "{!! $department->getFirstMedia('image')->collection_name !!}"
            };
                    @endif
            var dz_var15671147171873255749ble = $(".dropzone.image").dropzone({
                    url: "{!!url('uploads/store')!!}",
                    addRemoveLinks: true,
                    maxFiles: 1,
                    init: function () {
                        @if(isset($department) && $department->hasMedia('image'))
                        dzInit(this, var15671147171873255749ble, '{!! url($department->getFirstMediaUrl('image','thumb')) !!}')
                        @endif
                    },
                    accept: function (file, done) {
                        dzAccept(file, done, this.element, "{!!config('medialibrary.icons_folder')!!}");
                    },
                    sending: function (file, xhr, formData) {
                        dzSending(this, file, formData, '{!! csrf_token() !!}');
                    },
                    maxfilesexceeded: function (file) {
                        dz_var15671147171873255749ble[0].mockFile = '';
                        dzMaxfile(this, file);
                    },
                    complete: function (file) {
                        dzComplete(this, file, var15671147171873255749ble, dz_var15671147171873255749ble[0].mockFile);
                        dz_var15671147171873255749ble[0].mockFile = file;
                    },
                    removedfile: function (file) {
                        dzRemoveFile(
                            file, var15671147171873255749ble, '{!! url("departments/remove-media") !!}',
                            'image', '{!! isset($department) ? $department->id : 0 !!}', '{!! url("uplaods/clear") !!}', '{!! csrf_token() !!}'
                        );
                    }
                });
            dz_var15671147171873255749ble[0].mockFile = var15671147171873255749ble;
            dropzoneFields['image'] = dz_var15671147171873255749ble;
            

        </script>
    @endprepend
</div>

<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- Market Id Field -->
    <div class="form-group row ">
        <input type="hidden" name="idMarket" id="idMarket">
        {!! Form::label('markets[]', trans('lang.department_market_id'), ['class' => 'col-3 control-label text-right'])
        !!}
        <div class="col-9">
            {!! Form::select('markets[]', $markets, $marketsSelected, ['class' => 'select2 form-control', 'multiple' =>
            'multiple']) !!}
            <div class="form-text text-muted">{{ trans('lang.department_market_id_help') }}</div>
        </div>
    </div>
    <!-- Description Field -->
    <div class="form-group row ">
        {!! Form::label('description', trans('lang.department_description'), [
        'class' => 'col-3 control-label
        text-right',
        ]) !!}
        <div class="col-9">
            {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' =>
            trans('lang.department_description_placeholder')]) !!}
            <div class="form-text text-muted">{{ trans('lang.department_description_help') }}</div>
        </div>
    </div>
</div>
@if ($customFields)
    <div class="clearfix"></div>
    <div class="col-12 custom-field-container">
        <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
        {!! $customFields !!}
    </div>
@endif
<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{ setting('theme_color') }}"><i class="fa fa-save"></i>
        {{ trans('lang.save') }} {{ trans('lang.department') }}</button>
    <a href="{!!  route('departments.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i>
        {{ trans('lang.cancel') }}</a>
</div>
