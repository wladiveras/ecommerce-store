@extends('template.simple')
@section('content')
<tickets-filter :request="{{json_encode($filter)}}"
                :categories="{{json_encode($categories)}}"
                :status="{{json_encode($status)}}"
                :priorities="{{json_encode($priorities)}}"
                create="{{route('tickets.create')}}"
></tickets-filter>  
<resource-table :filter="{{json_encode($filter)}}" route="{{route('tickets.search')}}" perpage="15">
    <template slot="table">
        <table id="table" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th style="width:1%;">#</th>
                    <th>Assunto</th>
                    <th>Ultima Atualização</th>
                    <th>Status</th>
                    <th>Prioridade</th>
                    <th>Categoria</th>
                    <th style="width:1%;" class="no-sort"></th>
                </tr>
            </thead>
        </table>
    </template>
</resource-table>
@endsection
