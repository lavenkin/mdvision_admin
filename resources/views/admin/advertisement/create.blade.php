@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-4">广告位管理</div>
                    </div>
                  
                </div>
<!--                 <div class="dataTables_wrapper form-inline dt-bootstrap no-footer"> -->
                <!-- Table -->
                <div class="panel-body table-responsive">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">添加</h4>
                        </div>
                        <form action="{!! route('advertisement.store') !!}" method="post" id="myform">
                            <div class="modal-body">
                                
                                <div class="form-group">
                                    <label for="title">标题</label>
                                    <input type="text" class="form-control" name="title" placeholder="Title" value="">
                                    <span id="helpBlock" class="help-block">请填写标题，方便列表查询。</span>
                                </div>

                                <div class="form-group">
                                    <label for="review">轮播背景图片</label>
                                    <div>
                                        <image src="holder.js/180x135?text=点击上传图片 \n 1800x1350" width=180 height=135 id="adsBanner"/>
                                        <input type="hidden" name="banner_url" value="">
                                        <span id="helpBlock" class="help-block">请上传1800x1350尺寸的图片。</span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="content">选择文章</label>                            
                                    <select class="form-control select-article " name="articles" multiple="multiple"></select>
                                    <input type="hidden" id="articles_ids" name="articles"/>
                                    <span id="helpBlock" class="help-block">请选择2篇首页文章。</span>
                                </div>

                                <div class="form-group">
                                    <label for="content">选择产品</label>
                                    <select class="form-control select-product" name="products" multiple="multiple"></select>
                                    <input type="hidden" id="products_ids" name="products" />
                                    <!-- <p><input type="text" name="products[]" class="form-control select-product" placeholder="产品二" /></p>
                                    <p><input type="text" name="products[]" class="form-control select-product" placeholder="产品三" /></p>
                                    <p><input type="text" name="products[]" class="form-control select-product" placeholder="产品四" /></p> -->
                                    <span id="helpBlock" class="help-block">请选择4个产品。</span>
                                </div>
                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default back" data-dismiss="modal" OnClick="window.history.go(-1)">返回</button>
                                <button type="submit" class="btn btn-primary">保存</button>
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    
    var onSelectArticles = $(".select-article").select2({
        placeholder: "请选择",
        ajax: {
            url: "/api/search/articles",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true,
        },
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        maximumSelectionLength: 2,
        minimumInputLength:1,
        templateResult: function (data) {
            if (data.items != '') {
                return data.title;
            }
        },
        templateSelection: function (data, container) {
            return data.title;
        }
    });

    onSelectArticles.change(function() {
        var value = $(this).select2("data");
        var data = new Array();
        $.each(value, function(i,val) { 
            data[i] = val.id; 
        });  

        $('#articles_ids').val(data);
    });

    var onSelectProducts = $(".select-product").select2({
        placeholder: "请选择",
        ajax: {
            url: "/api/search/products",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;

                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true,
        },
        maximumSelectionLength:4,
        minimumInputLength:1,
        escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
        templateResult: function (data) {
            if (data.items != '') {
                return data.title;
            }
        },
        templateSelection: function (data, container) {
            return data.title;
        },
    });

    onSelectProducts.change(function() {
        var value = $(this).select2("data");
        var data = new Array();
        $.each(value, function(i,val) { 
            data[i] = val.id; 
        });  
        
        $('#products_ids').val(data);
    });

    // 图片上传
    var uploader = new plupload.Uploader({
        browse_button: 'adsBanner', // this can be an id of a DOM element or the DOM element itself
        url: '/api/image/upload',
        filters: {
            max_file_size: '10mb',
            mime_type: [{ title: "Image files", extensions: "jpg,png,jpeg" }]
        }
    });

    uploader.bind('FilesAdded', function(up, files) {
        uploader.start();
    });

    uploader.bind('FileUploaded', function(up, files, object) {
        imageUpResponse(object.response);
    });

    var imageUpResponse = function (data) {
        data = jQuery.parseJSON(data);
        // console.log(data);
        $("#adsBanner").attr('src', data.path);
        $("input[name=banner_url]").val(data.path);
    }

    uploader.init();

    // 表单验证
    $("#myform").validate({
        errorElement: 'span', //default input error message container
        errorClass: 'help-block help-block-error', // default input error message class
        focusInvalid: false, // do not focus the last invalid input
        ignore: '',
        rules: {
            title: "required",
            articles: "required",
            products: "required",
        },
        messages: {
            title: "标题不能为空",
            articles: "请选择文章",
            products: "请选择产品",
        },
        highlight: function (element) { // hightlight error inputs
            $(element)
                .closest('.form-group').addClass('has-error'); // set error class to the control group
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        success: function (label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
        submitHandler: function(form) {
            var options = { 
                // type: $(form).attr('method'),
                success: function(data) {
                    console.log(data);
                    $('#advertisement-table').DataTable().ajax.reload( null, false );
                    window.history.go(-1);
                }, 
 
                clearForm: true ,       // clear all form fields after successful submit 
                resetForm: true ,       // reset the form after successful submit 
            };  
            $(form).ajaxSubmit(options); 
        }
     });
});
</script>
@endpush