@extends('template.simple')
@section('content')
<form class="row" method="POST" action="{{route('tickets.store')}}">
    @csrf
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <h4 class="col-md-6 col-sm-12 d-flex align-items-center">
                        <i class="material-icons mr-2">add</i>
                        <span>Novo Ticket</span>
                    </h4>
                    <div class="col-md-6 col-sm-12">
                        <a href="{{route('tickets.index')}}" class="float-right link">Voltar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row d-flex justify-content-center mt-3">

                    <div class="col-md-8">
                        <div class="row d-flex align-items-center mb-3">
                            <div class="col-md-2">
                                <b>Assunto :</b>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control" value="{{old('subject')}}" name="subject" required>
                                <small class="form-text text-muted">Descreva brevemente seu ticket</small>
                            </div>
                        </div>
                        <div class="row d-flex align-items-center mb-3">
                                <div class="col-md-2">
                                    <b>Categoria :</b>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control" name="category_id" id="category_id" required>
                                        @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Selecione a categoria que seu ticket melhor se encaixa</small>
                                </div>
                            </div>
                        <div class="row d-flex align-items-center">
                            <div class="col-md-2">
                                <b>Descrição :</b>
                            </div>
                            <div class="col-md-10">
                                <component-summernote
                                    name="html"
                                    disableresize
                                    required
                                    value="{{old('html')}}"
                                    route_upload_image="{{route('api.upload_image')}}"
                                >
                                </component-summernote>
                                <small class="form-text text-muted">Descreva de maneira completa aqui seu ticket</small>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-primary float-right" type="submit">Enviar Ticket</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
