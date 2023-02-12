@extends('admin.layout.master')
@section('title')
    مرتجع المشتريات العام
@endsection
@section('style')
    <link rel="stylesheet" href="{{ asset('adminassets/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminassets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection
@section('contentheader')
    حركات مخزنية
@endsection
@section('contentheaderlink')
    <a href="{{ route('admin.suppliers_orders_general_return.index') }}"> فواتير مرتجع المشتريات العام </a>
@endsection
@section('contentheaderactive')
    تعديل
@endsection
@section('content')



    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title card_title_center"> تعديل فاتورة مترجع مشتريات عام الي مورد </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @if (!@empty($data))

                        @if ($data['is_approved'] == 0)

                            <form action="{{ route('admin.suppliers_orders_general_return.update', $data['id']) }}"
                                method="post">
                                @csrf
                                <div class="form-group">
                                    <label> تاريخ الفاتورة</label>
                                    <input name="order_date" id="order_date" type="date"
                                        value="{{ old('order_date', $data['order_date']) }}" class="form-control"
                                        value="{{ old('order_date') }}">
                                    @error('notes')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label> بيانات الموردين</label>
                                    <select name="supplier_code" id="supplier_code" class="form-control select2">
                                        <option value="">اختر المورد</option>
                                        @if (@isset($suupliers) && !@empty($suupliers))
                                            @foreach ($suupliers as $info)
                                                <option @if (old('supplier_code', $data['supplier_code']) == $info->supplier_code) selected="selected" @endif
                                                    value="{{ $info->supplier_code }}"> {{ $info->name }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('supplier_code')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                                <div class="form-group">
                                    <label> نوع الفاتورة</label>
                                    <select name="pill_type" id="pill_type" class="form-control">
                                        <option @if (old('pill_type', $data['pill_type']) == 1) selected="selected" @endif value="1">
                                            كاش</option>
                                        <option @if (old('pill_type', $data['pill_type']) == 2) selected="selected" @endif value="2">
                                            اجل</option>
                                    </select>
                                    @error('pill_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label> مخزن صرف المرتجع</label>
                                    <select @if ($added_counter_details > 0) disabled @endif name="store_id" id="store_id"
                                        class="form-control select2">
                                        <option value=""> اختر المخزن </option>
                                        @if (@isset($stores) && !@empty($stores))
                                            @foreach ($stores as $info)
                                                <option @if (old('store_id', $data['store_id']) == $info->id) selected="selected" @endif
                                                    value="{{ $info->id }}"> {{ $info->name }} </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('store_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label> ملاحظات</label>
                                    <input name="notes" id="notes" class="form-control"
                                        value="{{ old('notes', $data['notes']) }}">
                                    @error('notes')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-primary btn-sm"> تعديل</button>
                                    <a href="{{ route('admin.suppliers_orders_general_return.index') }}"
                                        class="btn btn-sm btn-danger">الغاء</a>

                                </div>


                            </form>
                        @else
                            <div class="alert alert-danger">
                                عفوا لايمكت تحديث فاتورة معتمدة ومؤرشفة
                            </div>
                        @endif
                    @else
                        <div class="alert alert-danger">
                            عفوا لاتوجد بيانات لعرضها !!
                        </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
    </div>

@endsection

@section('script')
    <script src="{{ asset('adminassets/plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        //Initialize Select2 Elements
        $('.select2').select2({
            theme: 'bootstrap4'
        });
    </script>
@endsection
