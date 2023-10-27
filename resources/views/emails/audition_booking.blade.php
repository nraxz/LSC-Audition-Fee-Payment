<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Booking confirm</title>
   
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://bootswatch.com//5/journal/bootstrap.css">
    <link rel="stylesheet" href="https://bootswatch.com//_vendor/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://bootswatch.com//_vendor/prismjs/themes/prism-okaidia.css">
    <link rel="stylesheet" href="https://bootswatch.com//_assets/css/custom.min.css">
</head>
<body>
   <div class="container">
      <div class="card border-success mb-3">
         <div class="card-header">{{ $application->subject }}</div>
         <div class="card-body">
               <h4 class="card-title">Dear {{ $application->name }}</h4>
               <p class="card-text"> {!! $application->content !!}</p>
         </div>
      </div>
   </div>
</body>
</html>