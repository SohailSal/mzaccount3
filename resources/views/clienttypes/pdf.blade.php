<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
<div class="row">
    <div class="col-sm-12">
    <h1 class="display-5">Type of Client</h1>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <strong>ID:</strong>
            {{ $clienttype->id }}
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <strong>Type of Client:</strong>
            {{ $clienttype->type_of_client }}
        </div>
    </div>

</div>
  </body>
  </html>
