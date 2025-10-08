<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<title>Time Tracking</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('reports.summary') }}">Time Tracking</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        @auth
        <li class="nav-item"><a class="nav-link" href="{{ route('employees.index') }}">Сотрудники</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('logout') }}">Выход</a></li>
        @endauth
      </ul>
    </div>
  </div>
</nav>
<div class="container py-4">@yield('content')</div>
</body>
</html>
