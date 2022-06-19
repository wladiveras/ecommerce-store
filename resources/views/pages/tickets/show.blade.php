@extends('template.simple')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <h4 class="col-md-6 col-sm-12 d-flex align-items-center">
                        <i class="material-icons mr-2">search</i>
                        <span>Detalhe do Ticket #{{str_pad($ticket->id, 6, "0", STR_PAD_LEFT)}}</span>
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
                                <input type="text" class="form-control" value="{{$ticket->subject}}" disabled readonly>
                                <small class="form-text text-muted">Aqui a descrição breve seu ticket</small>
                            </div>
                        </div>
                        <div class="row d-flex align-items-center  mb-3">
                            <div class="col-md-2">
                                <b>Descrição :</b>
                            </div>
                            <div class="col-md-10">
                                <component-summernote
                                    disabled
                                    name="html"
                                    disableresize
                                    required
                                    value="{{ $ticket->html }}"
                                >
                                </component-summernote>
                                <small class="form-text text-muted">Aqui a descrição completa do seu ticket</small>
                            </div>
                        </div>
                        <div class="row d-flex align-items-center mb-3">
                            <div class="col-md-2">
                                <b>Status :</b>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control" value="{{$ticket->completed_at ? "Finalizado" : $ticket->status_name}}" disabled readonly>
                                <small class="form-text text-muted">Status do Ticket</small>
                            </div>
                        </div>
                        <div class="row d-flex align-items-center mb-3">
                            <div class="col-md-2">
                                <b>Categoria :</b>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control" value="{{$ticket->category_name}}" disabled readonly>
                                <small class="form-text text-muted">Categoria do Ticket</small>
                            </div>
                        </div>
                        <div class="row d-flex align-items-center mb-5">
                            <div class="col-md-2">
                                <b>Prioridade :</b>
                            </div>
                            <div class="col-md-10">
                                <input type="text" class="form-control" value="{{$ticket->priority_name}}" disabled readonly>
                                <small class="form-text text-muted">Prioridade do Ticket</small>
                            </div>
                        </div>
                        <hr>
                        @if($comments->count()>0)
                            <h1 class="text-center mt-5 mb-5">Comentários</h1>
                            @foreach($comments as $comment)
                                <div  @if($comment->user_id!=$user_id) class="row d-flex align-items-center mb-3" @else class="row d-flex align-items-center mb-3 comment-answer" @endif>
                                    @if($comment->user_id==$user_id)<div class="col-md-2"></div>@endif
                                    <div class="col-md-10 col-sm-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-12">
                                                        <b class="float-left">{{$comment->user_name}}</b>
                                                    </div>
                                                    <div class="col-md-6 col-sm-12">
                                                        <span class="float-right">{{\Carbon::createFromDate($comment->updated_at)->diffForHumans()}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                {!!$comment->html!!}
                                            </div>
                                        </div>
                                    </div>
                                    @if($comment->user_id!=$user_id)<div class="col-md-2"></div>@endif
                                </div>
                            @endforeach
                        @else
                            <h1 class="text-center">Comentários</h1>
                            <small class="form-text text-muted text-center">Sem Comentários</small>
                        @endif
                        @if(!$ticket->completed_at)
                            <form method="POST" action="{{route('tickets.new_comment',['id'=>$ticket->id])}}">
                                @csrf                            
                                <div class="row d-flex align-items-center  mb-3">
                                    <div class="col-md-12">
                                        <component-summernote
                                            name="comment"
                                            disableresize
                                            required
                                            value="{{old('comment')}}"
                                            route_upload_image="{{route('api.upload_image')}}"
                                        >
                                        </component-summernote>
                                        <small class="form-text text-muted">Adicione aqui um novo comentario</small>
                                    </div>
                                </div>
                                <div class="row d-flex align-items-center  mb-3">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-secondary">Comentar</button>
                                    </div>
                                </div>
                            </form>
                        @endif

                    </div>

                </div>
            </div>


        </div>
    </div>
</div>

@endsection
