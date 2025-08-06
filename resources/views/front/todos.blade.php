<div class="row">
    <div class="col-md-8">
        @include('front.todos.datagrid', ['pagination' => $pagination, 'todos' => $todos, 'id' => $todo?->id ?? null])
    </div>
    <div class="col-md-4">
        @include('front.todos.form', ['errors' => $errors, 'todo' => $todo])
    </div>
</div>
