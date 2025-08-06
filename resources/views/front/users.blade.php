<div class="row">
    <div class="col-md-8">
        @include('front.users.datagrid', ['me' => $me, 'pagination' => $pagination, 'users' => $users, 'id' => $user?->id ?? null])
    </div>
    <div class="col-md-4">
        @include('front.users.form', ['errors' => $errors, 'user' => $user])
    </div>
</div>
