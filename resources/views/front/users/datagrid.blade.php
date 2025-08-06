<div class="card shadow py-2">
    <div class="card-body p-0">
        <div class="card-text">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col" class="text-end">#</th>
                    <th scope="col">Username</th>
                    <th scope="col" class="text-end">Ultimo accesso</th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="4" class="px-4 pt-4">
                        {!! $pagination !!}
                    </td>
                </tr>
                </tfoot>

                <tbody>
                @forelse($users as $user)
                    <tr
                        class="{{ $id === $user->id ? 'table-primary' : '' }}"
                        style="vertical-align: middle"
                    >
                        <td class="text-end">{{ $user->id }}</td>
                        <td>{{ $user->usernm }}</td>
                        <td class="text-end">{{ $user->logged_in }}</td>
                        <td class="text-end">
                            <x-menu-dropdown
                                :id="$user->id"
                                :items="$user->id !== $me ?
                                    ['/users?edit=:id' => 'Modifica password', 'divider' => 'divider', '/users/:id/delete' => 'Elimina'] :
                                    ['/users?edit=:id' => 'Modifica password']"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            Non ci sono ancora utenti
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
