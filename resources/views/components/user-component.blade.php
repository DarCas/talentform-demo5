@props(['user'])
<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    {{ $user['usernm'] }}
</a>
<ul class="dropdown-menu">
    <li><a class="dropdown-item" href="/login/logout">Logout</a></li>
</ul>
