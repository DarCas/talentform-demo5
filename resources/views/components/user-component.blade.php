@props(['user'])
<div class="btn-group-lg">
    <button type="button" class="btn btn-info dropdown-toggle"
            data-bs-toggle="dropdown" aria-expanded="false">
        {{ $user['usernm'] }}
    </button>
    <ul class="dropdown-menu bg-danger dropdown-menu-end shadow">
        <li>
            <a class="dropdown-item bg-danger text-white"
               href="/login/logout">Logout</a>
        </li>
    </ul>
</div>
