<!-- landing view dopo l'autenticazione -->

{{-- view 'index' per visualizzare elenco risultati --}}
{{-- riceve in ingresso la collection di risultati letta dal DB dal metodo 'index', --}}
{{-- del controller 'ResultController' --}}
@extends('layouts.structure')

@section('content')
<div class="container">
     <div class="row">
         <div class="col-12">
             <h1 class="d-inline-block my-5">Tutti i risultati</h1>
         </div>
     </div>
     <div class="row">
         <div class="col-12">
             <table class="table">
                 <thead>
                     <tr>
                         <th>ID</th>
                         <th>Method</th>
                         <th>URL</th>
                         <th>Path</th>
                         <th>Domain</th>
                         <th>Status</th>
                         <th>Date</th>
                     </tr>
                 </thead>
                 <tbody>
                     @forelse ($results as $result)
                     <tr>
                         <td>{{ $result->id }}</td>
                         <td>{{ $result->method }}</td>
                         <td>{{ $result->url }}</td>
                         <td>{{ $result->path }}</td>
                         <td>{{ $result->domain }}</td>
                         <td>{{ $result->status }}</td>
                         <td>{{ $result->date }}</td>
                     </tr>
                     @empty
                     <tr>
                         <td colspan="5">Non c'è alcun risultato</td>
                     </tr>
                     @endforelse
                 </tbody>
             </table>
             {{-- paginazione fatta automaticamente da Laravel --}}
             {{ $results->links() }}
         </div>
     </div>
 </div>
@endsection