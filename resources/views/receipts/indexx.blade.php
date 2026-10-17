@extends('base')
@section('main')
<div class="row">
<div class="col-sm-12">
  @if(session()->get('success'))
    <div class="alert alert-success">
      {{ session()->get('success') }}
    </div>
  @endif
    <h1 class="display-5">Delete Receipts</h1>
    <div>
    <a style="margin: 5px;" href="{{ route('home')}}" class="btn btn-primary">Home</a>
    </div>
    <div>
    {{$receipts->links()}}
    </div>
  <table class="table table-striped">
    <thead>
        <tr>
          <td>Ref</td>
          <td>Date of Receipt</td>
          <td>Head of Account</td>
          <td align='right'>Amount</td>
          <td align='right'>Income Tax withheld</td>
          <td align='right'>Sales Tax withheld</td>
          <td style="text-align:center;">Actions</td>
        </tr>
    </thead>
    <tbody>
        @foreach($receipts as $receipt)
        <tr>
            <td>{{$receipt->ref}}</td>
            <td>{{$receipt->date_of_receipt}}</td>
            <td>{{$receipt->account->head_of_account}}</td>
            <td align='right'>{{ number_format($receipt->amount, 2) }}</td>
            <td align='right'>{{ number_format($receipt->itax, 2) }}</td>
            <td align='right'>{{ number_format($receipt->stax, 2) }}</td>
            <td>
                <form action="{{ route('receipts.destroy', $receipt->id)}}" method="post">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger" onclick="return confirm('Are you sure?')" type="submit">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
  </table>
  <div>
    {{$receipts->links()}}
  </div>
</div>
</div>
@endsection
