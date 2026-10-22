@extends('base')
@section('main')
<div class="row">
    <div class="col-sm-12">
    <h1 class="display-5">Receipt</h1>
        <div>
        <a style="margin: 19px;" class="btn btn-primary" href="{{ route('receipts.index') }}"> Back</a>
        <a style="margin: 19px;" class="btn btn-primary" href="{{ route('receipts.create')}}">New Receipt</a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <strong>Ref:</strong>
            {{ $receipt->ref }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <strong>Date of Receipt:</strong>
            {{ $receipt->date_of_receipt }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <strong>Invoice ref:</strong>
            {{ $receipt->invoice->ref }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <strong>Client:</strong>
            {{ $receipt->account->head_of_account }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <strong>Description:</strong>
            {{ $receipt->description }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <strong>Cheque amount:</strong>
            {{ \App\Support\AmountFormatter::formatAmount($receipt->amount) }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <strong>Income tax withheld:</strong>
            {{ \App\Support\AmountFormatter::formatAmount($receipt->itax) }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <strong>Sales tax withheld:</strong>
            {{ \App\Support\AmountFormatter::formatAmount($receipt->stax) }}
        </div>
    </div>

</div>
@endsection
